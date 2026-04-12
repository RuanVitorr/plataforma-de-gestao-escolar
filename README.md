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

Diagrama do banco de dados: https://dbdiagram.io/d/69dba77f8089629684789440

<img width="1112" height="553" alt="image" src="https://github.com/user-attachments/assets/95087708-0779-43e7-93f8-968d5bf39d2f" />






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

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio>
```

---

### 2. Criar e configurar o banco de dados PostgreSQL

Execute o script abaixo no seu PostgreSQL:

```sql
-- ==========================================
-- Banco de Dados: escola
-- ==========================================

DROP DATABASE IF EXISTS escola;
CREATE DATABASE escola;

-- Conectar ao banco (psql)
\c escola

-- ==========================================
-- Tabela: alunos
-- ==========================================
CREATE TABLE alunos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rgm VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- Tabela: disciplinas
-- ==========================================
CREATE TABLE disciplinas (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL
);

-- ==========================================
-- Tabela: professores
-- ==========================================
CREATE TABLE professores (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    disciplina_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_disciplina
        FOREIGN KEY(disciplina_id)
        REFERENCES disciplinas(id)
        ON DELETE SET NULL
);

-- ==========================================
-- Tabela: aluno_disciplina (matrícula)
-- ==========================================
CREATE TABLE aluno_disciplina (
    id SERIAL PRIMARY KEY,
    aluno_id INT NOT NULL,
    disciplina_id INT NOT NULL,
    CONSTRAINT fk_aluno FOREIGN KEY(aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    CONSTRAINT fk_disciplina FOREIGN KEY(disciplina_id) REFERENCES disciplinas(id) ON DELETE CASCADE,
    CONSTRAINT uq_aluno_disciplina UNIQUE(aluno_id, disciplina_id)
);

-- ==========================================
-- Tabela: notas
-- ==========================================
CREATE TABLE notas (
    id SERIAL PRIMARY KEY,
    aluno_disciplina_id INT NOT NULL,
    nota1 NUMERIC(5,2),
    nota2 NUMERIC(5,2),
    CONSTRAINT fk_aluno_disciplina FOREIGN KEY(aluno_disciplina_id) REFERENCES aluno_disciplina(id) ON DELETE CASCADE
);
```

---

### 3. (Opcional) Executar script da pasta `/database`

Caso prefira, utilize o script SQL já disponível no projeto.

---

### 4. Configurar conexão com o banco

Edite o arquivo:

```
/config/database.php
```

E ajuste:

* host
* porta
* usuário
* senha
* nome do banco (`escola`)

---

### 5. Rodar o servidor local

```bash
php -S localhost:8000
```

---

### 6. Acessar a API

Via navegador ou Postman:

```
http://localhost:8000/list/alunos
```



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
