# Plataforma de Gestão Escolar

## 📖 Descrição
Este projeto consiste no desenvolvimento de uma **API REST** para gestão escolar, simulando o funcionamento de uma instituição de ensino superior.  

O sistema permite o gerenciamento de **alunos, professores, turmas, disciplinas e notas**, incluindo o controle de avaliações (N1 e N2) e cálculo de média por disciplina.  

O foco é construir uma API **simples, funcional e organizada**, seguindo boas práticas sem complexidade desnecessária.

---

## 📌 Versões do Projeto
- **N1:** API RESTful completa (Backend)  
- **N2:** Interface Web (Frontend)

---

## 🎯 Objetivos da API
- Gerenciar alunos, professores e turmas  
- Controlar disciplinas e seus respectivos professores  
- Realizar matrícula de alunos em disciplinas  
- Registrar notas (N1 e N2)  
- Consultar boletim acadêmico de forma simplificada

---

## 🛠️ Tecnologias Utilizadas
- PHP 8.5  
- PostgreSQL  
- JSON (comunicação da API)  
- Postman (testes)

---

## 📂 Estrutura do Projeto

- /api-escola
- /config -> Conexão com banco de dados
- /controllers -> Lógica dos endpoints
- /models -> Manipulação de dados
- /routes -> Definição das rotas da API
- /database -> Scripts SQL para criação do banco
- index.php -> Ponto de entrada da API


---

## 🗄️ Modelagem do Banco de Dados

### Entidades principais

**Turma**
- id (PK)  
- nome  
- curso  
- turno  

**Aluno**
- id (PK)  
- nome  
- email  
- turma_id (FK)  

**Matrícula (aluno_disciplina)**
- id (PK)  
- aluno_id (FK)  
- disciplina_id (FK)  
- turma_id (FK)  

**Nota**
- id (PK)  
- matricula_id (FK)  
- nota1  
- nota2  

**Professor**
- id (PK)  
- nome  

**Disciplina**
- id (PK)  
- nome  
- professor_id (FK)  

---

### Relacionamentos
- TURMA 1:N ALUNO  
- ALUNO 1:N MATRICULA  
- DISCIPLINA 1:N MATRICULA  
- MATRICULA 1:1 NOTA  
- PROFESSOR 1:N DISCIPLINA  

---

## 🔌 Endpoints da API

### Alunos
- `GET /list/alunos`  
- `GET /alunos/{id}`  
- `POST /create/alunos`  
- `PUT /update/alunos/{id}`  
- `DELETE /delete/alunos/{id}`  

### Professores
- `GET /list/professores`  
- `GET /professores/{id}`  
- `POST /create/professores`  
- `PUT /update/professores/{id}`  
- `DELETE /delete/professores/{id}`  

### Turmas
- `GET /list/turmas`  
- `GET /turmas/{id}`  
- `POST /create/turmas`  
- `PUT /update/turmas/{id}`  
- `DELETE /delete/turmas/{id}`  

### Disciplinas
- `GET /list/disciplinas`  
- `GET /disciplinas/{id}`  
- `POST /create/disciplinas`  
- `PUT /update/disciplinas/{id}`  
- `DELETE /delete/disciplinas/{id}`  

### Matrículas
- `GET /aluno_disciplina`  
- `GET /aluno_disciplina/{id}`  
- `POST /aluno_disciplina`  
- `DELETE /aluno_disciplina/{id}`  

### Notas
- `GET /notas`  
- `GET /notas/{id}`  
- `POST /notas`  
- `PUT /notas/{id}`  
- `DELETE /notas/{id}`  

### Boletim
- `GET /boletim/{aluno_id}`  
  Retorna:
  - disciplinas cursadas  
  - nota1 e nota2  
  - média por disciplina

---

## ⚙️ Regras de Negócio
- Um aluno pode se matricular em várias disciplinas  
- Cada disciplina pertence a um professor  
- Cada matrícula representa um aluno cursando uma disciplina  
- Cada matrícula possui duas notas (N1 e N2)  
- Média = (nota1 + nota2) / 2  
- Notas devem estar entre 0 e 10

---

## ▶️ Como Executar o Projeto

1. Clonar o repositório:
git clone <url-do-repositorio>


2. Criar banco de dados PostgreSQL:  
- Nome: `escola`

3. Executar script SQL atualizado (pasta `/database`)  

4. Configurar conexão com o banco no arquivo `/config/database.php`  

5. Rodar servidor local:

php -S localhost:8000


6. Acessar a API via Postman ou navegador:
http://localhost:8000/list/alunos


---

## 🧪 Testes
Testes realizados utilizando Postman:  
- Criação de registros  
- Listagem  
- Atualização  
- Exclusão  
- Consulta de boletim  

---

## 📌 Funcionalidades Implementadas
- CRUD completo de alunos(APENAS ESSE)
- CRUD professores
- CRUD turmas
- CRUD disciplinas  
- Matrícula de alunos em disciplinas  
- Lançamento de notas (N1 e N2)  
- Consulta de boletim por aluno

---

## 📊 Critérios Atendidos
- ✅ Funcionalidade: API completa e funcional  
- ✅ Código: Estrutura organizada (MVC simplificado)  
- ✅ Interface: API REST com JSON  
- ✅ Documentação: README completo

---

## 🚀 Próximos Passos (N2)
- Desenvolvimento do frontend web  
- Integração com a API  
- Interface para alunos e professores  

---

## 👨‍💻 Autores
- Ruan Vitor  
- Anita Donato  
- Pedro Henrique
