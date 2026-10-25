/*
 * AmostraWeb - comportamentos comuns a todas as telas.
 * Sem dependências externas.
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------
     * Menu lateral em telas pequenas (gaveta)
     * ------------------------------------------------------------ */
    const botaoMenu = document.querySelector('[data-abrir-menu]');

    function alternarMenu(abrir) {
        document.body.classList.toggle('menu-aberto', abrir);
        if (botaoMenu) {
            botaoMenu.setAttribute('aria-expanded', abrir ? 'true' : 'false');
            botaoMenu.querySelector('.sr-only').textContent = abrir ? 'Fechar menu' : 'Abrir menu';
        }
        if (abrir) {
            const primeiro = document.querySelector('#menu-principal a');
            if (primeiro) primeiro.focus();
        }
    }

    if (botaoMenu) {
        botaoMenu.addEventListener('click', () => alternarMenu(!document.body.classList.contains('menu-aberto')));
        document.querySelectorAll('[data-fechar-menu]').forEach(el => el.addEventListener('click', () => alternarMenu(false)));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && document.body.classList.contains('menu-aberto')) {
                alternarMenu(false);
                botaoMenu.focus();
            }
        });
    }

    /* ------------------------------------------------------------
     * Categorias do menu: lembra quais o usuário deixou abertas
     * (a categoria da página atual sempre abre)
     * ------------------------------------------------------------ */
    const CHAVE_MENU = 'amostraweb.menu-aberto';
    let abertos = {};
    try { abertos = JSON.parse(localStorage.getItem(CHAVE_MENU)) || {}; } catch (e) { abertos = {}; }

    document.querySelectorAll('[data-menu-grupo]').forEach(grupo => {
        const nome = grupo.dataset.menuGrupo;
        if (!grupo.hasAttribute('data-ativo') && abertos[nome]) grupo.open = true;

        grupo.addEventListener('toggle', () => {
            abertos[nome] = grupo.open;
            try { localStorage.setItem(CHAVE_MENU, JSON.stringify(abertos)); } catch (e) { /* sem armazenamento: só não lembra */ }
        });
    });

    /* ------------------------------------------------------------
     * Mensagens de erro ligadas aos campos (leitores de tela)
     * ------------------------------------------------------------ */
    let primeiroInvalido = null;

    document.querySelectorAll('.erro-campo').forEach((erro, i) => {
        const grupo = erro.closest('.form-grupo, .label-input, .campo');
        const campo = grupo && grupo.querySelector('input:not([type=hidden]), select, textarea');
        if (!campo) return;

        erro.id = erro.id || 'erro-campo-' + i;
        campo.setAttribute('aria-invalid', 'true');
        const descritores = (campo.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
        descritores.push(erro.id);
        campo.setAttribute('aria-describedby', descritores.join(' '));

        if (!primeiroInvalido) primeiroInvalido = campo;
    });

    /* ------------------------------------------------------------
     * Máscaras: data-mascara="cnpj | cpf-cnpj | cep | telefone"
     * ------------------------------------------------------------ */
    const mascaras = {
        cnpj: d => aplicar(d.slice(0, 14), '00.000.000/0000-00'),
        cep: d => aplicar(d.slice(0, 8), '00000-000'),
        'cpf-cnpj': d => d.length <= 11 ? aplicar(d, '000.000.000-00') : aplicar(d.slice(0, 14), '00.000.000/0000-00'),
        telefone: d => d.length <= 10 ? aplicar(d, '(00) 0000-0000') : aplicar(d.slice(0, 11), '(00) 00000-0000'),
    };

    function aplicar(digitos, modelo) {
        let resultado = '';
        let i = 0;
        for (const c of modelo) {
            if (i >= digitos.length) break;
            resultado += c === '0' ? digitos[i++] : c;
        }
        return resultado;
    }

    document.querySelectorAll('[data-mascara]').forEach(campo => {
        const formatar = mascaras[campo.dataset.mascara];
        if (!formatar) return;
        campo.addEventListener('input', () => {
            campo.value = formatar(campo.value.replace(/\D/g, ''));
        });
    });

    /* ------------------------------------------------------------
     * Confirmação antes de enviar, em uma janela (popup) do próprio sistema.
     *   <form data-confirmar="Mensagem?"> ou no botão que envia:
     *   <button data-confirmar="Mensagem?"
     *           data-confirmar-titulo="Título"            (opcional)
     *           data-confirmar-botao="Texto do botão"     (opcional)
     *           data-confirmar-tipo="perigo | sucesso">   (opcional)
     * ------------------------------------------------------------ */
    let janela = null;

    function criarJanela() {
        janela = document.createElement('dialog');
        janela.className = 'janela-confirmar';
        janela.setAttribute('aria-labelledby', 'janela-confirmar-titulo');
        janela.setAttribute('aria-describedby', 'janela-confirmar-mensagem');
        janela.innerHTML =
            '<div class="janela-icone" aria-hidden="true"><i class="fa-solid"></i></div>' +
            '<h2 id="janela-confirmar-titulo"></h2>' +
            '<p id="janela-confirmar-mensagem"></p>' +
            '<div class="janela-acoes">' +
            '  <button type="button" class="botao-toolbar botao-secundario" value="nao">Voltar</button>' +
            '  <button type="button" class="botao-toolbar janela-confirmar-sim" value="sim"></button>' +
            '</div>';
        document.body.appendChild(janela);

        janela.querySelectorAll('.janela-acoes button').forEach(b =>
            b.addEventListener('click', () => janela.close(b.value)));
        // clique fora da caixa fecha sem confirmar
        janela.addEventListener('click', e => { if (e.target === janela) janela.close('nao'); });
        return janela;
    }

    function confirmar({ mensagem, titulo, botao, tipo }) {
        const d = janela || criarJanela();
        const icones = { perigo: 'fa-triangle-exclamation', sucesso: 'fa-circle-check' };

        d.dataset.tipo = tipo || 'padrao';
        d.querySelector('.janela-icone i').className = 'fa-solid ' + (icones[tipo] || 'fa-circle-question');
        d.querySelector('h2').textContent = titulo || 'Confirmar ação';
        d.querySelector('p').textContent = mensagem;
        d.querySelector('.janela-confirmar-sim').textContent = botao || 'Confirmar';
        d.returnValue = '';

        return new Promise(resolve => {
            d.addEventListener('close', () => resolve(d.returnValue === 'sim'), { once: true });
            d.showModal();
            // foco no "Voltar" em ações perigosas; no "Confirmar" nas demais
            d.querySelector(tipo === 'perigo' ? '.botao-secundario' : '.janela-confirmar-sim').focus();
        });
    }

    document.addEventListener('submit', e => {
        const form = e.target;
        const botao = e.submitter && e.submitter.dataset.confirmar ? e.submitter : null;
        const origem = botao || (form.dataset.confirmar ? form : null);
        if (!origem || form.dataset.confirmado) return;

        e.preventDefault();
        confirmar({
            mensagem: origem.dataset.confirmar,
            titulo: origem.dataset.confirmarTitulo,
            botao: origem.dataset.confirmarBotao,
            tipo: origem.dataset.confirmarTipo,
        }).then(ok => {
            if (!ok) {
                if (e.submitter) e.submitter.focus();
                return;
            }
            form.dataset.confirmado = '1';
            form.requestSubmit(e.submitter || undefined);
        });
    });

    // ao voltar pelo navegador, a página volta do cache: pede a confirmação de novo
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('form[data-confirmado]').forEach(f => delete f.dataset.confirmado);
    });

    /* ------------------------------------------------------------
     * Abas acessíveis
     *   <div role="tablist" data-abas [data-botao-salvar="#id"]>
     *     <button role="tab" id="aba-x" aria-controls="painel-x" data-form="form-id">
     *   <div role="tabpanel" id="painel-x" aria-labelledby="aba-x">
     * Abre a aba indicada no endereço (#x) e troca o form do botão salvar.
     * ------------------------------------------------------------ */
    document.querySelectorAll('[data-abas]').forEach(lista => {
        const abas = Array.from(lista.querySelectorAll('[role=tab]'));
        const botaoSalvar = lista.dataset.botaoSalvar ? document.querySelector(lista.dataset.botaoSalvar) : null;

        function abrir(aba, focar) {
            abas.forEach(a => {
                const ativa = a === aba;
                a.setAttribute('aria-selected', ativa ? 'true' : 'false');
                a.tabIndex = ativa ? 0 : -1;
                a.classList.toggle('ativa', ativa);
                document.getElementById(a.getAttribute('aria-controls')).hidden = !ativa;
            });
            if (botaoSalvar && aba.dataset.form) botaoSalvar.setAttribute('form', aba.dataset.form);
            if (focar) aba.focus();
            const nome = aba.dataset.aba;
            history.replaceState(null, '', abas.indexOf(aba) === 0 ? location.pathname + location.search : '#' + nome);
        }

        abas.forEach((aba, i) => {
            aba.addEventListener('click', () => abrir(aba, false));
            aba.addEventListener('keydown', e => {
                let alvo = null;
                if (e.key === 'ArrowRight') alvo = abas[(i + 1) % abas.length];
                if (e.key === 'ArrowLeft') alvo = abas[(i - 1 + abas.length) % abas.length];
                if (e.key === 'Home') alvo = abas[0];
                if (e.key === 'End') alvo = abas[abas.length - 1];
                if (alvo) {
                    e.preventDefault();
                    abrir(alvo, true);
                }
            });
        });

        // aba inicial: a do endereço (#nome), a que tem erro de validação, ou a primeira
        const doEndereco = abas.find(a => '#' + a.dataset.aba === location.hash);
        const comErro = abas.find(a => document.getElementById(a.getAttribute('aria-controls')).querySelector('.erro-campo'));
        abrir(comErro || doEndereco || abas[0], false);
    });

    // foco no primeiro campo com erro (depois de abrir a aba certa)
    if (primeiroInvalido && primeiroInvalido.offsetParent !== null && !document.querySelector("[autofocus]")) {
        primeiroInvalido.focus();
    }
})();
