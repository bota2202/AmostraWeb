<?php

namespace App\Http\Controllers;

use App\Models\Estabelecimento;
use App\Models\User;
use App\Http\Requests\StoreEstabelecimentoRequest;
use App\Http\Requests\UpdateEstabelecimentoRequest;

class EstabelecimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estabelecimentos=Estabelecimento::paginate(10);
        return view('estabelecimentos.index',[
            'estabelecimentos'=>$estabelecimentos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gestores=User::where('cargo','gestor')->get();
        return view('estabelecimentos.create',[
            'gestores'=>$gestores,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEstabelecimentoRequest $request)
    {
        Estabelecimento::create($request->validated());
        return redirect('/estabelecimentos')->with('success','Estabelecimento criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Estabelecimento $estabelecimento)
    {
        return view('estabelecimentos.show',[
            'estabelecimento'=>$estabelecimento,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Estabelecimento $estabelecimento)
    {
        $gestores=User::where('cargo','gestor')->get();
        return view('estabelecimentos.edit',[
            'gestores'=>$gestores,
            'estabelecimento'=>$estabelecimento,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEstabelecimentoRequest $request, Estabelecimento $estabelecimento)
    {
        $estabelecimento->update($request->validated());
        return redirect('/estabelecimentos')->with('success','Estabelecimento de id '.$estabelecimento->id.' atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Estabelecimento $estabelecimento)
    {
        $id=$estabelecimento->id;
        $estabelecimento->delete();
        return redirect('/estabelecimentos')->with('success','Estabelecimento de id '.$id.' excluído com sucesso');
    }
}
