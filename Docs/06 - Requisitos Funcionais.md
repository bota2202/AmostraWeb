# 06 - Requisitos Funcionais

## Objetivo

Este documento descreve todas as funcionalidades que deverão ser implementadas no sistema.

---

# Autenticação

### RF001 - Login

O sistema deverá permitir que usuários autenticados realizem login utilizando seu código de usuário e senha.

### RF002 - Encerramento de Sessão

O sistema deverá permitir que o usuário encerre sua sessão de forma segura.

---

# Dashboard

### RF003 - Dashboard Inicial

O sistema deverá apresentar um dashboard inicial após o login.

### RF004 - Dashboard Administrativo

O sistema deverá disponibilizar um dashboard administrativo contendo indicadores e estatísticas dos pedidos e das amostras, respeitando o escopo de visualização do usuário.

---

# Pedidos

### RF005 - Cadastro de Pedidos

O sistema deverá permitir o cadastro de novos pedidos.

### RF006 - Consulta de Pedidos

O sistema deverá permitir consultar os pedidos cadastrados.

### RF007 - Pesquisa de Pedidos

O sistema deverá permitir pesquisar pedidos utilizando filtros.

### RF008 - Visualização de Pedidos

O sistema deverá permitir visualizar os detalhes de um pedido, incluindo todas as amostras vinculadas.

### RF009 - Alteração de Pedidos

O sistema deverá permitir alterar as informações permitidas de um pedido.

---

# Amostras

### RF010 - Geração de Amostras

O sistema deverá gerar automaticamente uma amostra para cada produto informado durante o cadastro de um pedido.

### RF011 - Consulta de Amostras

O sistema deverá permitir consultar as amostras cadastradas.

### RF012 - Pesquisa de Amostras

O sistema deverá permitir pesquisar amostras utilizando filtros.

### RF013 - Visualização de Detalhes

O sistema deverá permitir visualizar todas as informações de uma amostra.

### RF014 - Alteração de Amostras

O sistema deverá permitir alterar as informações permitidas de uma amostra.

### RF015 - Cancelamento de Amostras

O sistema deverá permitir cancelar uma amostra.

### RF016 - Exclusão de Amostras

O sistema deverá permitir excluir permanentemente uma amostra cancelada.

### RF017 - Alteração de Status

O sistema deverá permitir registrar alterações de status das amostras.

### RF018 - Solicitação de Alteração de Prazo

O sistema deverá permitir solicitar alteração do prazo de avaliação de uma amostra.

### RF019 - Aprovação de Alteração de Prazo

O sistema deverá permitir aprovar ou reprovar solicitações de alteração de prazo.

### RF020 - Histórico da Amostra

O sistema deverá manter um histórico cronológico das alterações realizadas em cada amostra.

---

# Usuários

### RF021 - Cadastro de Usuários

O sistema deverá permitir o cadastro de novos usuários.

### RF022 - Consulta de Usuários

O sistema deverá permitir consultar os usuários cadastrados.

### RF023 - Alteração de Usuários

O sistema deverá permitir alterar as informações dos usuários.

### RF024 - Definição de Perfil

O sistema deverá permitir definir o perfil de cada usuário.

### RF025 - Gerenciamento de Permissões

O sistema deverá permitir atribuir ou remover permissões individuais dos usuários.

---

# Auditoria

### RF026 - Registro de Auditoria

O sistema deverá registrar automaticamente as ações relevantes realizadas pelos usuários e pelo próprio sistema.

### RF027 - Consulta de Auditoria

O sistema deverá permitir consultar os registros de auditoria.

---

# Configurações

### RF028 - Configurações do Usuário

O sistema deverá permitir que o usuário altere suas preferências pessoais.

### RF029 - Configurações do Sistema

O sistema deverá permitir alterar as configurações gerais da aplicação.

---

# Alertas

### RF030 - Alertas de Prazo

O sistema deverá informar quando existirem amostras com prazo vencido ou próximo do vencimento.

### RF031 - Solicitações Pendentes

O sistema deverá informar gestores ou administradores sobre solicitações de alteração de prazo pendentes de aprovação.