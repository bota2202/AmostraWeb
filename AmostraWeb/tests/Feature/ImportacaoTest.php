<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Log;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * RF036 / RF041 - importação de produtos e clientes por CSV exportado do ERP.
 */
class ImportacaoTest extends TestCase
{
    use RefreshDatabase;

    private function arquivo(string $conteudo, string $nome = 'erp.csv'): UploadedFile
    {
        $caminho = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($caminho, $conteudo);

        return new UploadedFile($caminho, $nome, 'text/csv', null, true);
    }

    public function test_importa_produtos_no_formato_do_erp(): void
    {
        $admin = $this->criarUsuario('admin');
        $existente = $this->criarProduto(['codigo_produto' => '110.31141', 'descricao' => 'Nome antigo']);

        // separador ";" e acentos em Windows-1252, como o Excel salva
        $csv = mb_convert_encoding(implode("\r\n", [
            'Código do produto;Descrição do produto;Status;Família;Descrição da família;NCM;Marca',
            '110.30949;OCULOS PARA COLHEITA AGRICOLA - CANE VISION;ATIVO;080;MATERIA PRIMA;90049020;',
            '110.31141;360-CORPO DO CONE PE DE 75CM;INATIVO;080;MATERIA PRIMA;39269090;360',
            ';SEM CODIGO;ATIVO;;;;',
        ]), 'Windows-1252', 'UTF-8');

        $this->actingAs($admin)
            ->post(route('produtos.importar.store'), ['arquivo' => $this->arquivo($csv)])
            ->assertRedirect(route('produtos.importar'))
            ->assertSessionHas('resultado_importacao', fn ($r) => $r['criados'] === 1 && $r['atualizados'] === 1 && $r['com_erro'] === 1);

        $novo = Produto::where('codigo_produto', '110.30949')->first();
        $this->assertSame('080 - MATERIA PRIMA', $novo->familia);
        $this->assertTrue($novo->status);

        $existente->refresh();
        $this->assertSame('360-CORPO DO CONE PE DE 75CM', $existente->descricao);
        $this->assertFalse($existente->status);

        // um único registro de resumo na auditoria
        $this->assertSame(1, Log::where('acao', 'importar')->count());
    }

    public function test_importa_clientes_formatando_cpf_e_endereco(): void
    {
        $admin = $this->criarUsuario('admin');

        $csv = implode("\n", [
            'Cód. Empresa,Razão Social,Nome Fantasia,Cnpj/Cpf,Status empresa,Tipo,Município,Sigla,Bairro,CEP,E-mail,Número,Endereço',
            '56077,MORGANA DARC DE OLIVEIRA RAMOS,MORGANA,11866100696,ATIVO,CLIENTE,ABADIA DOS DOURADOS,MG,CENTRO,38540000,email-invalido,1170,AVENIDA DONA BALDOINA',
            '56078,EMPRESA SEM DOCUMENTO,,123,ATIVO,CLIENTE,LIMEIRA,SP,,,,,',
        ]);

        $this->actingAs($admin)->post(route('clientes.importar.store'), ['arquivo' => $this->arquivo($csv)]);

        $cliente = Cliente::where('codigo_empresa', '56077')->first();
        $this->assertSame('118.661.006-96', $cliente->cnpj_cpf);
        $this->assertNull($cliente->email);
        $this->assertSame('AVENIDA DONA BALDOINA, 1170 - CENTRO - ABADIA DOS DOURADOS/MG - CEP 38540-000', $cliente->endereco_completo);
        $this->assertNull(Cliente::where('codigo_empresa', '56078')->first());
    }

    public function test_arquivo_sem_as_colunas_obrigatorias(): void
    {
        $admin = $this->criarUsuario('admin');

        $this->actingAs($admin)
            ->post(route('clientes.importar.store'), ['arquivo' => $this->arquivo("Nome;Telefone\nFulano;123")])
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'colunas obrigatórias'));
    }

    public function test_importar_exige_permissao(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: ['produtos' => ['visualizar']]);

        $this->actingAs($vendedor)->get(route('produtos.importar'))->assertForbidden();
    }

    public function test_baixa_o_arquivo_modelo(): void
    {
        $admin = $this->criarUsuario('admin');

        $resposta = $this->actingAs($admin)->get(route('produtos.importar.modelo'));

        $resposta->assertOk();
        $conteudo = $resposta->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $conteudo, 'O arquivo deve ter BOM para o Excel abrir os acentos.');
        $this->assertStringContainsString('"Código do produto";"Descrição do produto"', $conteudo);

        // o próprio modelo pode ser importado de volta
        $caminho = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($caminho, $conteudo);
        $this->actingAs($admin)->post(route('produtos.importar.store'), ['arquivo' => new UploadedFile($caminho, 'modelo.csv', 'text/csv', null, true)]);
        $this->assertDatabaseHas('produtos', ['codigo_produto' => '110.30949', 'data_revisao' => '2026-08-01 00:00:00']);
    }
}
