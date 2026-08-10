# 04 - Fluxo das Amostras

## Objetivo

Este documento descreve o fluxo de cadastro, vinculação e acompanhamento das amostras dentro do sistema.

## Fluxo Principal

1. O representante realiza o cadastro de uma amostra.

2. O representante informa os dados necessários da amostra, incluindo produto, quantidade e prazo de avaliação.

3. O sistema gera automaticamente um código único para identificação da amostra.

4. A amostra é criada inicialmente sem vínculo com um pedido.

5. O representante deverá vincular a amostra a um pedido em até 24 horas após seu cadastro.

6. Para realizar a vinculação, o pedido deverá possuir um cliente cadastrado e selecionado.

7. Após a vinculação ao pedido, a amostra passa a fazer parte do respectivo envio e fica associada ao cliente do pedido.

8. A amostra é iniciada com o status **Em análise**.

9. Durante o período de avaliação, o representante acompanha individualmente cada amostra.

10. Caso o prazo de avaliação de uma amostra seja ultrapassado sem conclusão, o sistema altera automaticamente seu status para **Atrasado**.

11. A qualquer momento durante a avaliação, cada amostra poderá receber um dos seguintes status:

- Aprovado;
- Reprovado.

12. Após sua aprovação ou reprovação, a amostra é considerada finalizada.

## Fluxos Alternativos

### Vinculação ao Pedido

Uma amostra poderá ser criada sem vínculo com um pedido.

Após o cadastro:

- A amostra deverá ser vinculada a um pedido em até 24 horas;
- O pedido deverá possuir um cliente cadastrado;
- Ao ser vinculada, a amostra ficará associada ao cliente definido no pedido;
- O sistema deverá identificar amostras que permaneçam sem vínculo após esse prazo;
- A amostra poderá ser vinculada posteriormente, conforme as regras definidas para o controle de prazo.

### Cancelamento

Uma amostra poderá ser cancelada antes de sua conclusão.

Após o cancelamento:

- Permanecerá registrada no sistema;
- Não poderá sofrer novas alterações;
- Poderá ser excluída permanentemente apenas por usuários autorizados;
- Todas as ações deverão permanecer registradas no log.

### Prorrogação de Prazo

Durante o período de avaliação, o representante poderá solicitar uma prorrogação do prazo de uma amostra.

A solicitação deverá conter uma justificativa.

A alteração somente será realizada após aprovação do gestor responsável ou de um administrador.

Toda alteração deverá ser registrada no log.

## Ciclo de Vida da Amostra

| Status     | Descrição                                            |
| ---------- | ---------------------------------------------------- |
| Em análise | A amostra está em período de avaliação pelo cliente. |
| Atrasado   | O prazo de avaliação foi excedido sem conclusão.     |
| Aprovado   | A amostra foi aprovada pelo cliente.                 |
| Reprovado  | A amostra foi reprovada pelo cliente.                |
| Cancelada  | A amostra foi cancelada antes de sua conclusão.      |

## Regras Gerais

- Uma amostra poderá ser criada sem vínculo com um pedido.
- Toda amostra deverá ser vinculada a um pedido em até 24 horas após seu cadastro.
- Todo pedido deverá possuir um cliente cadastrado.
- A amostra estará associada ao cliente por meio do pedido ao qual estiver vinculada.
- Toda amostra deverá possuir um código único.
- Toda alteração relevante deverá gerar um registro no log.
- O status **Atrasado** será definido automaticamente pelo sistema.
- Uma amostra atrasada poderá ser aprovada ou reprovada posteriormente.
- O cancelamento não remove o registro da base de dados.
- A exclusão definitiva somente poderá ocorrer para amostras canceladas.
- Toda alteração de prazo deverá possuir justificativa.
- Toda alteração de prazo deverá ser aprovada antes de entrar em vigor.