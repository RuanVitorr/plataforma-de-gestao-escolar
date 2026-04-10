<?php

require_once __DIR__ . '/../config/Database.php';

class Disciplina {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Retorna todas as disciplinas
    public function getAll() {
        try {
            $stmt = $this->conn->query("SELECT * FROM disciplinas ORDER BY id ASC");
            $disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $disciplinas;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar disciplinas",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Retorna uma disciplina pelo ID
    public function getById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM disciplinas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $disciplina = $stmt->fetch(PDO::FETCH_ASSOC);
            return $disciplina ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar disciplina",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Cria uma nova disciplina
    public function create($nome, $codigo) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO disciplinas (nome, codigo)
                VALUES (:nome, :codigo)
                RETURNING *
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':codigo', $codigo);

            $stmt->execute();

            $novaDisciplina = $stmt->fetch(PDO::FETCH_ASSOC);
            return $novaDisciplina;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao criar disciplina",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Atualiza uma disciplina pelo ID
    public function update($id, $nome, $codigo) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE disciplinas
                SET nome = :nome,
                    codigo = :codigo
                WHERE id = :id
                RETURNING *
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $disciplinaAtualizada = $stmt->fetch(PDO::FETCH_ASSOC);
            return $disciplinaAtualizada ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao atualizar disciplina",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Exclui uma disciplina pelo ID
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM disciplinas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["mensagem" => "Disciplina excluida com sucesso"];
            } else {
                return ["erro" => "Disciplina não encontrada"];
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao excluir disciplina",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}