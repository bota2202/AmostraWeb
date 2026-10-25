<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Solicitacao_Prazo;
use App\Models\User;
use App\Support\EventoLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Consulta dos logs (RF027).
 * A lista mostra uma linha por ação (os campos alterados de um mesmo "salvar" ficam juntos),
 * agrupada por dia; o detalhe mostra o antes e depois, o registro afetado e de onde foi feito.
 */
class LogController extends Controller
{
    private const POR_PAGINA = 25;

    public function index(Request $request)
    {
        $filtros = $request->only(['modulo', 'usuario', 'tipo', 'de', 'ate', 'registro']);
        $registro = $this->registroDoFiltro($filtros['registro'] ?? null);

        $consulta = Log::query()
            ->when($filtros['modulo'] ?? null, fn (Builder $q, $m) => $q->where('modulo', $m))
            ->when(($filtros['usuario'] ?? null) === 'sistema', fn (Builder $q) => $q->whereNull('user_id'))
            ->when(is_numeric($filtros['usuario'] ?? null), fn (Builder $q) => $q->where('user_id', $filtros['usuario']))
            ->when(EventoLog::TIPOS[$filtros['tipo'] ?? ''] ?? null, fn (Builder $q, $tipo) => $q->whereIn('acao', $tipo[1]))
            ->when($filtros['de'] ?? null, fn (Builder $q, $d) => $q->whereDate('data_hora', '>=', $d))
            ->when($filtros['ate'] ?? null, fn (Builder $q, $d) => $q->whereDate('data_hora', '<=', $d))
            ->when($registro, fn (Builder $q) => $q->where(function (Builder $q) use ($registro) {
                $q->where('tipo_registro', $registro['tipo'])->where('registro_id', $registro['id']);

                // a amostra mostra também as solicitações de prazo dela
                if ($registro['tipo'] === 'Amostra') {
                    $q->orWhere(fn (Builder $q) => $q->where('tipo_registro', 'Solicitacao_Prazo')
                        ->whereIn('registro_id', Solicitacao_Prazo::where('amostra_id', $registro['id'])->pluck('id')));
                }
            }));

        // uma linha por ação (lote), da mais recente para a mais antiga
        $lotes = (clone $consulta)
            ->select('lote')
            ->selectRaw('MAX(id) as ultimo_id')
            ->groupBy('lote')
            ->orderByDesc('ultimo_id')
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        $logs = Log::with('user')->whereIn('lote', $lotes->pluck('lote'))->get();
        $eventos = EventoLog::deLogs($logs)->sortByDesc(fn (EventoLog $e) => $e->registros->max('id'))->values();

        return view('logs.index', [
            'lotes' => $lotes,
            'dias' => $eventos->groupBy(fn (EventoLog $e) => $e->quando->toDateString()),
            'filtros' => $filtros,
            'registro' => $registro,
            'usuarios' => User::orderBy('name')->get(['id', 'name']),
            'modulos' => collect(config('amostraweb.modulos'))->except('logs')->put('autenticacao', Log::MODULOS_EXTRAS['autenticacao'])->all(),
        ]);
    }

    public function show(Log $log)
    {
        $registros = $log->lote
            ? Log::with('user')->where('lote', $log->lote)->get()
            : collect([$log->load('user')]);

        return view('logs.show', [
            'evento' => EventoLog::deLogs($registros)->first(),
        ]);
    }

    /**
     * Filtro "registro=Amostra-15": tudo o que aconteceu com um registro.
     *
     * @return array{tipo: string, id: int, nome: string, url: ?string}|null
     */
    private function registroDoFiltro(?string $filtro): ?array
    {
        if (! $filtro || ! preg_match('/^([A-Za-z_]+)-(\d+)$/', $filtro, $m)) {
            return null;
        }

        // o último log do registro (tem o retrato se ele já foi excluído) ou um modelo só para descrever
        $log = Log::where('tipo_registro', $m[1])->where('registro_id', $m[2])->latest('id')->first()
            ?? new Log(['tipo_registro' => $m[1], 'registro_id' => (int) $m[2], 'acao' => 'editar', 'modulo' => 'sistema', 'data_hora' => now()]);

        $evento = EventoLog::deLogs(collect([$log]))->first();
        if (! $evento->temRegistro()) {
            return null;
        }

        return [
            'tipo' => $m[1],
            'id' => (int) $m[2],
            'nome' => ucfirst(preg_replace('/^(o|a) /', '', $evento->tipoRegistroTexto())).' '.$evento->nomeRegistro(),
            'url' => $evento->urlRegistro(auth()->user()),
        ];
    }
}
