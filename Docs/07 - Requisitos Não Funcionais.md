# 07 - Requisitos Não Funcionais

## Objetivo

Este documento descreve os requisitos não funcionais do AmostraWeb, estabelecendo as características de qualidade, desempenho, segurança, usabilidade e manutenibilidade que deverão ser atendidas durante o desenvolvimento do sistema.

---

# Desempenho

### RNF001 - Tempo de Resposta

O sistema deverá responder às requisições dos usuários em tempo adequado, proporcionando uma navegação fluida durante sua utilização.

### RNF002 - Processamento

As operações automáticas do sistema deverão ser executadas sem comprometer a experiência dos usuários.

---

# Segurança

### RNF003 - Autenticação

O acesso ao sistema deverá ser permitido apenas para usuários autenticados.

### RNF004 - Controle de Acesso

O acesso às funcionalidades deverá respeitar as permissões atribuídas ao usuário.

### RNF005 - Auditoria

Os registros de auditoria deverão ser protegidos contra alterações não autorizadas.

### RNF006 - Armazenamento de Senhas

As senhas dos usuários deverão ser armazenadas utilizando algoritmos seguros de criptografia, não sendo permitido seu armazenamento em texto puro.

---

# Usabilidade

### RNF007 - Interface

O sistema deverá possuir uma interface intuitiva, padronizada e de fácil utilização.

### RNF008 - Responsividade

A interface deverá adaptar-se corretamente às diferentes resoluções de tela utilizadas pelos usuários.

---

# Manutenibilidade

### RNF009 - Organização do Código

O sistema deverá seguir uma arquitetura organizada, facilitando futuras manutenções e evoluções.

### RNF010 - Configurações Centralizadas

As configurações gerais da aplicação deverão estar centralizadas em arquivos específicos, evitando alterações diretas no código-fonte.

### RNF011 - Padronização

O código deverá seguir os padrões definidos para o projeto, mantendo consistência entre os módulos.

### RNF012 - Modularização

O sistema deverá ser organizado em módulos independentes, permitindo a evolução e manutenção de funcionalidades sem impactar significativamente os demais módulos.

---

# Compatibilidade

### RNF013 - Navegadores

O sistema deverá ser compatível com os principais navegadores modernos.

---

# Confiabilidade

### RNF014 - Integridade dos Dados

O sistema deverá garantir a integridade das informações armazenadas.

### RNF015 - Consistência

As regras de negócio deverão ser aplicadas de forma consistente em todas as operações realizadas pelo sistema.

### RNF016 - Disponibilidade

O sistema deverá manter a consistência dos dados mesmo diante de falhas inesperadas durante o processamento das operações.

---

# Escalabilidade

### RNF017 - Evolução

A arquitetura do sistema deverá permitir a inclusão de novos módulos e funcionalidades sem necessidade de grandes alterações estruturais.

### RNF018 - Integrações Futuras

O sistema deverá ser preparado para futuras integrações com sistemas externos, como ERP e demais aplicações corporativas.

---

# Qualidade

### RNF019 - Legibilidade

O código-fonte deverá ser escrito de forma clara, organizada e de fácil compreensão, seguindo boas práticas de desenvolvimento.

### RNF020 - Reutilização

Sempre que possível, componentes, serviços e regras de negócio deverão ser reutilizados, evitando duplicação de código.

### RNF021 - Documentação

O projeto deverá possuir documentação técnica suficiente para facilitar sua manutenção e evolução.

### RNF022 - Arquitetura

O sistema deverá seguir a arquitetura MVC disponibilizada pelo Laravel, respeitando a separação de responsabilidades entre as camadas da aplicação.