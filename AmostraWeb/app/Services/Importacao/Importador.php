<?php

namespace App\Services\Importacao;

use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Base da importação de arquivos CSV exportados pelo ERP (RN006 / RN025).
 *
 * - Detecta o separador (; , ou tab) e a codificação (UTF-8 ou Windows-1252).
 * - Reconhece as colunas pelo nome, ignorando acentos, pontuação e maiúsculas.
 * - Cria registros novos e atualiza os existentes (pelo código).
 * - Gera um relatório por linha e um único registro de auditoria.
 */
abstract class Importador
{
    public const MAX_LINHAS = 20000;
    private const MAX_ERROS_LISTADOS = 100;

    protected array $resultado = [
        'criados' => 0,
        'atualizados' => 0,
        'sem_alteracao' => 0,
        'com_erro' => 0,
        'erros' => [],
    ];

    /** Nome do módulo (auditoria / permissões). */
    abstract public function modulo(): string;

    /** Colunas (normalizadas) que precisam existir no arquivo. */
    abstract protected function colunasObrigatorias(): array;

    /**
     * Cabeçalho e uma linha de exemplo para o arquivo-modelo.
     *
     * @return array{0: array<string>, 1: array<string>}
     */
    abstract public function modelo(): array;

    /**
     * Converte uma linha do arquivo (colunas normalizadas) nos campos do model
     * e salva. Retorna 'criado', 'atualizado' ou 'sem_alteracao'.
     *
     * @throws ErroDeLinha quando a linha tem dados inválidos
     */
    abstract protected function importarLinha(array $linha): string;

    /**
     * @return array{criados:int, atualizados:int, sem_alteracao:int, com_erro:int, erros:array, total:int}
     */
    public function importar(string $caminho, string $nomeArquivo): array
    {
        [$cabecalho, $linhas] = $this->ler($caminho);

        $faltando = array_diff($this->colunasObrigatorias(), $cabecalho);
        if ($faltando) {
            throw new RuntimeException('O arquivo não tem as colunas obrigatórias: '.implode(', ', $faltando).'. Baixe o arquivo-modelo para conferir o formato.');
        }

        Log::semAuditoriaAutomatica(function () use ($linhas) {
            DB::transaction(function () use ($linhas) {
                foreach ($linhas as $numero => $linha) {
                    try {
                        $situacao = $this->importarLinha($linha);
                        $this->resultado[match ($situacao) {
                            'criado' => 'criados',
                            'atualizado' => 'atualizados',
                            default => 'sem_alteracao',
                        }]++;
                    } catch (ErroDeLinha $e) {
                        $this->resultado['com_erro']++;
                        if (count($this->resultado['erros']) < self::MAX_ERROS_LISTADOS) {
                            $this->resultado['erros'][] = ['linha' => $numero, 'mensagem' => $e->getMessage()];
                        }
                    }
                }
            });
        });

        $this->resultado['total'] = count($linhas);

        // um registro de resumo na auditoria (RN015)
        Log::registrar(
            null,
            'importar',
            valorNovo: json_encode(array_diff_key($this->resultado, ['erros' => true]), JSON_UNESCAPED_UNICODE),
            observacao: 'Arquivo '.$nomeArquivo.': '.$this->resultado['criados'].' criado(s), '
                .$this->resultado['atualizados'].' atualizado(s), '
                .$this->resultado['sem_alteracao'].' sem alteração, '
                .$this->resultado['com_erro'].' com erro.',
            modulo: $this->modulo(),
        );

        return $this->resultado;
    }

    /* ------------------------------------------------------------------
     | Leitura do arquivo
     * ------------------------------------------------------------------ */

    /**
     * @return array{0: array<string>, 1: array<int, array<string, string>>} cabeçalho e linhas (numeradas como no arquivo)
     */
    protected function ler(string $caminho): array
    {
        $conteudo = file_get_contents($caminho);
        if ($conteudo === false || trim($conteudo) === '') {
            throw new RuntimeException('O arquivo está vazio.');
        }

        // remove BOM e converte de Windows-1252 (Excel) para UTF-8 quando preciso
        $conteudo = preg_replace('/^\xEF\xBB\xBF/', '', $conteudo);
        if (! mb_check_encoding($conteudo, 'UTF-8')) {
            $conteudo = mb_convert_encoding($conteudo, 'UTF-8', 'Windows-1252');
        }

        $primeiraLinha = strtok($conteudo, "\r\n");
        $separador = collect([';', ',', "\t"])
            ->sortByDesc(fn ($s) => substr_count($primeiraLinha, $s))
            ->first();

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $conteudo);
        rewind($stream);

        $cabecalho = array_map([$this, 'normalizarColuna'], fgetcsv($stream, null, $separador, '"', '') ?: []);
        $linhas = [];
        $numero = 1;

        while (($valores = fgetcsv($stream, null, $separador, '"', '')) !== false) {
            $numero++;

            if (count(array_filter($valores, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // linha em branco
            }

            if (count($linhas) >= self::MAX_LINHAS) {
                throw new RuntimeException('O arquivo tem mais de '.self::MAX_LINHAS.' linhas. Divida em arquivos menores.');
            }

            $valores = array_pad(array_slice($valores, 0, count($cabecalho)), count($cabecalho), '');
            $linhas[$numero] = array_combine($cabecalho, array_map(fn ($v) => trim((string) $v), $valores));
        }

        fclose($stream);

        return [$cabecalho, $linhas];
    }

    /**
     * "Cód. Sub-Família" => "cod sub familia"
     */
    public function normalizarColuna(?string $nome): string
    {
        $nome = Str::lower(Str::ascii((string) $nome));
        $nome = preg_replace('/[^a-z0-9]+/', ' ', $nome);

        return trim($nome);
    }

    /* ------------------------------------------------------------------
     | Ajudas para as linhas
     * ------------------------------------------------------------------ */

    /**
     * Primeiro valor preenchido entre as colunas informadas.
     */
    protected function valor(array $linha, string ...$colunas): ?string
    {
        foreach ($colunas as $coluna) {
            $valor = $linha[$coluna] ?? '';
            if ($valor !== '') {
                return $valor;
            }
        }

        return null;
    }

    /**
     * Junta código e descrição do ERP: "080" + "MATERIA PRIMA" => "080 - MATERIA PRIMA".
     */
    protected function codigoEDescricao(array $linha, string $colunaCodigo, string $colunaDescricao): ?string
    {
        $codigo = $this->valor($linha, $colunaCodigo);
        $descricao = $this->valor($linha, $colunaDescricao);

        if ($codigo !== null && $descricao !== null) {
            return Str::limit($codigo.' - '.$descricao, 255, '');
        }

        return $codigo ?? $descricao;
    }

    /**
     * "ATIVO" / "INATIVO" / "S" / "N" / "1" / "0" => bool (padrão: ativo).
     */
    protected function status(?string $valor): bool
    {
        if ($valor === null) {
            return true;
        }

        return ! in_array(Str::upper(Str::ascii($valor)), ['INATIVO', 'INATIVA', 'N', 'NAO', '0', 'FALSE', 'BLOQUEADO'], true);
    }

    /**
     * Datas no formato do Brasil (31/12/2026) ou ISO (2026-12-31).
     */
    protected function data(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        foreach (['d/m/Y', 'd/m/y', 'Y-m-d'] as $formato) {
            $data = \DateTime::createFromFormat('!'.$formato, $valor);
            if ($data !== false) {
                return $data->format('Y-m-d');
            }
        }

        throw new ErroDeLinha('Data inválida: '.$valor.' (use dd/mm/aaaa).');
    }

    /**
     * Salva o model e diz o que aconteceu.
     */
    protected function salvar(\Illuminate\Database\Eloquent\Model $model): string
    {
        if (! $model->exists) {
            $model->save();
            return 'criado';
        }

        if ($model->isDirty()) {
            $model->save();
            return 'atualizado';
        }

        return 'sem_alteracao';
    }
}
