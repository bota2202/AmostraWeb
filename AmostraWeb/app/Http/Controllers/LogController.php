<?php

namespace App\Http\Controllers;

use App\Models\Log;

/**
 * Logs são apenas consultados; não são criados ou alterados pela tela.
 */
class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = Log::with('user')->latest('data_hora')->paginate(30);

        return view('logs.index', [
            'logs' => $logs,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Log $log)
    {
        $log->load('user');

        return view('logs.show', [
            'log' => $log,
        ]);
    }
}
