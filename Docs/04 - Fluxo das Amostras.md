# 04 - Fluxo das Amostras

## Objetivo

Este documento descreve o fluxo de vida de uma amostra dentro do sistema, desde seu cadastro até sua conclusão ou cancelamento.

## Fluxo Principal

1. O vendedor realiza o cadastro da amostra.

2. O sistema gera automaticamente um código único para identificação da amostra.

3. O vendedor informa todos os dados necessários para o envio da amostra, incluindo cliente, produtos, prazo de avaliação e demais informações obrigatórias.

4. Após o cadastro, a amostra passa para o status **Em análise**.

5. Durante o período de avaliação, o vendedor acompanha o andamento da amostra.

6. Caso o prazo de avaliação seja ultrapassado sem uma conclusão, o sistema altera automaticamente o status da amostra para **Atrasado**.

7. A qualquer momento durante a avaliação, o resultado poderá ser informado, alterando o status para:

- Aprovado;
- Reprovado.

8. Após a aprovação ou reprovação, a amostra é considerada finalizada.


## Fluxos Alternativos

### Cancelamento

Uma amostra poderá ser cancelada antes de sua conclusão.

Após o cancelamento:

- A amostra permanecerá registrada no sistema;
- Não poderá mais ser alterada;
- Poderá ser excluída permanentemente apenas por usuários autorizados;
- Todas as ações deverão permanecer registradas na auditoria.

### Prorrogação de Prazo

Durante o período de avaliação, o vendedor poderá solicitar uma prorrogação do prazo.

O pedido deverá conter uma justificativa.

A alteração somente será realizada após aprovação do gestor responsável ou de um administrador.

Toda alteração deverá ser registrada na auditoria.

## Ciclo de Vida da Amostra

| Status | Descrição |
|---------|-----------|
| Em análise | A amostra está em período de avaliação pelo cliente. |
| Atrasado | O prazo de avaliação foi excedido sem conclusão. |
| Aprovado | A amostra foi aprovada pelo cliente. |
| Reprovado | A amostra foi reprovada pelo cliente. |
| Cancelada | A amostra foi cancelada antes de sua conclusão. |

## Regras Gerais

- Toda amostra deverá possuir um código único.
- Toda alteração relevante deverá gerar um registro na auditoria.
- O status **Atrasado** será definido automaticamente pelo sistema.
- Uma amostra atrasada poderá ser aprovada ou reprovada posteriormente.
- O cancelamento não remove o registro da base de dados.
- A exclusão definitiva somente poderá ocorrer para amostras canceladas.
- Toda alteração de prazo deverá possuir justificativa.
- Toda alteração de prazo deverá ser aprovada antes de entrar em vigor.