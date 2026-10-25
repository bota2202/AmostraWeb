{{--
    Lista de módulos expansíveis com as ações de cada um.
    Parâmetros:
      $permissoesPorModulo - Permissao::agrupadasPorModulo()
      $selecionadas        - ids das permissões marcadas
      $somenteLeitura      - (opcional) true = mostra só ícones, sem checkbox

    As permissões são dadas só por grupo: o modo editável é usado na tela do grupo.
--}}
@php $somenteLeitura = $somenteLeitura ?? false; @endphp

@push('styles')
@once
<link rel="stylesheet" href="{{ asset('css/layout/modulos.css') }}">
@endonce
@endpush

<div class="lista-modulos" @unless($somenteLeitura) data-permissoes @endunless>
    @foreach ($permissoesPorModulo as $modulo => $acoes)
    @php $marcadas = $acoes->whereIn('id', $selecionadas)->count(); @endphp
    <details class="modulo" data-modulo>
        <summary>
            <span class="icone-modulo" aria-hidden="true"><i class="fa-solid {{ \App\Models\Permissao::ICONES_MODULOS[$modulo] ?? 'fa-cube' }}"></i></span>
            {{ config('amostraweb.modulos')[$modulo] ?? $modulo }}
            <span class="contador {{ $marcadas === $acoes->count() ? 'cheio' : '' }}" data-contador>
                <span data-contador-valor>{{ $marcadas }}</span>/{{ $acoes->count() }}<span class="sr-only"> liberadas</span>
            </span>
            <i class="fa-solid fa-chevron-down seta" aria-hidden="true"></i>
        </summary>

        <ul class="acoes-modulo">
            @if ($somenteLeitura)
                @foreach ($acoes as $permissao)
                @php $tem = in_array($permissao->id, $selecionadas); @endphp
                <li class="acao {{ $tem ? 'liberada' : 'bloqueada' }}">
                    <i class="fa-solid {{ $tem ? 'fa-circle-check' : 'fa-circle-xmark' }}" aria-hidden="true"></i>
                    <span class="nome-acao">{{ $permissao->acao_nome }}<span class="sr-only">: {{ $tem ? 'liberada' : 'bloqueada' }}</span></span>
                </li>
                @endforeach
            @else
                <li>
                    <label class="acao marcar-todas">
                        <input type="checkbox" data-marcar-todas>
                        Liberar todas as ações de {{ config('amostraweb.modulos')[$modulo] ?? $modulo }}
                    </label>
                </li>
                @foreach ($acoes as $permissao)
                <li>
                    <label class="acao" for="permissao-{{ $permissao->id }}">
                        <input
                            type="checkbox"
                            name="permissoes[]"
                            id="permissao-{{ $permissao->id }}"
                            value="{{ $permissao->id }}"
                            data-acao
                            @checked(in_array($permissao->id, $selecionadas))>
                        <span class="nome-acao">{{ $permissao->acao_nome }}</span>
                    </label>
                </li>
                @endforeach
            @endif
        </ul>
    </details>
    @endforeach
</div>

@unless ($somenteLeitura)
@push('scripts')
@once
<script>
    // Permissões editáveis: "liberar todas" e contadores
    document.querySelectorAll('[data-permissoes]').forEach(lista => {
        const contadorTotal = document.querySelector('[data-contador-total]');

        function atualizar() {
            let total = 0;
            lista.querySelectorAll('[data-modulo]').forEach(modulo => {
                const doModulo = [...modulo.querySelectorAll('[data-acao]')];
                const marcadas = doModulo.filter(a => a.checked).length;
                const todas = modulo.querySelector('[data-marcar-todas]');
                const contador = modulo.querySelector('[data-contador]');

                total += marcadas;
                contador.querySelector('[data-contador-valor]').textContent = marcadas;
                contador.classList.toggle('cheio', marcadas === doModulo.length);
                todas.checked = marcadas === doModulo.length;
                todas.indeterminate = marcadas > 0 && marcadas < doModulo.length;
            });

            if (contadorTotal) contadorTotal.textContent = total;
        }

        lista.querySelectorAll('[data-acao]').forEach(a => a.addEventListener('change', atualizar));

        lista.querySelectorAll('[data-modulo]').forEach(modulo => {
            const todas = modulo.querySelector('[data-marcar-todas]');
            todas.addEventListener('change', () => {
                modulo.querySelectorAll('[data-acao]').forEach(a => a.checked = todas.checked);
                atualizar();
            });
        });

        atualizar();
    });
</script>
@endonce
@endpush
@endunless
