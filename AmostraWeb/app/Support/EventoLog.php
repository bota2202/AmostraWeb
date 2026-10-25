<?php

namespace App\Support;

use App\Models\Amostra;
use App\Models\Cliente;
use App\Models\Estabelecimento;
use App\Models\Grupo;
use App\Models\Log;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Solicitacao_Prazo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Uma ação feita no sistema, montada a partir dos registros de log de um mesmo lote
 * (ex.: editar um cliente e mudar 3 campos = 1 ação com 3 alterações).
 *
 * Serve para as telas de log mostrarem frases legíveis:
 *   "Ana Ribeiro alterou o cliente Vale LTDA" · Status: Ativo → Inativo
 */
class EventoLog
{
    /**
     * Tipo de registro => [gênero, nome, classe, colunas do nome, rota do detalhe, módulo da permissão]
     */
    private const REGISTROS = [
        'Amostra' => ['a', 'amostra', Amostra::class, ['codigo'], 'amostras.show', 'amostras'],
        'Pedido' => ['o', 'pedido', Pedido::class, ['codigo'], 'pedidos.show', 'pedidos'],
        'Cliente' => ['o', 'cliente', Cliente::class, ['razao_social'], 'clientes.show', 'clientes'],
        'Produto' => ['o', 'produto', Produto::class, ['codigo_produto', 'descricao'], 'produtos.show', 'produtos'],
        'Estabelecimento' => ['o', 'estabelecimento', Estabelecimento::class, ['razao_social'], 'estabelecimentos.show', 'estabelecimentos'],
        'User' => ['o', 'usuário', User::class, ['name'], 'usuarios.show', 'usuarios'],
        'Grupo' => ['o', 'grupo', Grupo::class, ['nome'], 'grupos.show', 'permissoes'],
        'Solicitacao_Prazo' => ['a', 'solicitação de prazo', Solicitacao_Prazo::class, [], 'amostras.show', 'amostras'],
    ];

    /**
     * Ação => [categoria (cor), ícone]
     */
    private const VISUAL = [
        'criar' => ['criacao', 'fa-plus'],
        'editar' => ['alteracao', 'fa-pen'],
        'alterar_status' => ['status', 'fa-arrows-rotate'],
        'excluir' => ['exclusao', 'fa-trash-can'],
        'permissoes' => ['acesso', 'fa-user-shield'],
        'membros' => ['acesso', 'fa-users'],
        'importar' => ['importacao', 'fa-file-import'],
        'login' => ['sessao', 'fa-right-to-bracket'],
        'logout' => ['sessao', 'fa-right-from-bracket'],
        'login_falhou' => ['exclusao', 'fa-user-lock'],
        'senha_codigo' => ['sessao', 'fa-envelope'],
        'senha_redefinida' => ['status', 'fa-key'],
    ];

    /**
     * Grupos de ações usados no filtro "Tipo de ação".
     */
    public const TIPOS = [
        'cadastros' => ['Cadastros', ['criar']],
        'alteracoes' => ['Alterações', ['editar', 'alterar_status', 'permissoes', 'membros']],
        'exclusoes' => ['Exclusões', ['excluir']],
        'acessos' => ['Entradas e senhas', ['login', 'logout', 'login_falhou', 'senha_codigo', 'senha_redefinida']],
        'importacoes' => ['Importações do ERP', ['importar']],
    ];

    // ordem para escolher a ação principal de um lote
    private const PRIORIDADE = ['excluir', 'criar', 'alterar_status', 'permissoes', 'membros', 'editar'];

    // chaves estrangeiras mostradas pelo nome
    private const RELACOES = [
        'cliente_id' => [Cliente::class, 'razao_social'],
        'produto_id' => [Produto::class, 'descricao'],
        'pedido_id' => [Pedido::class, 'codigo'],
        'amostra_id' => [Amostra::class, 'codigo'],
        'user_id' => [User::class, 'name'],
        'solicitante_id' => [User::class, 'name'],
        'aprovador_id' => [User::class, 'name'],
        'gestor_id' => [User::class, 'name'],
        'estabelecimento_id' => [Estabelecimento::class, 'razao_social'],
    ];

    // campos que não aparecem nos dados de criação / exclusão
    private const CAMPOS_OCULTOS = ['id', 'lote', 'created_at', 'updated_at', 'email_verified_at', 'remember_token', 'password'];

    /** @var Collection<int, Log> */
    public Collection $registros;

    public Log $principal;

    public ?User $usuario;

    public Carbon $quando;

    // registro afetado (null = não existe mais ou não se aplica)
    private ?Model $registro = null;

    private ?array $retratoEmCache = null;

    /** @var array<string, string|null> */
    private static array $nomesRelacionados = [];

    public function __construct(Collection $registros)
    {
        $this->registros = $registros->sortBy('id')->values();

        $this->principal = collect(self::PRIORIDADE)
            ->map(fn ($acao) => $this->registros->firstWhere('acao', $acao))
            ->filter()
            ->first() ?? $this->registros->first();

        $this->usuario = $this->principal->user;
        $this->quando = $this->principal->data_hora;
    }

    /**
     * Monta as ações de uma lista de logs (agrupando pelo lote).
     *
     * @return Collection<int, self>
     */
    public static function deLogs(Collection $logs): Collection
    {
        $eventos = $logs->groupBy(fn (Log $log) => $log->lote ?? 'id'.$log->id)
            ->map(fn (Collection $grupo) => new self($grupo))
            ->values();

        self::carregarRegistros($eventos);

        return $eventos;
    }

    /**
     * Busca de uma vez os registros afetados (uma consulta por tipo).
     *
     * @param iterable<self> $eventos
     */
    public static function carregarRegistros(iterable $eventos): void
    {
        $eventos = collect($eventos);

        $eventos->groupBy(fn (self $e) => $e->principal->tipo_registro)->each(function (Collection $doTipo, string $tipo) {
            $classe = self::REGISTROS[$tipo][2] ?? null;
            if (! $classe) {
                return;
            }

            $query = $classe::query()->whereKey($doTipo->map(fn (self $e) => $e->principal->registro_id)->unique()->all());
            if ($tipo === 'Solicitacao_Prazo') {
                $query->with('amostra:id,codigo');
            }
            $encontrados = $query->get()->keyBy('id');

            $doTipo->each(fn (self $e) => $e->registro = $encontrados->get($e->principal->registro_id));
        });
    }

    /* ------------------------------------------------------------------
     | Identificação
     * ------------------------------------------------------------------ */

    public function id(): int
    {
        return $this->principal->id;
    }

    public function acao(): string
    {
        return $this->principal->acao;
    }

    public function categoria(): string
    {
        return 'cat-'.(self::VISUAL[$this->acao()][0] ?? 'alteracao');
    }

    public function icone(): string
    {
        return self::VISUAL[$this->acao()][1] ?? 'fa-circle-info';
    }

    public function acaoNome(): string
    {
        return $this->principal->acao_nome;
    }

    public function moduloNome(): string
    {
        return $this->principal->modulo_nome;
    }

    public function autor(): string
    {
        if ($this->usuario) {
            return $this->usuario->name;
        }

        // acesso negado: quem tentou entrar (quando o código existe)
        if ($this->acao() === 'login_falhou') {
            return $this->registro?->name ?? 'Alguém';
        }

        return 'Sistema';
    }

    public function feitoPeloSistema(): bool
    {
        return ! $this->usuario && $this->acao() !== 'login_falhou';
    }

    /* ------------------------------------------------------------------
     | Registro afetado
     * ------------------------------------------------------------------ */

    public function temRegistro(): bool
    {
        return isset(self::REGISTROS[$this->principal->tipo_registro]) && $this->principal->registro_id;
    }

    /**
     * Ex.: "Vale LTDA", "000015", "123 - Luva de raspa". Usa o retrato gravado no log
     * quando o registro já foi excluído.
     */
    public function nomeRegistro(): ?string
    {
        if (! $this->temRegistro()) {
            return null;
        }

        $tipo = $this->principal->tipo_registro;

        if ($tipo === 'Solicitacao_Prazo') {
            $codigo = $this->registro?->amostra?->codigo
                ?? self::nomeRelacionado('amostra_id', $this->retrato()['amostra_id'] ?? null);

            return $codigo ? 'amostra '.$codigo : '#'.$this->principal->registro_id;
        }

        $colunas = self::REGISTROS[$tipo][3];
        $fonte = $this->registro ? $this->registro->getAttributes() : $this->retrato();

        $nome = collect($colunas)->map(fn ($c) => $fonte[$c] ?? null)->filter(fn ($v) => $v !== null && $v !== '')->implode(' - ');

        return $nome !== '' ? $nome : '#'.$this->principal->registro_id;
    }

    /**
     * "o cliente", "a amostra"... com preposição opcional: "do cliente", "da amostra".
     */
    public function tipoRegistroTexto(?string $preposicao = null): string
    {
        [$genero, $nome] = self::REGISTROS[$this->principal->tipo_registro];

        $artigo = match ($preposicao) {
            'de' => 'd'.$genero,
            'em' => 'n'.$genero,
            default => $genero,
        };

        return $artigo.' '.$nome;
    }

    public function registroExcluido(): bool
    {
        return $this->temRegistro() && ! $this->registro;
    }

    /**
     * Link para o registro, se ele existe e quem está vendo tem acesso.
     */
    public function urlRegistro(?User $quemVe): ?string
    {
        if (! $this->registro || ! $quemVe) {
            return null;
        }

        [, , , , $rota, $modulo] = self::REGISTROS[$this->principal->tipo_registro];

        if (! $quemVe->temPermissao($modulo)) {
            return null;
        }

        return $this->principal->tipo_registro === 'Solicitacao_Prazo'
            ? ($this->registro->amostra ? route($rota, $this->registro->amostra) : null)
            : route($rota, $this->registro);
    }

    /**
     * Filtro da lista de logs com tudo o que aconteceu com este registro.
     */
    public function filtroRegistro(): ?string
    {
        return $this->temRegistro() ? $this->principal->tipo_registro.'-'.$this->principal->registro_id : null;
    }

    /* ------------------------------------------------------------------
     | Frase: [autor] [texto] [destaque] [complemento]
     * ------------------------------------------------------------------ */

    /**
     * @return array{texto: string, destaque: ?string, complemento: ?string}
     */
    public function frase(): array
    {
        $acao = $this->acao();
        $tipo = $this->principal->tipo_registro;
        $nome = $this->nomeRegistro();
        $frase = fn (string $texto, ?string $destaque = null, ?string $complemento = null) => compact('texto', 'destaque', 'complemento');

        switch ($acao) {
            case 'login':
                return $frase('entrou no sistema');
            case 'logout':
                return $frase('saiu do sistema');
            case 'login_falhou':
                return $frase('tentou entrar no sistema, mas o acesso foi negado');
            case 'senha_codigo':
                return $frase('pediu um código para redefinir a senha');
            case 'senha_redefinida':
                return $frase('redefiniu a senha com o código enviado por e-mail');
            case 'importar':
                return $frase('importou', Str::lower($this->moduloNome()), 'do ERP');
        }

        if (! $this->temRegistro()) {
            return $frase(Str::lower($this->acaoNome()));
        }

        // solicitação de prazo: pedido e decisão
        if ($tipo === 'Solicitacao_Prazo') {
            $situacao = $this->registros->firstWhere('campo_alterado', 'situacao')?->valor_novo;

            return match (true) {
                $acao === 'criar' => $frase('pediu um novo prazo para a', $nome),
                $acao === 'excluir' => $frase('cancelou o pedido de novo prazo da', $nome),
                $situacao === 'aprovada' => $frase('aprovou o novo prazo da', $nome),
                $situacao === 'reprovada' => $frase('reprovou o novo prazo da', $nome),
                default => $frase('alterou a solicitação de prazo da', $nome),
            };
        }

        if ($acao === 'alterar_status') {
            $status = $this->registros->firstWhere('campo_alterado', 'status');
            $novo = $status?->valor_novo;

            if ($tipo === 'Amostra') {
                return match ($novo) {
                    'aprovada' => $frase('registrou a aprovação da amostra', $nome),
                    'reprovada' => $frase('registrou a reprovação da amostra', $nome),
                    'cancelada' => $frase('cancelou a amostra', $nome),
                    'atrasada' => $frase('marcou como atrasada a amostra', $nome, '(prazo vencido)'),
                    default => $frase('mudou o status da amostra', $nome, 'para '.$this->formatar('status', $novo)),
                };
            }

            if (in_array($novo, ['1', 'Sim', 'true'], true)) {
                return $frase('ativou '.$this->tipoRegistroTexto(), $nome);
            }
            if (in_array($novo, ['0', 'Não', 'false'], true)) {
                return $frase('inativou '.$this->tipoRegistroTexto(), $nome);
            }

            return $frase('mudou o status '.$this->tipoRegistroTexto('de'), $nome, 'para '.$this->formatar('status', $novo));
        }

        if ($acao === 'excluir') {
            return $frase('excluiu '.$this->tipoRegistroTexto(), $nome);
        }

        if ($acao === 'criar') {
            return $frase('cadastrou '.$this->tipoRegistroTexto(), $nome);
        }

        // edições
        $campos = $this->registros->pluck('campo_alterado')->filter()->unique()->values();

        if ($campos->all() === ['password']) {
            return $frase('alterou a senha '.$this->tipoRegistroTexto('de'), $nome);
        }
        if ($campos->all() === ['permissoes']) {
            return $frase('alterou as permissões '.$this->tipoRegistroTexto('de'), $nome);
        }
        if ($campos->all() === ['membros']) {
            return $frase('alterou os membros '.$this->tipoRegistroTexto('de'), $nome);
        }

        return $frase('alterou '.$this->tipoRegistroTexto(), $nome);
    }

    /* ------------------------------------------------------------------
     | Detalhes
     * ------------------------------------------------------------------ */

    /**
     * Campos alterados (sem as listas de membros/permissões).
     *
     * @return Collection<int, array{campo: string, antes: ?string, depois: ?string}>
     */
    public function alteracoes(): Collection
    {
        return $this->registros
            ->filter(fn (Log $l) => $l->campo_alterado && ! in_array($l->campo_alterado, ['membros', 'permissoes'], true))
            ->filter(fn (Log $l) => in_array($l->acao, ['editar', 'alterar_status', 'senha_redefinida'], true))
            ->map(fn (Log $l) => [
                'campo' => $l->campo_nome,
                'antes' => $this->formatar($l->campo_alterado, $l->valor_anterior),
                'depois' => $this->formatar($l->campo_alterado, $l->valor_novo),
            ])
            ->values();
    }

    /**
     * Listas alteradas (membros e permissões de um grupo).
     *
     * @return Collection<int, array{campo: string, incluidos: array<string>, retirados: array<string>}>
     */
    public function listas(): Collection
    {
        $itens = fn (?string $valor) => $valor === null || $valor === '(nenhum)' ? [] : explode("\n", $valor);

        return $this->registros
            ->filter(fn (Log $l) => in_array($l->campo_alterado, ['membros', 'permissoes'], true))
            ->map(fn (Log $l) => [
                'campo' => $l->campo_nome,
                'incluidos' => array_values(array_diff($itens($l->valor_novo), $itens($l->valor_anterior))),
                'retirados' => array_values(array_diff($itens($l->valor_anterior), $itens($l->valor_novo))),
            ])
            ->values();
    }

    /**
     * Dados gravados na criação, na exclusão ou na importação: [nome do campo => valor].
     *
     * @return array<string, string>
     */
    public function dados(): array
    {
        if ($this->acao() === 'importar') {
            $resultado = json_decode((string) $this->principal->valor_novo, true) ?: [];
            $nomes = ['total' => 'Linhas no arquivo', 'criados' => 'Criados', 'atualizados' => 'Atualizados', 'sem_alteracao' => 'Sem alteração', 'com_erro' => 'Com erro'];

            return collect($nomes)->filter(fn ($n, $chave) => isset($resultado[$chave]))
                ->mapWithKeys(fn ($n, $chave) => [$n => (string) $resultado[$chave]])
                ->all();
        }

        if (! in_array($this->acao(), ['criar', 'excluir'], true)) {
            return [];
        }

        return collect($this->retrato())
            ->except(self::CAMPOS_OCULTOS)
            ->map(fn ($valor, $campo) => $this->formatar($campo, is_scalar($valor) ? (string) $valor : json_encode($valor, JSON_UNESCAPED_UNICODE)))
            ->filter(fn ($valor) => $valor !== null)
            ->mapWithKeys(fn ($valor, $campo) => [Log::CAMPOS[$campo] ?? ucfirst(str_replace('_', ' ', $campo)) => $valor])
            ->all();
    }

    /**
     * Resumo curto para a lista: até 3 alterações ou a observação.
     *
     * @return array{itens: array<string>, mais: int, texto: ?string}
     */
    public function resumo(): array
    {
        // senha não tem valor para mostrar; campo que estava vazio mostra só o valor novo
        $alteracoes = $this->alteracoes()->reject(fn ($a) => $a['antes'] === '••••••');
        $itens = $alteracoes->take(3)->map(fn ($a) => $a['campo'].': '.($a['antes'] === null
            ? Str::limit($a['depois'] ?? '(vazio)', 60)
            : Str::limit($a['antes'], 40).' → '.Str::limit($a['depois'] ?? '(vazio)', 40)))->values()->all();

        foreach ($this->listas() as $lista) {
            if ($lista['incluidos']) {
                $itens[] = $lista['campo'].': +'.count($lista['incluidos']);
            }
            if ($lista['retirados']) {
                $itens[] = $lista['campo'].': −'.count($lista['retirados']);
            }
        }

        $texto = $this->observacao();
        if (in_array($this->acao(), ['login', 'logout'], true)) {
            $texto = null;
        }

        return ['itens' => $itens, 'mais' => max(0, $alteracoes->count() - 3), 'texto' => $itens ? null : ($texto ? Str::limit($texto, 140) : null)];
    }

    public function observacao(): ?string
    {
        $obs = $this->registros->pluck('observacao')->filter()->unique()
            ->reject(fn ($o) => in_array($o, ['Registro criado', 'Registro excluído', 'Senha alterada'], true) || str_starts_with($o, 'IP '))
            ->first();

        // as listas já aparecem detalhadas
        return $this->listas()->isNotEmpty() && $obs && (str_starts_with($obs, 'Incluídos') || str_starts_with($obs, 'Retirados')) ? null : $obs;
    }

    /* ------------------------------------------------------------------
     | Origem
     * ------------------------------------------------------------------ */

    public function ip(): ?string
    {
        return $this->principal->ip;
    }

    public function navegadorUserAgent(): ?string
    {
        return $this->principal->navegador;
    }

    /**
     * Ex.: "Chrome 141 no Windows".
     */
    public function navegador(): ?string
    {
        $ua = $this->principal->navegador;
        if (! $ua) {
            return null;
        }

        $navegador = match (true) {
            (bool) preg_match('/Edg\/(\d+)/', $ua, $m) => 'Edge '.$m[1],
            (bool) preg_match('/OPR\/(\d+)/', $ua, $m) => 'Opera '.$m[1],
            (bool) preg_match('/Firefox\/(\d+)/', $ua, $m) => 'Firefox '.$m[1],
            (bool) preg_match('/Chrome\/(\d+)/', $ua, $m) => 'Chrome '.$m[1],
            (bool) preg_match('/Version\/(\d+).*Safari/', $ua, $m) => 'Safari '.$m[1],
            default => null,
        };

        $sistema = match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => null,
        };

        if (! $navegador && ! $sistema) {
            return Str::limit($ua, 60);
        }

        return trim(($navegador ?? 'Navegador').($sistema ? ' no '.$sistema : ''));
    }

    /* ------------------------------------------------------------------
     | Valores legíveis
     * ------------------------------------------------------------------ */

    public function formatar(?string $campo, ?string $valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $tipo = $this->principal->tipo_registro;

        if ($campo === 'status') {
            return match (true) {
                $tipo === 'Amostra' => Amostra::STATUS[$valor] ?? $valor,
                $tipo === 'Pedido' => Pedido::STATUS[$valor] ?? $valor,
                in_array($valor, ['1', 'Sim', 'true'], true) => 'Ativo',
                in_array($valor, ['0', 'Não', 'false'], true) => 'Inativo',
                default => $valor,
            };
        }

        if ($campo === 'situacao') {
            return Solicitacao_Prazo::SITUACOES[$valor] ?? $valor;
        }

        if ($campo === 'cargo') {
            return User::CARGOS[$valor] ?? $valor;
        }

        if (isset(self::RELACOES[$campo]) && ctype_digit($valor)) {
            return self::nomeRelacionado($campo, $valor) ?? '#'.$valor;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}( 00:00:00)?$/', $valor)) {
            return Carbon::parse($valor)->format('d/m/Y');
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/', $valor)) {
            return Carbon::parse($valor)->format('d/m/Y H:i');
        }

        return $valor;
    }

    private static function nomeRelacionado(string $campo, mixed $id): ?string
    {
        if ($id === null || ! isset(self::RELACOES[$campo])) {
            return null;
        }

        [$classe, $coluna] = self::RELACOES[$campo];
        $chave = $classe.'#'.$id;

        if (! array_key_exists($chave, self::$nomesRelacionados)) {
            self::$nomesRelacionados[$chave] = $classe::whereKey($id)->value($coluna);
        }

        return self::$nomesRelacionados[$chave];
    }

    /**
     * Dados do registro gravados no log (na criação ou na exclusão).
     *
     * @return array<string, mixed>
     */
    private function retrato(): array
    {
        return $this->retratoEmCache ??= $this->buscarRetrato();
    }

    private function buscarRetrato(): array
    {
        foreach ($this->registros as $log) {
            if (in_array($log->acao, ['criar', 'excluir'], true)) {
                $dados = json_decode((string) ($log->acao === 'criar' ? $log->valor_novo : $log->valor_anterior), true);
                if (is_array($dados)) {
                    return $dados;
                }
            }
        }

        // procura no histórico do registro (ex.: alteração de um registro já excluído)
        if ($this->temRegistro()) {
            $log = Log::where('tipo_registro', $this->principal->tipo_registro)
                ->where('registro_id', $this->principal->registro_id)
                ->whereIn('acao', ['criar', 'excluir'])
                ->latest('id')
                ->first();

            $dados = $log ? json_decode((string) ($log->acao === 'criar' ? $log->valor_novo : $log->valor_anterior), true) : null;

            return is_array($dados) ? $dados : [];
        }

        return [];
    }
}
