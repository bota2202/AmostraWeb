{{--
    Botões de ação no fim das telas de edição.
    Parâmetros:
      $rotaExcluir  - URL do destroy
      $descricao    - texto usado na confirmação (ex.: "o produto 000123")
      $rotaStatus   - (opcional) URL do PATCH de status
      $ativo        - (opcional) status atual do registro
--}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form-excluir.css') }}">
@endpush

<section class="zona-acoes">
    @isset($rotaStatus)
    <form action="{{ $rotaStatus }}" method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="{{ $ativo ? 0 : 1 }}">
        <button type="submit" class="botao-status">
            @if ($ativo)
            <i class="fa-solid fa-toggle-off"></i> Inativar
            @else
            <i class="fa-solid fa-toggle-on"></i> Ativar
            @endif
        </button>
    </form>
    @endisset

    <form action="{{ $rotaExcluir }}" method="POST" onsubmit="return confirm('Deseja realmente excluir {{ $descricao }}?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="botao-excluir"><i class="fa-solid fa-trash"></i> Excluir</button>
    </form>
</section>
