<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Models\Grupo;
use App\Models\Permissao;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Grupos de permissões (tela "Permissões" da sidebar).
 */
class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grupos = Grupo::withCount(['users', 'permissoes'])->orderBy('nome')->get();

        return view('grupos.index', [
            'grupos' => $grupos,
            'totalPermissoes' => Permissao::count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('grupos.create');
    }

    /**
     * Cria o grupo e já abre a edição para incluir membros e permissões.
     */
    public function store(StoreGrupoRequest $request)
    {
        $grupo = Grupo::create($request->validated());

        return redirect()->to(route('grupos.edit', $grupo).'#membros')
            ->with('success', 'Grupo '.$grupo->nome.' criado. Agora inclua os membros e libere as permissões.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Grupo $grupo)
    {
        $grupo->load([
            'users' => fn ($q) => $q->orderBy('name'),
            'permissoes',
        ]);

        return view('grupos.show', [
            'grupo' => $grupo,
            'permissoesPorModulo' => Permissao::agrupadasPorModulo(),
        ]);
    }

    /**
     * Edição com abas: Dados, Membros e Permissões.
     */
    public function edit(Grupo $grupo)
    {
        return view('grupos.edit', [
            'grupo' => $grupo,
            'usuarios' => User::orderBy('name')->get(),
            'membros' => $grupo->users()->pluck('users.id')->all(),
            'permissoesPorModulo' => Permissao::agrupadasPorModulo(),
            'selecionadas' => $grupo->permissoes()->pluck('permissoes.id')->all(),
        ]);
    }

    /**
     * Salva as três abas de uma vez.
     */
    public function update(UpdateGrupoRequest $request, Grupo $grupo)
    {
        DB::transaction(function () use ($request, $grupo) {
            $grupo->update($request->safe()->only(['nome', 'descricao']));
            $grupo->users()->sync($request->validated('usuarios') ?? []);
            $grupo->permissoes()->sync($request->validated('permissoes') ?? []);
        });

        return redirect()->route('grupos.show', $grupo)->with('success', 'Grupo '.$grupo->nome.' atualizado com sucesso');
    }

    /**
     * Remove o grupo. Os membros perdem só as permissões que vinham dele.
     */
    public function destroy(Grupo $grupo)
    {
        $nome = $grupo->nome;

        DB::transaction(function () use ($grupo) {
            $grupo->users()->detach();
            $grupo->permissoes()->detach();
            $grupo->delete();
        });

        return redirect()->route('grupos.index')->with('success', 'Grupo '.$nome.' excluído com sucesso');
    }
}
