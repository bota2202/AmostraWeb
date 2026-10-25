{{--
    Lista de módulos expansíveis com as ações de cada um.
    Parâmetros:
      $permissoesPorModulo - Permissao::agrupadasPorModulo()
      $selecionadas        - ids das permissões marcadas
      $somenteLeitura      - (opcional) true = mostra só ícones, sem checkbox

    No modo editável, se a página tiver checkboxes de grupo ([data-grupo]),
    as ações que vêm dos grupos marcados aparecem travadas com "via <grupo>".
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
            <span class="icone-modulo"><i class="fa-solid {{ \App\Models\Permissao::ICONES_MODULOS[$modulo] ?? 'fa-cube' }}"></i></span>
            {{ config('amostraweb.modulos')[$modulo] ?? $modulo }}
            <span class="contador {{ $marcadas === $acoes->count() ? 'cheio' : '' }}" data-contador>
                {{ $marcadas }}/{{ $acoes->count() }}
            </span>
            <i class="fa-solid fa-chevron-down seta"></i>
        </summary>

        <ul class="acoes-modulo">
            @if ($somenteLeitura)
                @foreach ($acoes as $permissao)
                @php $tem = in_array($permissao->id, $selecionadas); @endphp
                <li class="acao {{ $tem ? 'liberada' : 'bloqueada' }}">
                    <i class="fa-solid {{ $tem ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                    <span class="nome-acao">{{ $permissao->acao_nome }}</span>
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
                        <span class="via-grupo" data-via hidden></span>
                    </label>
                    {{-- mantém a permissão própria do usuário mesmo quando ela também vem de um grupo --}}
                    <input type="hidden" name="permissoes[]" value="{{ $permissao->id }}" data-preservar disabled>
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
    // Permissões editáveis: "liberar todas", contadores e herança dos grupos marcados
    document.querySelectorAll('[data-permissoes]').forEach(lista => {
        const form = lista.closest('form') || document;
        const grupos = form.querySelectorAll('[data-grupo]');
        const acoes = lista.querySelectorAll('[data-acao]');
        const contadorTotal = document.querySelector('[data-contador-total]');

        // estado próprio (sem contar os grupos)
        acoes.forEach(a => a.dataset.propria = a.checked ? '1' : '');

        function herdadas() {
            const mapa = {};
            grupos.forEach(g => {
                // checkbox desmarcado = grupo não aplicado; marcador (span) = sempre aplicado
                if (g.matches('input') && !g.checked) return;
                JSON.parse(g.dataset.permissoes).forEach(id => {
                    (mapa[id] = mapa[id] || []).push(g.dataset.nome);
                });
            });
            return mapa;
        }

        function atualizar() {
            const mapa = herdadas();

            acoes.forEach(a => {
                const li = a.closest('li');
                const via = li.querySelector('[data-via]');
                const preservar = li.querySelector('[data-preservar]');
                const nomes = mapa[a.value];

                if (nomes) {
                    a.checked = true;
                    a.disabled = true;
                    via.hidden = false;
                    via.textContent = 'via ' + nomes.join(', ');
                    preservar.disabled = !a.dataset.propria;
                } else {
                    a.disabled = false;
                    a.checked = !!a.dataset.propria;
                    via.hidden = true;
                    preservar.disabled = true;
                }
            });

            let total = 0;
            lista.querySelectorAll('[data-modulo]').forEach(modulo => {
                const doModulo = [...modulo.querySelectorAll('[data-acao]')];
                const marcadas = doModulo.filter(a => a.checked).length;
                const editaveis = doModulo.filter(a => !a.disabled);
                const todas = modulo.querySelector('[data-marcar-todas]');
                const contador = modulo.querySelector('[data-contador]');

                total += marcadas;
                contador.textContent = marcadas + '/' + doModulo.length;
                contador.classList.toggle('cheio', marcadas === doModulo.length);
                todas.checked = marcadas === doModulo.length;
                todas.indeterminate = marcadas > 0 && marcadas < doModulo.length;
                todas.disabled = editaveis.length === 0;
            });

            if (contadorTotal) contadorTotal.textContent = total;
        }

        acoes.forEach(a => a.addEventListener('change', () => {
            a.dataset.propria = a.checked ? '1' : '';
            atualizar();
        }));

        lista.querySelectorAll('[data-modulo]').forEach(modulo => {
            const todas = modulo.querySelector('[data-marcar-todas]');
            todas.addEventListener('change', () => {
                modulo.querySelectorAll('[data-acao]').forEach(a => {
                    if (!a.disabled) a.dataset.propria = todas.checked ? '1' : '';
                });
                atualizar();
            });
        });

        grupos.forEach(g => g.addEventListener('change', atualizar));
        atualizar();
    });
</script>
@endonce
@endpush
@endunless
