<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\ResetPasswordUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Estabelecimento;
use App\Models\Permissao;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Lista com busca e filtros (RF022).
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['busca', 'cargo', 'status']);
        $termo = $filtros['busca'] ?? null;

        $users=User::with('estabelecimento')
            ->when($termo, fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('name', 'like', "%{$termo}%")
                ->orWhere('codigo_usuario', 'like', "%{$termo}%")
                ->orWhere('email', 'like', "%{$termo}%")))
            ->when($filtros['cargo'] ?? null, fn (Builder $q, $cargo) => $q->where('cargo', $cargo))
            ->when(isset($filtros['status']) && $filtros['status'] !== '', fn (Builder $q) => $q->where('status', (bool) $filtros['status']))
            ->orderBy('name')
            ->paginate($this->porPagina())
            ->withQueryString();

        return view('usuarios.index',[
            'users'=>$users,
            'filtros'=>$filtros,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usuarios.create',[
            'estabelecimentos'=>Estabelecimento::where('status', true)->orderBy('razao_social')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $usuario = User::create($request->validated());

        $mensagem = 'Usuário '.$usuario->codigo_usuario.' criado.';
        if (! $usuario->isAdmin()) {
            $mensagem .= ' Para liberar os acessos, adicione-o a um grupo em Grupos de permissões.';
        }

        return redirect()->route('usuarios.show', $usuario)->with('success', $mensagem);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        $usuario->load(['estabelecimento', 'grupos' => fn ($q) => $q->orderBy('nome')]);

        return view('usuarios.show',[
            'user'=>$usuario,
            'permissoesPorModulo'=>Permissao::agrupadasPorModulo(),
            'efetivas'=>$usuario->permissoesEfetivas()->pluck('id')->all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        return view('usuarios.edit',[
            'user'=>$usuario,
            'estabelecimentos'=>Estabelecimento::where('status', true)
                ->orWhere('id', $usuario->estabelecimento_id)
                ->orderBy('razao_social')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $usuario)
    {
        $usuario->update($request->validated());
        return redirect()->route('usuarios.show', $usuario)->with('success', 'Usuário '.$usuario->codigo_usuario.' atualizado.');
    }

    /**
     * Ativa ou inativa o usuário. Usuários inativos não conseguem entrar no sistema.
     */
    public function alterarStatus(ChangeStatusRequest $request, User $usuario)
    {
        if ($usuario->is(auth()->user())) {
            return back()->with('error', 'Você não pode inativar o seu próprio usuário.');
        }

        $usuario->update($request->validated());
        $texto = $usuario->status ? 'ativado' : 'inativado';
        return redirect()->route('usuarios.edit', $usuario)->with('success', 'Usuário '.$texto.' com sucesso');
    }

    /**
     * Define uma nova senha para o usuário.
     */
    public function alterarSenha(ResetPasswordUserRequest $request, User $usuario)
    {
        $usuario->update(['password' => $request->validated('password')]);
        return redirect()->route('usuarios.edit', $usuario)->with('success', 'Senha do usuário '.$usuario->codigo_usuario.' alterada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        if ($usuario->is(auth()->user())) {
            return back()->with('error', 'Você não pode excluir o seu próprio usuário.');
        }

        $possuiVinculos = $usuario->pedidos()->exists()
            || $usuario->solicitacoesPrazo()->exists()
            || $usuario->aprovacoesPrazo()->exists()
            || $usuario->estabelecimentosGeridos()->exists();

        if ($possuiVinculos) {
            return back()->with('error', 'Não é possível excluir: o usuário possui pedidos, solicitações ou estabelecimentos vinculados. Inative-o em vez de excluir.');
        }

        $codigo=$usuario->codigo_usuario;

        DB::transaction(function () use ($usuario) {
            $usuario->grupos()->detach();
            $usuario->delete();
        });

        return redirect('/usuarios')->with('success','Usuário de código '.$codigo.' excluído com sucesso');
    }
}
