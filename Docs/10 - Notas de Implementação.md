# 10 - Notas de Implementação

## Objetivo

Este documento registra como as regras dos documentos 01 a 09 foram implementadas e as decisões
tomadas onde a documentação não detalhava o comportamento.

---

## Perfis, escopo e permissões

### Escopo de visualização (RN017)

| Perfil | Pedidos e amostras visíveis |
|---|---|
| Vendedor (`representante` no banco) | Somente os pedidos em que é o vendedor responsável |
| Gestor | Os pedidos dos estabelecimentos que gerencia e do seu próprio estabelecimento |
| Administrador | Todos |

O dashboard, as listas, os detalhes, as solicitações de prazo e os contadores do menu respeitam esse escopo.
Clientes e produtos são cadastros compartilhados (sem escopo).

### Permissões (RN022)

- As permissões são fixas e definidas em `config/amostraweb.php` (`permissoes_padrao`). Elas são criadas
  pelas migrations; não há tela para cadastrar permissões.
- Ações por módulo:

| Módulo | Ações |
|---|---|
| Pedidos | visualizar, criar, editar (pedidos não são excluídos - RN023) |
| Amostras | visualizar, criar, editar (inclui aprovar/reprovar o resultado), cancelar, excluir |
| Solicitações de prazo | visualizar, criar, aprovar, excluir (desistir da própria solicitação) |
| Clientes / Produtos | visualizar, criar, editar, excluir, importar |
| Estabelecimentos / Usuários / Permissões e grupos | visualizar, criar, editar, excluir |
| Auditoria | visualizar |

- **Grupos de permissões:** as permissões são liberadas **somente por grupo** (ex.: "Vendedores");
  não existe permissão dada direto ao usuário. O usuário tem a soma das permissões dos seus grupos,
  e os membros são definidos na tela do grupo.
- **Administrador:** tem sempre todas as permissões. As demais telas e o dashboard ficam liberados
  para todos os usuários logados.
- **Aprovação de prazo (RN008):** além da permissão "aprovar", somente o gestor do estabelecimento do
  pedido ou um administrador decide; ninguém decide a própria solicitação.

---

## Pedidos e amostras

- O pedido é cadastrado junto com os produtos; cada produto gera uma amostra com código próprio (fluxo 3-4).
- Códigos de pedido e de amostra são sequenciais de seis dígitos (RN001 / RN002).
- O estabelecimento do pedido é o do vendedor responsável (RN018). Vendedores são sempre o responsável
  pelos próprios pedidos; gestores e administradores escolhem o vendedor e podem trocá-lo (RN004).
- **Status do pedido (decisão):** calculado automaticamente. "Em aberto" enquanto houver amostra em
  análise ou atrasada; "Concluído" quando todas forem aprovadas, reprovadas ou canceladas.
- A amostra não pode trocar de pedido (RN024) e o pedido não pode ficar sem amostras (RN023).

### Ciclo de vida da amostra

```
Em análise ──(prazo vence, automático)──> Atrasada
Em análise / Atrasada ──> Aprovada | Reprovada | Cancelada (com motivo)
Aprovada, Reprovada e Cancelada são finais.
```

- **Atrasada (RN009):** marcada automaticamente pelo sistema, no máximo a cada 10 minutos
  (na primeira requisição de um usuário) e de hora em hora pelo agendador
  (`php artisan amostras:atualizar-atrasadas`). A auditoria registra "Sistema" como responsável.
- **Prazo:** só muda por solicitação aprovada. O novo prazo precisa ser futuro e posterior ao atual.
  Ao aprovar o prazo de uma amostra atrasada, ela volta para "Em análise".
- **Exclusão (RN014):** apenas amostras canceladas, sem deixar o pedido vazio. As solicitações de prazo
  da amostra são excluídas junto e tudo fica registrado na auditoria.

---

## Auditoria e histórico (RN015 / RN016 / RN020)

- Toda criação, alteração (um registro por campo, com valor anterior e novo) e exclusão de pedidos,
  amostras, solicitações, clientes, produtos, estabelecimentos, usuários e grupos é registrada.
- Também são registrados: entrada e saída do sistema, tentativas de acesso negadas, alterações de
  permissões e de membros de grupos, e importações (um registro de resumo por arquivo).
- Senhas: registra-se que foram alteradas, nunca o valor.
- Os registros não podem ser alterados nem excluídos (RNF005).
- Tela de logs: uma linha por ação (os campos salvos juntos aparecem como uma ação só), em frases
  legíveis ("Ana Ribeiro alterou o cliente Vale LTDA"), agrupadas por dia, com cor e ícone por tipo.
  O detalhe mostra o antes e depois de cada campo, quem fez, o registro afetado e de onde foi feito
  (IP e navegador).
- O histórico de uma amostra ou pedido fica no módulo de logs: o botão "Histórico" dessas telas abre
  o log já filtrado (a amostra inclui as solicitações de prazo dela).

---

## Importação do ERP (RN006 / RN025 / RF036 / RF041)

- Formato: CSV exportado do Excel (separador `;` ou `,`, UTF-8 ou Windows-1252).
- As colunas são reconhecidas pelo nome usado nas planilhas do ERP (`Docs/Planilhas`),
  sem diferenciar acentos e maiúsculas. A tela de importação oferece um arquivo-modelo.
- Registros existentes são atualizados pelo código (produto: "Código do produto";
  cliente: "Cód. Empresa"); os demais são criados.
- Campos que o ERP envia como código + descrição são gravados juntos (ex.: "080 - MATERIA PRIMA").
- Clientes: CPF/CNPJ é formatado, o endereço é montado a partir de rua, número, bairro,
  município, UF e CEP, e e-mails inválidos ficam em branco.
- Linhas com problema são ignoradas e listadas no relatório da importação.

---

## Usabilidade e acessibilidade (RNF007 / RNF008)

- Interface em português, com mensagens de validação e páginas de erro (403, 404, 419, 429, 500, 503) traduzidas.
- Todas as combinações de cores de texto têm contraste mínimo de 4,5:1 (WCAG AA).
- Navegação completa por teclado (foco visível, link "Pular para o conteúdo", abas com setas),
  leitores de tela (rótulos, títulos em ordem, erros ligados aos campos, mensagens anunciadas)
  e preferência de "reduzir movimento".
- Em telas pequenas, o menu lateral vira uma gaveta; as tabelas se ajustam à largura.
- Login limitado a 5 tentativas por minuto por usuário e IP.
- Ações importantes (aprovar, reprovar, cancelar, inativar, excluir) pedem confirmação numa janela
  do próprio sistema (popup acessível por teclado), não no alerta do navegador.

---

## Redefinição de senha ("Esqueci minha senha")

- Na tela de login, "Esqueci minha senha" pede o e-mail da conta e envia um **código de 6 dígitos**.
- O código vale por **15 minutos**, aceita no máximo **5 tentativas erradas** e só pode ser usado uma vez.
  Fica guardado com hash (nunca em texto puro).
- A tela não informa se o e-mail existe (evita descobrir contas). Usuários inativos não recebem código.
- Envio limitado a 3 códigos a cada 10 minutos por e-mail e IP.
- O e-mail do usuário passa a ser único no sistema.
- O envio do código e a troca da senha ficam no log (sem o valor da senha).
- Envio de e-mail: configurado no `.env` (`MAIL_*`). Com `MAIL_MAILER=log`, o e-mail vai para
  `storage/logs/laravel.log` (útil em desenvolvimento).
