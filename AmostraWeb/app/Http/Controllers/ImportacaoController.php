<?php

namespace App\Http\Controllers;

use App\Services\Importacao\Importador;
use App\Services\Importacao\ImportadorClientes;
use App\Services\Importacao\ImportadorProdutos;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Importação de clientes e produtos por CSV exportado do ERP (RF036 / RF041).
 * As rotas informam o tipo ("clientes" ou "produtos").
 */
class ImportacaoController extends Controller
{
    private const TIPOS = [
        'clientes' => ['classe' => ImportadorClientes::class, 'titulo' => 'Clientes', 'singular' => 'cliente', 'chave' => 'Cód. Empresa'],
        'produtos' => ['classe' => ImportadorProdutos::class, 'titulo' => 'Produtos', 'singular' => 'produto', 'chave' => 'Código do produto'],
    ];

    public function create(string $tipo)
    {
        $importador = $this->importador($tipo);
        [$cabecalho] = $importador->modelo();

        return view('importacao.create', [
            'tipo' => $tipo,
            'config' => self::TIPOS[$tipo],
            'colunas' => $cabecalho,
            'resultado' => session('resultado_importacao'),
            'maxLinhas' => Importador::MAX_LINHAS,
        ]);
    }

    public function store(Request $request, string $tipo)
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'extensions:csv,txt', 'max:10240'],
        ], [
            'arquivo.required' => 'Escolha o arquivo CSV exportado do ERP.',
            'arquivo.extensions' => 'O arquivo deve ser .csv (no Excel: Salvar como > CSV separado por ponto e vírgula).',
            'arquivo.max' => 'O arquivo pode ter no máximo 10 MB.',
        ]);

        $arquivo = $request->file('arquivo');

        try {
            $resultado = $this->importador($tipo)->importar($arquivo->getRealPath(), $arquivo->getClientOriginalName());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $mensagem = $resultado['criados'].' criado(s), '.$resultado['atualizados'].' atualizado(s)'
            .($resultado['com_erro'] ? ' e '.$resultado['com_erro'].' linha(s) com erro.' : '.');

        return redirect()->route($tipo.'.importar')
            ->with($resultado['com_erro'] ? 'error' : 'success', 'Importação concluída: '.$mensagem)
            ->with('resultado_importacao', $resultado);
    }

    /**
     * Arquivo-modelo com as colunas esperadas e uma linha de exemplo.
     */
    public function modelo(string $tipo)
    {
        [$cabecalho, $exemplo] = $this->importador($tipo)->modelo();

        return response()->streamDownload(function () use ($cabecalho, $exemplo) {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF"); // BOM: o Excel abre com acentos corretos
            fputcsv($saida, $cabecalho, ';', '"', '');
            fputcsv($saida, $exemplo, ';', '"', '');
            fclose($saida);
        }, 'modelo-importacao-'.$tipo.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function importador(string $tipo): Importador
    {
        abort_unless(isset(self::TIPOS[$tipo]), 404);

        return app(self::TIPOS[$tipo]['classe']);
    }
}
