{{--
    Botões no fim das telas de edição.
    Parâmetros:
      $rotaExcluir  - (opcional) URL do destroy
      $descricao    - texto usado na confirmação (ex.: "o produto 000123")
      $rotaStatus   - (opcional) URL do PATCH de status
      $ativo        - (opcional) status atual do registro
--}}
@push('styles')
@once
<link rel="stylesheet" href="{{ asset('css/layout/form-excluir.css') }}">
@endonce
@endpush

@if (isset($rotaStatus) || isset($rotaExcluir))
<section class="zona-acoes" aria-label="Outras ações">
    @isset($rotaStatus)
    <form action="{{ $rotaStatus }}" method="POST"
        @if ($ativo) data-confirmar="Inativar {{ $descricao }}? Ele deixa de aparecer nos novos cadastros." data-confirmar-titulo="Inativar registro" data-confirmar-botao="Inativar" data-confirmar-tipo="perigo" @endif>
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="{{ $ativo ? 0 : 1 }}">
        <button type="submit" class="botao-status">
            @if ($ativo)
            <i class="fa-solid fa-toggle-off" aria-hidden="true"></i> Inativar
            @else
            <i class="fa-solid fa-toggle-on" aria-hidden="true"></i> Ativar
            @endif
        </button>
    </form>
    @endisset

    @isset($rotaExcluir)
    <form action="{{ $rotaExcluir }}" method="POST" data-confirmar="Excluir {{ $descricao }} permanentemente? Esta ação não pode ser desfeita."
        data-confirmar-titulo="Excluir registro" data-confirmar-botao="Excluir" data-confirmar-tipo="perigo">
        @csrf
        @method('DELETE')
        <button type="submit" class="botao-excluir"><i class="fa-solid fa-trash" aria-hidden="true"></i> Excluir</button>
    </form>
    @endisset
</section>
@endif
