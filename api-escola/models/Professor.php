<?php

require_once __DIR__ . '/../config/Database.php';

class Professor {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Retorna todos os professores
    public function getAll() {
        try {
            $stmt = $this->conn->query("SELECT * FROM professores ORDER BY id ASC");
            $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $professores;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar professores",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Retorna um professor pelo ID
    public function getById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM professores WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $professor = $stmt->fetch(PDO::FETCH_ASSOC);
            return $professor ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar professor",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Cria um novo professor
    public function create($nome, $email, $disciplina_id) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO professores (nome, email, disciplina_id)
                VALUES (:nome, :email, :disciplina_id)
                RETURNING *
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':disciplina_id', $disciplina_id, PDO::PARAM_INT);

            $stmt->execute();

            $novoProfessor = $stmt->fetch(PDO::FETCH_ASSOC);
            return $novoProfessor;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao criar professor",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Atualiza um professor pelo ID
    public function update($id, $nome, $email, $disciplina_id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE professores
                SET nome = :nome,
                    email = :email,
                    disciplina_id = :disciplina_id
                WHERE id = :id
                RETURNING *
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':disciplina_id', $disciplina_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $professorAtualizado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $professorAtualizado ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao atualizar professor",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Exclui um professor pelo ID
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM professores WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["mensagem" => "Professor excluido com sucesso"];
            } else {
                return ["erro" => "Professor não encontrado"];
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao excluir professor",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}