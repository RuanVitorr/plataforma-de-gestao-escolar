<?php

require_once __DIR__ . '/../config/Database.php';

class Aluno {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect(); // Conexão corrigida
    }

    // Retorna todos os alunos
    public function getAll() {
        try {
            $stmt = $this->conn->query("SELECT * FROM alunos ORDER BY id ASC");
            $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $alunos;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar alunos",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Cria um novo aluno
    public function create($nome, $email, $rgm) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO alunos (nome, email, rgm)
                VALUES (:nome, :email, :rgm)
                RETURNING *
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':rgm', $rgm);

            $stmt->execute();

            $novoAluno = $stmt->fetch(PDO::FETCH_ASSOC);
            return $novoAluno;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao criar aluno",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }


    // Atualiza um aluno pelo ID
    public function update($id, $nome, $email, $rgm) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE alunos
                SET nome = :nome,
                    email = :email,
                    rgm = :rgm
                WHERE id = :id
                RETURNING *
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':rgm', $rgm);
            $stmt->bindParam(':id', $id);

            $stmt->execute();

            $alunoAtualizado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $alunoAtualizado;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao atualizar aluno",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }


    // DELETE aluno por ID
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM alunos WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $executou = $stmt->execute();

            if ($executou && $stmt->rowCount() > 0) {
                return ["mensagem" => "Aluno excluido com sucesso"];
            } else {
                return ["erro" => "Aluno não encontrado"];
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao excluir aluno",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}