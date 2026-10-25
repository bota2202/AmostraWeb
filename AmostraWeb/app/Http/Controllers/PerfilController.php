<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Http\Requests\UpdateSenhaPerfilRequest;
use Illuminate\Http\Request;

/**
 * "Meu perfil" (avatar no topo): o usuário logado vê e edita os próprios dados.
 */
class PerfilController extends Controller
{
    public function show(Request $request)
    {
        return view('perfil.perfil', [
            'user' => $request->user()->load('estabelecimento'),
        ]);
    }

    public function edit(Request $request)
    {
        return view('perfil.editar', [
            'user' => $request->user()->load('estabelecimento'),
        ]);
    }

    public function update(UpdatePerfilRequest $request)
    {
        $request->user()->update($request->validated());
        return redirect()->route('perfil')->with('success', 'Seus dados foram atualizados');
    }

    public function alterarSenha(UpdateSenhaPerfilRequest $request)
    {
        $request->user()->update(['password' => $request->validated('password')]);
        return redirect()->route('perfil')->with('success', 'Sua senha foi alterada');
    }
}
