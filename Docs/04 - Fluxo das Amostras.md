# 04 - Fluxo das Amostras

## Objetivo

Este documento descreve o fluxo de cadastro e acompanhamento das amostras dentro do sistema.

## Fluxo Principal

1. O vendedor realiza o cadastro de um pedido.

2. O vendedor informa os dados gerais do pedido, como cliente, data de envio e demais informações obrigatórias.

3. O vendedor adiciona um ou mais produtos ao pedido.

4. Para cada produto informado, o sistema gera automaticamente uma amostra com um código único de identificação.

5. Todas as amostras são iniciadas com o status **Em análise**.

6. Durante o período de avaliação, o vendedor acompanha individualmente cada amostra.

7. Caso o prazo de avaliação de uma amostra seja ultrapassado sem conclusão, o sistema altera automaticamente seu status para **Atrasado**.

8. A qualquer momento durante a avaliação, cada amostra poderá receber um dos seguintes status:

- Aprovado;
- Reprovado.

9. Após sua aprovação ou reprovação, a amostra é considerada finalizada.

## Fluxos Alternativos

### Cancelamento

Uma amostra poderá ser cancelada antes de sua conclusão.

Após o cancelamento:

- Permanecerá registrada no sistema;
- Não poderá sofrer novas alterações;
- Poderá ser excluída permanentemente apenas por usuários autorizados;
- Todas as ações deverão permanecer registradas na auditoria.

### Prorrogação de Prazo

Durante o período de avaliação, o vendedor poderá solicitar uma prorrogação do prazo de uma amostra.

A solicitação deverá conter uma justificativa.

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

- Todo pedido deverá conter pelo menos uma amostra.
- Cada produto informado no pedido gerará uma amostra individual.
- Toda amostra deverá possuir um código único.
- Toda alteração relevante deverá gerar um registro na auditoria.
- O status **Atrasado** será definido automaticamente pelo sistema.
- Uma amostra atrasada poderá ser aprovada ou reprovada posteriormente.
- O cancelamento não remove o registro da base de dados.
- A exclusão definitiva somente poderá ocorrer para amostras canceladas.
- Toda alteração de prazo deverá possuir justificativa.
- Toda alteração de prazo deverá ser aprovada antes de entrar em vigor.