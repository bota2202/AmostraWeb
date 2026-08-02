# 05 - Regras de Negócio

## Objetivo

Este documento descreve as regras de negócio que deverão ser respeitadas durante todo o funcionamento do sistema.

---

## Regras Gerais

### RN001 - Código do Pedido

Todo pedido deverá possuir um código único gerado automaticamente pelo sistema.

O código será composto por seis dígitos numéricos, iniciando em **000001**.

---

### RN002 - Código da Amostra

Toda amostra deverá possuir um código único gerado automaticamente pelo sistema.

O código será composto por seis dígitos numéricos, iniciando em **000001**.

---

### RN003 - Cadastro de Pedidos

Um pedido somente poderá ser cadastrado por usuários que possuam a permissão correspondente.

Todo pedido deverá conter, obrigatoriamente, pelo menos uma amostra.

---

### RN004 - Responsável pelo Pedido

Todo pedido deverá possuir obrigatoriamente um vendedor responsável.

Após o cadastro, o responsável não poderá ser alterado, salvo por usuários autorizados.

Todas as amostras pertencentes ao pedido serão vinculadas automaticamente ao mesmo responsável.

---

### RN005 - Vínculo das Amostras

Toda amostra deverá pertencer obrigatoriamente a um único pedido.

---

### RN006 - Produtos

Nesta primeira versão, os produtos serão cadastrados manualmente durante a criação do pedido.

Cada produto informado deverá gerar automaticamente uma amostra individual.

A futura integração com o ERP poderá substituir este comportamento.

---

### RN007 - Prazo de Avaliação

Cada amostra deverá possuir um prazo de avaliação informado durante o cadastro do pedido.

---

### RN008 - Alteração de Prazo

O vendedor poderá solicitar a alteração do prazo de avaliação de uma amostra.

A solicitação deverá conter uma justificativa obrigatória.

A alteração somente será efetivada após aprovação do gestor responsável ou de um administrador.

---

### RN009 - Status Atrasado

Quando a data atual ultrapassar o prazo de avaliação e a amostra não estiver:

- Aprovada;
- Reprovada;
- Cancelada;

o sistema deverá alterar automaticamente seu status para **Atrasado**.

---

### RN010 - Aprovação

Uma amostra poderá ser aprovada mesmo estando com o status **Atrasado**.

---

### RN011 - Reprovação

Uma amostra poderá ser reprovada mesmo estando com o status **Atrasado**.

---

### RN012 - Independência das Amostras

As amostras pertencentes ao mesmo pedido possuirão ciclo de vida independente.

A alteração de status de uma amostra não deverá alterar o status das demais amostras pertencentes ao mesmo pedido.

---

### RN013 - Cancelamento

Uma amostra poderá ser cancelada por usuários autorizados.

Após o cancelamento:

- Não poderá sofrer novas alterações;
- Permanecerá armazenada no sistema;
- Continuará disponível para consulta.

---

### RN014 - Exclusão

A exclusão permanente somente poderá ocorrer para amostras canceladas.

Toda exclusão deverá permanecer registrada na auditoria.

---

### RN015 - Auditoria

Todas as ações relevantes realizadas pelos usuários ou automaticamente pelo sistema deverão ser registradas na auditoria.

---

### RN016 - Histórico

Toda alteração de status deverá gerar um novo registro no histórico da respectiva amostra.

---

### RN017 - Visualização

O sistema deverá respeitar o escopo de visualização definido pelo perfil do usuário.

- O vendedor visualizará apenas seus próprios pedidos e amostras.
- O gestor visualizará os pedidos e amostras pertencentes ao seu estabelecimento.
- O administrador poderá visualizar todos os registros do sistema.

---

### RN018 - Estabelecimento

O estabelecimento do pedido será definido automaticamente de acordo com o estabelecimento vinculado ao vendedor responsável.

As amostras herdarão automaticamente essa informação.

---

### RN019 - Identificação do Usuário

Todos os usuários serão identificados pelo código de usuário utilizado na empresa.

---

### RN020 - Registro de Alterações

Todas as alterações realizadas em pedidos e amostras deverão registrar:

- Usuário responsável;
- Data e hora;
- Ação realizada;
- Informações alteradas.

---

### RN021 - Configuração do Sistema

As configurações gerais do sistema deverão ser centralizadas em arquivos específicos de configuração, permitindo alterações sem necessidade de modificar o código-fonte.

Exemplos:

- Nome do sistema;
- Logotipo;
- Cores;
- Integrações;
- Configurações gerais.

---

### RN022 - Permissões

As funcionalidades do sistema deverão ser controladas por permissões individuais atribuídas aos usuários.

Os perfis de usuário serão utilizados apenas para definir o escopo de atuação dentro do sistema.

---

### RN023 - Integridade dos Pedidos

Um pedido somente será considerado válido caso possua pelo menos uma amostra vinculada.

Não será permitido remover todas as amostras de um pedido já existente.

---

### RN024 - Integridade das Amostras

Toda amostra deverá estar vinculada a um único pedido durante todo o seu ciclo de vida.

Não será permitido transferir uma amostra para outro pedido após sua criação.