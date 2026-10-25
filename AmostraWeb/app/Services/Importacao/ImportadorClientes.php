<?php

namespace App\Services\Importacao;

use App\Models\Cliente;
use Illuminate\Support\Str;

/**
 * Importa clientes a partir do CSV exportado pelo ERP (RF041).
 * As colunas seguem a planilha Docs/Planilhas/Cliente.xlsx.
 */
class ImportadorClientes extends Importador
{
    public function modulo(): string
    {
        return 'clientes';
    }

    protected function colunasObrigatorias(): array
    {
        return ['cod empresa', 'razao social', 'cnpj cpf'];
    }

    public function modelo(): array
    {
        return [
            [
                'Cód. Empresa', 'Razão Social', 'Nome Fantasia', 'Cnpj/Cpf', 'Status empresa', 'Tipo',
                'DESCCLASSIFEMPRESA', 'Município', 'Estado', 'Sigla', 'País', 'Telefone 1',
                'Grupo fiscal de empresas', 'Descrição do grupo fiscal de empresas',
                'Grupo econômico', 'Descrição grupo econômico', 'Bairro', 'CEP', 'E-mail', 'Número', 'Endereço',
            ],
            [
                '56077', 'MORGANA DARC DE OLIVEIRA RAMOS', 'MORGANA DARC DE OLIVEIRA RAMOS', '118.661.006-96', 'ATIVO', 'CLIENTE',
                'CLIENTE NAO CONTRIBUINTE', 'ABADIA DOS DOURADOS', 'MINAS GERAIS', 'MG', 'BRASIL', '(34) 99999-0000',
                '17', 'CLIENTE PESSOA FISICA',
                '', '', 'CENTRO', '38540000', 'contato@exemplo.com.br', '1170', 'AVENIDA DONA BALDOINA',
            ],
        ];
    }

    protected function importarLinha(array $linha): string
    {
        $codigo = $this->valor($linha, 'cod empresa', 'codigo da empresa', 'codigo empresa');
        $razao = $this->valor($linha, 'razao social');

        if ($codigo === null) {
            throw new ErroDeLinha('Código da empresa em branco.');
        }
        if ($razao === null) {
            throw new ErroDeLinha('Cliente '.$codigo.': razão social em branco.');
        }

        $documento = $this->documento($this->valor($linha, 'cnpj cpf'), $codigo);

        $duplicado = Cliente::where('cnpj_cpf', $documento)->where('codigo_empresa', '!=', $codigo)->value('codigo_empresa');
        if ($duplicado) {
            throw new ErroDeLinha('Cliente '.$codigo.': o CNPJ/CPF '.$documento.' já pertence ao cliente '.$duplicado.'.');
        }

        $email = $this->valor($linha, 'e mail', 'email');

        $cliente = Cliente::firstOrNew(['codigo_empresa' => $codigo]);
        $cliente->fill([
            'razao_social' => Str::limit($razao, 255, ''),
            'nome_fantasia' => ($fantasia = $this->valor($linha, 'nome fantasia')) ? Str::limit($fantasia, 255, '') : null,
            'cnpj_cpf' => $documento,
            'status' => $this->status($this->valor($linha, 'status empresa', 'status')),
            'tipo' => Str::limit($this->valor($linha, 'tipo') ?? 'CLIENTE', 50, ''),
            'tipo_empresa' => ($classificacao = $this->valor($linha, 'descclassifempresa', 'tipo de empresa')) ? Str::limit($classificacao, 50, '') : null,
            'grupo_fiscal' => $this->limitar($this->codigoEDescricao($linha, 'grupo fiscal de empresas', 'descricao do grupo fiscal de empresas'), 50),
            'grupo_economico' => $this->limitar($this->codigoEDescricao($linha, 'grupo economico', 'descricao grupo economico'), 100),
            'telefone' => $this->limitar($this->valor($linha, 'telefone 1', 'telefone'), 20),
            // e-mail inválido no ERP não impede a importação: fica em branco
            'email' => $email && filter_var($email, FILTER_VALIDATE_EMAIL) ? Str::lower($email) : null,
            'endereco_completo' => $this->endereco($linha),
        ]);

        return $this->salvar($cliente);
    }

    /**
     * Formata CPF (11 dígitos) ou CNPJ (14 dígitos).
     */
    private function documento(?string $valor, string $codigo): string
    {
        $digitos = preg_replace('/\D/', '', (string) $valor);

        return match (strlen($digitos)) {
            11 => vsprintf('%s%s%s.%s%s%s.%s%s%s-%s%s', str_split($digitos)),
            14 => vsprintf('%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s', str_split($digitos)),
            default => throw new ErroDeLinha('Cliente '.$codigo.': CNPJ/CPF inválido ('.($valor ?: 'em branco').').'),
        };
    }

    /**
     * "AVENIDA DONA BALDOINA, 1170 - CENTRO, ABADIA DOS DOURADOS/MG - CEP 38540-000"
     */
    private function endereco(array $linha): ?string
    {
        $rua = $this->valor($linha, 'endereco', 'logradouro');
        $numero = $this->valor($linha, 'numero');
        $bairro = $this->valor($linha, 'bairro');
        $cidade = $this->valor($linha, 'municipio', 'cidade');
        $uf = $this->valor($linha, 'sigla', 'uf', 'estado');
        $cep = preg_replace('/\D/', '', (string) $this->valor($linha, 'cep'));

        $partes = array_filter([
            trim(($rua ?? '').($numero && $numero !== '0' ? ', '.$numero : '')),
            $bairro,
            trim(($cidade ?? '').($uf ? '/'.$uf : ''), '/'),
            strlen($cep) === 8 ? 'CEP '.substr($cep, 0, 5).'-'.substr($cep, 5) : null,
        ]);

        return $partes ? Str::limit(implode(' - ', $partes), 500, '') : null;
    }

    private function limitar(?string $valor, int $tamanho): ?string
    {
        return $valor === null ? null : Str::limit($valor, $tamanho, '');
    }
}
