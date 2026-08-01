# 05 - Regras de Negócio

## Objetivo

Este documento descreve as regras de negócio que deverão ser respeitadas durante todo o funcionamento do sistema.

## Regras Gerais

### RN001 - Código da Amostra

Toda amostra deverá possuir um código único gerado automaticamente pelo sistema.

O código será composto por seis dígitos numéricos, iniciando em **000001**.

### RN002 - Cadastro de Amostras

Uma amostra somente poderá ser cadastrada por usuários que possuam a permissão correspondente.

### RN003 - Responsável pela Amostra

Toda amostra deverá possuir obrigatoriamente um vendedor responsável.

Após o cadastro, o responsável não poderá ser alterado, salvo por usuários autorizados.


### RN004 - Prazo de Avaliação

Toda amostra deverá possuir um prazo de avaliação informado no momento do cadastro.

### RN005 - Alteração de Prazo

O vendedor poderá solicitar a alteração do prazo da amostra.

A solicitação deverá conter uma justificativa obrigatória.

A alteração somente será efetivada após aprovação do gestor responsável ou de um administrador.

### RN006 - Status Atrasado

Quando a data atual ultrapassar o prazo de avaliação e a amostra não estiver:

- Aprovada;
- Reprovada;
- Cancelada;

o sistema deverá alterar automaticamente seu status para **Atrasado**.

### RN007 - Aprovação

Uma amostra poderá ser aprovada mesmo estando com o status **Atrasado**.

### RN008 - Reprovação

Uma amostra poderá ser reprovada mesmo estando com o status **Atrasado**.

### RN009 - Cancelamento

Uma amostra poderá ser cancelada por usuários autorizados.

Após o cancelamento:

- não poderá sofrer novas alterações;
- permanecerá armazenada no sistema;
- continuará disponível para consulta.

### RN010 - Exclusão

A exclusão permanente somente poderá ocorrer para amostras canceladas.

A exclusão deverá permanecer registrada na auditoria.

### RN011 - Auditoria

Todas as ações relevantes realizadas pelos usuários ou automaticamente pelo sistema deverão ser registradas na auditoria.

### RN012 - Histórico

Toda alteração de status deverá gerar um novo registro no histórico da amostra.

### RN013 - Visualização

O sistema deverá respeitar o escopo de visualização definido pelo perfil do usuário.

### RN014 - Estabelecimento

O estabelecimento da amostra deverá ser definido automaticamente de acordo com o estabelecimento vinculado ao vendedor responsável.

### RN015 - Identificação do Usuário

Todos os usuários serão identificados pelo código de usuário utilizado na empresa.

### RN016 - Produtos

Nesta primeira versão, os produtos serão cadastrados manualmente durante a criação da amostra.

A futura integração com o ERP poderá substituir este comportamento.

### RN017 - Registro de Alterações

Todas as alterações realizadas em uma amostra deverão registrar:

- usuário responsável;
- data e hora;
- ação realizada;
- informações alteradas.

### RN018 - Configuração do Sistema

As configurações gerais do sistema deverão ser centralizadas em arquivos específicos de configuração, permitindo alterações sem necessidade de modificar o código-fonte.

Exemplos:

- Nome do sistema;
- Logo;
- Cores;
- Integrações;
- Configurações gerais.

### RN019 - Permissões

As funcionalidades do sistema deverão ser controladas por permissões individuais atribuídas aos usuários.

Os perfis de usuário serão utilizados apenas para definir o escopo de atuação dentro do sistema.