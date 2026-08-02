# 08 - Modelo Conceitual

## Objetivo

Este documento descreve as principais entidades que compõem o sistema AmostraWeb, apresentando seus objetivos, responsabilidades e atributos, servindo como base para a modelagem do banco de dados e implementação da aplicação.

---

# Pedido

## Objetivo

Representar um envio de um ou mais produtos para um cliente, agrupando todas as amostras pertencentes ao mesmo envio.

## Responsabilidades

- Registrar os dados gerais do envio;
- Agrupar as amostras pertencentes ao pedido;
- Identificar o vendedor responsável;
- Permitir o acompanhamento geral do pedido.

## Atributos

- Código
- Cliente
- Cidade
- Estado
- Usuário Responsável
- Observação
- Status
- Data de Emissão

---

# Amostra

## Objetivo

Representar um produto individual enviado para avaliação comercial por um cliente.

## Responsabilidades

- Controlar o ciclo de vida da amostra;
- Registrar seu prazo de avaliação;
- Armazenar o resultado da avaliação.

## Atributos

- Código
- Pedido
- Produto
- Quantidade
- Status
- Prazo de Avaliação

---

# Usuário

## Objetivo

Representar um usuário autenticado do sistema.

## Responsabilidades

- Acessar o sistema;
- Executar ações conforme suas permissões;
- Registrar operações realizadas na aplicação.

## Atributos

- Código de Usuário
- Nome
- E-mail
- Senha
- Cargo
- Estabelecimento
- Situação

---

# Permissão

## Objetivo

Representar uma autorização concedida a um usuário para executar determinadas funcionalidades do sistema.

## Responsabilidades

- Controlar o acesso às funcionalidades;
- Permitir a personalização das permissões individuais;
- Garantir a segurança operacional do sistema.

## Atributos

- Nome
- Módulo
- Ação

---

# Registro de Log

## Objetivo

Representar o registro de todas as ações relevantes realizadas pelos usuários ou automaticamente pelo sistema, garantindo rastreabilidade, auditoria e histórico completo das operações executadas na aplicação.

## Responsabilidades

- Registrar todas as alterações realizadas no sistema;
- Identificar o usuário responsável pela ação;
- Armazenar o momento em que a ação foi executada;
- Registrar alterações realizadas automaticamente pelo sistema;
- Permitir consultas para fins de auditoria e acompanhamento do histórico das entidades.

## Atributos

- Usuário
- Módulo
- Tipo do Registro
- ID do Registro
- Ação
- Campo Alterado
- Valor Anterior
- Valor Novo
- Data e Hora
- Observação

---

# Solicitação de Alteração de Prazo

## Objetivo

Representar uma solicitação realizada pelo vendedor para alterar o prazo de avaliação de uma amostra.

## Responsabilidades

- Registrar a justificativa da solicitação;
- Controlar o processo de aprovação;
- Registrar o responsável pela decisão.

## Atributos

- Amostra
- Solicitante
- Aprovador
- Justificativa
- Prazo Atual
- Novo Prazo
- Situação
- Motivo da Aprovação/Reprovação
- Data da Solicitação
- Data da Aprovação