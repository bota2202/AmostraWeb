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
- Estabelecimento
- Usuário Responsável
- Observação
- Status
- Data de Emissão
- Data de Criação
- Data de Atualização

---

# Amostras

## Descrição

Representa um produto individual que poderá ser vinculado a um pedido.

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

# Produtos

## Descrição

Armazena os produtos comercializados pela empresa que podem ser utilizados no cadastro de amostras.

## Campos

- ID
- Código do Produto
- Descrição
- Família
- Subfamília
- Grupo
- Categoria do Produto
- NBS
- Tipo
- NCM
- Serviço
- CEST
- ANP
- Aplicação de Entrada
- Aplicação de Saída
- Origem da Mercadoria
- Controle de Verba
- Status
- Marca
- Data de Revisão
- Classe
- C.A.
- ID Categoria
- ID Preço
- Tributação do Município
- Data de Criação
- Data de Atualização

---

# Clientes

## Descrição

Armazena os clientes que podem receber pedidos e amostras.

## Campos

- ID
- Código da Empresa
- Razão Social
- Nome Fantasia
- Status
- Tipo
- Grupo Fiscal
- Tipo de Empresa
- Grupo Econômico
- CNPJ/CPF
- Endereço Completo
- Telefone
- E-mail
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

- Usuário ID
- Permissão ID

---

# Solicitações de Alteração de Prazo

## Descrição

Registra todas as solicitações de alteração de prazo realizadas para uma amostra.

## Campos

- ID
- Amostra ID
- Solicitante ID
- Aprovador ID
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
- Usuário ID
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

# Estabelecimentos

## Descrição

Registra todas as unidades da empresa para um controle mais preciso.

## Campos

- ID
- Razão Social
- CNPJ
- CEP
- Estado
- Cidade
- Bairro
- Rua
- Número
- Gestor ID
- Data de Criação
- Data de Atualização

---

# Relacionamentos

| Origem | Cardinalidade | Destino |
|---|---:|---|
| Estabelecimento | 1 : N | Usuário |
| Estabelecimento | 1 : N | Pedido |
| Estabelecimento | 1 : 1 | Usuário (Gestor) |
| Usuário | 1 : N | Pedido |
| Cliente | 1 : N | Pedido |
| Pedido | 1 : N | Amostra |
| Produto | 1 : N | Amostra |
| Usuário | N : N | Permissão |
| Usuário | 1 : N | Registro de Log |
| Amostra | 1 : N | Solicitação de Alteração de Prazo |
| Usuário | 1 : N | Solicitação como Solicitante |
| Usuário | 1 : N | Solicitação como Aprovador | 