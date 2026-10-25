<?php

namespace App\Services\Importacao;

use RuntimeException;

/**
 * Erro de uma linha do arquivo: a linha é ignorada e aparece no relatório.
 */
class ErroDeLinha extends RuntimeException
{
}
