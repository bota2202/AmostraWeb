<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;

/**
 * Registro de auditoria (RN015 / RN020).
 * Somente inclusão: registros não podem ser alterados nem excluídos (RNF005).
 */
class Log extends Model
{
    public const ACOES = [
        'criar' => 'Criação',
        'editar' => 'Alteração',
        'alterar_status' => 'Alteração de status',
        'excluir' => 'Exclusão',
        'permissoes' => 'Alteração de permissões',
        'membros' => 'Alteração de membros',
        'importar' => 'Importação',
        'login' => 'Entrada no sistema',
        'logout' => 'Saída do sistema',
        'login_falhou' => 'Tentativa de acesso negada',
        'senha_codigo' => 'Código de redefinição de senha enviado',
        'senha_redefinida' => 'Senha redefinida',
    ];

    // módulos que não são telas de cadastro
    public const MODULOS_EXTRAS = [
        'autenticacao' => 'Autenticação',
        'grupos' => 'Grupos de permissões',
    ];

    // nomes amigáveis dos campos alterados
    public const CAMPOS = [
        'status' => 'Status',
        'prazo_avaliacao' => 'Prazo de avaliação',
        'quantidade' => 'Quantidade',
        'produto_id' => 'Produto',
        'pedido_id' => 'Pedido',
        'cliente_id' => 'Cliente',
        'user_id' => 'Vendedor responsável',
        'estabelecimento_id' => 'Estabelecimento',
        'data_emissao' => 'Data de envio',
        'observacao' => 'Observação',
        'gestor_id' => 'Gestor',
        'password' => 'Senha',
        'name' => 'Nome',
        'email' => 'E-mail',
        'cargo' => 'Cargo',
        'situacao' => 'Situação',
        'novo_prazo' => 'Novo prazo',
        'aprovador_id' => 'Aprovador',
        'motivo_aprovacao_reprovacao' => 'Motivo',
        'data_aprovacao' => 'Data da decisão',
        'razao_social' => 'Razão social',
        'nome_fantasia' => 'Nome fantasia',
        'descricao' => 'Descrição',
        'codigo' => 'Código',
        'codigo_usuario' => 'Código de usuário',
        'codigo_empresa' => 'Código da empresa',
        'codigo_produto' => 'Código do produto',
        'cnpj' => 'CNPJ',
        'cnpj_cpf' => 'CPF/CNPJ',
        'uf' => 'UF',
        'cidade' => 'Cidade',
        'bairro' => 'Bairro',
        'rua' => 'Rua',
        'numero' => 'Número',
        'cep' => 'CEP',
        'telefone' => 'Telefone',
        'endereco_completo' => 'Endereço',
        'tipo' => 'Tipo',
        'nome' => 'Nome',
        'justificativa' => 'Justificativa',
        'prazo_atual' => 'Prazo atual',
        'data_solicitacao' => 'Data da solicitação',
        'solicitante_id' => 'Solicitante',
        'amostra_id' => 'Amostra',
        'familia' => 'Família',
        'subfamilia' => 'Subfamília',
        'grupo' => 'Grupo',
        'categoria' => 'Categoria',
        'marca' => 'Marca',
        'data_revisao' => 'Data de revisão',
        'membros' => 'Membros',
        'permissoes' => 'Permissões',
    ];

    protected $fillable = [
        'lote',
        'user_id',
        'modulo',
        'tipo_registro',
        'registro_id',
        'acao',
        'campo_alterado',
        'valor_anterior',
        'valor_novo',
        'observacao',
        'data_hora',
        'ip',
        'navegador',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    // registros gravados dentro de emLote() ficam juntos como uma ação só
    private static ?string $loteAtual = null;

    // enquanto true, os registros são gravados como "Sistema" (sem usuário)
    private static bool $gravandoComoSistema = false;

    // enquanto true, a auditoria automática dos models fica pausada (ex.: importação em massa,
    // que grava um único registro de resumo no lugar de um por linha)
    private static bool $pausada = false;

    public static function semAuditoriaAutomatica(callable $acao): mixed
    {
        $anterior = self::$pausada;
        self::$pausada = true;

        try {
            return $acao();
        } finally {
            self::$pausada = $anterior;
        }
    }

    /**
     * Tudo o que for registrado dentro de $acao vira uma ação só na tela de logs
     * (ex.: os vários campos alterados ao salvar um cadastro).
     */
    public static function emLote(callable $acao): mixed
    {
        if (self::$loteAtual !== null) {
            return $acao();
        }

        self::$loteAtual = (string) Str::uuid();

        try {
            return $acao();
        } finally {
            self::$loteAtual = null;
        }
    }

    public static function auditoriaPausada(): bool
    {
        return self::$pausada;
    }

    /**
     * Executa uma ação automática do sistema: a auditoria não atribui ao usuário logado.
     */
    public static function comoSistema(callable $acao): mixed
    {
        $anterior = self::$gravandoComoSistema;
        self::$gravandoComoSistema = true;

        try {
            return $acao();
        } finally {
            self::$gravandoComoSistema = $anterior;
        }
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Registros de log não podem ser alterados.'));
        static::deleting(fn () => throw new LogicException('Registros de log não podem ser excluídos.'));
    }

    /**
     * Grava um registro de auditoria sobre um model (ou sobre um módulo, sem model).
     */
    public static function registrar(
        ?Model $model,
        string $acao,
        ?string $campo = null,
        ?string $valorAnterior = null,
        ?string $valorNovo = null,
        ?string $observacao = null,
        ?string $modulo = null,
        ?int $userId = null,
    ): self {
        return self::create([
            'lote' => self::$loteAtual ?? (string) Str::uuid(),
            'user_id' => $userId ?? (self::$gravandoComoSistema ? null : auth()->id()),
            'modulo' => $modulo ?? ($model && method_exists($model, 'getModuloAuditoria') ? $model->getModuloAuditoria() : 'sistema'),
            'tipo_registro' => $model ? class_basename($model) : 'Sistema',
            'registro_id' => $model?->getKey() ?? 0,
            'acao' => $acao,
            'campo_alterado' => $campo,
            'valor_anterior' => $valorAnterior,
            'valor_novo' => $valorNovo,
            'observacao' => $observacao,
            'data_hora' => now(),
            // de onde veio a ação (ações automáticas do sistema não têm IP)
            'ip' => self::$gravandoComoSistema ? null : request()?->ip(),
            'navegador' => self::$gravandoComoSistema ? null : (Str::limit((string) request()?->userAgent(), 250, '') ?: null),
        ]);
    }


    /**
     * Registra a troca de uma lista (permissões de um usuário, membros de um grupo...).
     * Só grava se algo mudou. Os itens são gravados pelo nome, em ordem.
     *
     * @param array<string> $antes
     * @param array<string> $depois
     */
    public static function registrarLista(Model $model, string $acao, string $campo, array $antes, array $depois, ?string $modulo = null): void
    {
        sort($antes);
        sort($depois);

        if ($antes === $depois) {
            return;
        }

        $adicionados = array_values(array_diff($depois, $antes));
        $removidos = array_values(array_diff($antes, $depois));

        $resumo = collect([
            $adicionados ? 'Incluídos: '.implode(', ', $adicionados) : null,
            $removidos ? 'Retirados: '.implode(', ', $removidos) : null,
        ])->filter()->implode(' | ');

        self::registrar(
            $model,
            $acao,
            campo: $campo,
            valorAnterior: $antes ? implode("\n", $antes) : '(nenhum)',
            valorNovo: $depois ? implode("\n", $depois) : '(nenhum)',
            observacao: $resumo,
            modulo: $modulo,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Histórico de um registro (ex.: histórico da amostra - RN016 / RF020).
     */
    public function scopeDoRegistro(Builder $query, Model $model): Builder
    {
        return $query->where('tipo_registro', class_basename($model))
            ->where('registro_id', $model->getKey());
    }

    public function getModuloNomeAttribute(): string
    {
        return config('amostraweb.modulos')[$this->modulo]
            ?? self::MODULOS_EXTRAS[$this->modulo]
            ?? ucfirst(str_replace('_', ' ', $this->modulo));
    }

    public function getAcaoNomeAttribute(): string
    {
        return self::ACOES[$this->acao] ?? ucfirst($this->acao);
    }

    public function getCampoNomeAttribute(): ?string
    {
        if ($this->campo_alterado === null) {
            return null;
        }

        return self::CAMPOS[$this->campo_alterado] ?? ucfirst(str_replace('_', ' ', $this->campo_alterado));
    }
}
