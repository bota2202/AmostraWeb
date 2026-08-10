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

Um pedido deverá possuir pelo menos uma amostra vinculada para ser considerado válido.

---

### RN004 - Responsável pelo Pedido

Todo pedido deverá possuir obrigatoriamente um representante responsável.

Após o cadastro, o responsável não poderá ser alterado, salvo por usuários autorizados.

---

### RN005 - Cadastro de Amostras

Uma amostra somente poderá ser cadastrada por usuários que possuam a permissão correspondente.

A amostra poderá ser criada inicialmente sem vínculo com um pedido.

---

### RN006 - Vínculo das Amostras

Uma amostra poderá permanecer temporariamente sem vínculo com um pedido após seu cadastro.

A amostra deverá ser vinculada a um pedido em até **24 horas** após sua criação.

Após a vinculação, a amostra pertencerá a um único pedido.

---

### RN007 - Produtos

Nesta primeira versão, os produtos serão cadastrados manualmente durante o cadastro da amostra.

A futura integração com o ERP poderá substituir este comportamento.

---

### RN008 - Prazo de Avaliação

Cada amostra deverá possuir um prazo de avaliação informado durante seu cadastro.

---

### RN009 - Alteração de Prazo

O representante poderá solicitar a alteração do prazo de avaliação de uma amostra.

A solicitação deverá conter uma justificativa obrigatória.

A alteração somente será efetivada após aprovação do gestor responsável ou de um administrador.

---

### RN010 - Status Atrasado

Quando a data atual ultrapassar o prazo de avaliação e a amostra não estiver:

- Aprovada;
- Reprovada;
- Cancelada;

o sistema deverá alterar automaticamente seu status para **Atrasado**.

---

### RN011 - Aprovação

Uma amostra poderá ser aprovada mesmo estando com o status **Atrasado**.

---

### RN012 - Reprovação

Uma amostra poderá ser reprovada mesmo estando com o status **Atrasado**.

---

### RN013 - Independência das Amostras

As amostras pertencentes ao mesmo pedido possuirão ciclo de vida independente.

A alteração de status de uma amostra não deverá alterar o status das demais amostras pertencentes ao mesmo pedido.

---

### RN014 - Cancelamento

Uma amostra poderá ser cancelada por usuários autorizados.

Após o cancelamento:

- Não poderá sofrer novas alterações;
- Permanecerá armazenada no sistema;
- Continuará disponível para consulta.

---

### RN015 - Exclusão

A exclusão permanente somente poderá ocorrer para amostras canceladas.

Toda exclusão deverá permanecer registrada no log.

---

### RN016 - Registro de Log

Todas as ações relevantes realizadas pelos usuários ou automaticamente pelo sistema deverão ser registradas no log.

O registro deverá permitir identificar, quando aplicável:

- Usuário responsável;
- Módulo;
- Registro afetado;
- Ação realizada;
- Campo alterado;
- Valor anterior;
- Valor novo;
- Data e hora;
- Observação.

---

### RN017 - Visualização

O sistema deverá respeitar o escopo de visualização definido pelo perfil do usuário.

- O representante visualizará apenas seus próprios pedidos e amostras.
- O gestor visualizará os pedidos e amostras pertencentes ao seu estabelecimento.
- O administrador poderá visualizar todos os registros do sistema.

---

### RN018 - Estabelecimento

O estabelecimento do pedido será definido automaticamente de acordo com o estabelecimento vinculado ao representante responsável.

As amostras vinculadas ao pedido estarão associadas ao mesmo estabelecimento.

---

### RN019 - Identificação do Usuário

Todos os usuários serão identificados pelo código de usuário utilizado na empresa.

---

### RN020 - Registro de Alterações

Todas as alterações realizadas em pedidos e amostras deverão ser registradas no log, contendo, quando aplicável:

- Usuário responsável;
- Data e hora;
- Ação realizada;
- Campo alterado;
- Valor anterior;
- Valor novo.

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

Uma amostra poderá ser criada sem vínculo com um pedido.

Após ser vinculada, a amostra deverá permanecer vinculada ao mesmo pedido durante todo o seu ciclo de vida.

Não será permitido transferir uma amostra para outro pedido após sua vinculação.