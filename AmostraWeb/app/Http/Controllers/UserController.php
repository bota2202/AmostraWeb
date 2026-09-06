<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users=User::paginate(100);
        return view('usuarios.index',[
            'users'=>$users,
        ]); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());
        return redirect('/usuarios')->with('success','Usuário criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        return view('usuarios.show',[
            'user'=>$usuario,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        return view('usuarios.edit',[
            'user'=>$usuario,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $usuario)
    {
        $usuario->update($request->validated());
        return redirect('/usuarios')->with('success', 'Usuario de código ' . $usuario->codigo_usuario . ' atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        $codigo=$usuario->codigo_usuario;
        $usuario->delete();
        return redirect('/usuarios')->with('success','Usuário de código '.$codigo.' deletado com sucesso');
    }
}
