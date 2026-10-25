<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUsuarioPermissaoRequest;
use App\Models\User;

/**
 * Salva as permissões extras do usuário (aba "Permissões" da edição do usuário).
 * Os grupos do usuário são definidos na edição do grupo (aba Membros).
 */
class UsuarioPermissaoController extends Controller
{
    public function update(UpdateUsuarioPermissaoRequest $request, User $usuario)
    {
        $usuario->permissoes()->sync($request->validated('permissoes') ?? []);

        return redirect()->to(route('usuarios.edit', $usuario).'#permissoes')
            ->with('success', 'Permissões do usuário '.$usuario->codigo_usuario.' atualizadas com sucesso');
    }
}
