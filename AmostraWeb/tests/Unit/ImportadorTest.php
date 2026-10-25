<?php

namespace Tests\Unit;

use App\Services\Importacao\ImportadorProdutos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImportadorTest extends TestCase
{
    public static function colunas(): array
    {
        return [
            ['Código do produto', 'codigo do produto'],
            ['Cód. Sub-Família', 'cod sub familia'],
            ['Cnpj/Cpf', 'cnpj cpf'],
            ['  E-mail ', 'e mail'],
            ['DESCCLASSIFEMPRESA', 'descclassifempresa'],
        ];
    }

    #[DataProvider('colunas')]
    public function test_reconhece_o_nome_da_coluna_sem_acentos_e_pontuacao(string $original, string $normalizada): void
    {
        $this->assertSame($normalizada, (new ImportadorProdutos())->normalizarColuna($original));
    }
}
