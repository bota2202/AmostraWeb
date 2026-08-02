# 09 - Modelo Lógico do Banco de Dados

## Objetivo

Este documento descreve a estrutura lógica do banco de dados do AmostraWeb, apresentando as entidades, seus principais atributos e os relacionamentos entre elas. Este modelo servirá como base para a implementação das migrations e models da aplicação.

---

# Usuários

## Descrição

Armazena as informações dos usuários autorizados a acessar o sistema.

## Campos

- ID
- Código de Usuário
- Nome
- E-mail
- Senha
- Cargo
- Estabelecimento
- Situação
- Data de Criação
- Data de Atualização

---

# Pedidos

## Descrição

Representa um envio realizado para um cliente, podendo conter uma ou mais amostras.

## Campos

- ID
- Código
- Cliente
- Cidade
- Estado
- Usuário Responsável
- Observação
- Status
- Data de Emissão
- Data de Criação
- Data de Atualização

---

# Amostras

## Descrição

Representa um produto individual pertencente a um pedido.

## Campos

- ID
- Código
- Pedido
- Produto
- Quantidade
- Status
- Prazo de Avaliação
- Data de Criação
- Data de Atualização

---

# Permissões

## Descrição

Armazena todas as permissões disponíveis no sistema.

## Campos

- ID
- Nome
- Ação
- Módulo

---

# Usuário x Permissão

## Descrição

Tabela responsável por relacionar usuários às permissões atribuídas.

## Campos

- Usuário
- Permissão

---

# Solicitações de Alteração de Prazo

## Descrição

Registra todas as solicitações de alteração de prazo realizadas para uma amostra.

## Campos

- ID
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

---

# Registro de Log

## Descrição

Registra todas as ações relevantes executadas por usuários ou automaticamente pelo sistema.

## Campos

- ID
- Usuário
- Módulo
- Tipo do Registro
- ID do Registro
- Ação
- Campo Alterado
- Valor Anterior
- Valor Novo
- Observação
- Data e Hora

---

# Relacionamentos

| Origem | Cardinalidade | Destino |
|---------|---------------|---------|
| Usuário | 1 : N | Pedido |
| Pedido | 1 : N | Amostra |
| Usuário | N : N | Permissão |
| Usuário | 1 : N | Registro de Log |
| Amostra | 1 : N | Solicitação de Alteração de Prazo |
|Usuário | 1:N | Solicitação de Alteração|

---