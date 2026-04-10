<?php

require_once __DIR__ . '/../config/Database.php';

class Nota {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Lista todas as notas
    public function getAll() {
        try {
            $stmt = $this->conn->query("
                SELECT * 
                FROM notas
                ORDER BY id ASC
            ");

            $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $notas;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar notas",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Busca uma nota pelo ID
    public function getById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM notas
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $nota = $stmt->fetch(PDO::FETCH_ASSOC);
            return $nota ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar nota",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Verifica se a matrícula existe
    public function matriculaExiste($aluno_disciplina_id) {
        $stmt = $this->conn->prepare("
            SELECT id
            FROM aluno_disciplina
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $aluno_disciplina_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Verifica se já existe nota para a matrícula
    public function notaJaExiste($aluno_disciplina_id) {
        $stmt = $this->conn->prepare("
            SELECT id
            FROM notas
            WHERE aluno_disciplina_id = :aluno_disciplina_id
        ");

        $stmt->bindParam(':aluno_disciplina_id', $aluno_disciplina_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Cria uma nota
    public function create($aluno_disciplina_id, $nota1, $nota2) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notas (aluno_disciplina_id, nota1, nota2)
                VALUES (:aluno_disciplina_id, :nota1, :nota2)
                RETURNING *
            ");

            $stmt->bindParam(':aluno_disciplina_id', $aluno_disciplina_id, PDO::PARAM_INT);
            $stmt->bindParam(':nota1', $nota1);
            $stmt->bindParam(':nota2', $nota2);

            $stmt->execute();

            $novaNota = $stmt->fetch(PDO::FETCH_ASSOC);
            return $novaNota;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao criar nota",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Atualiza uma nota
    public function update($id, $nota1, $nota2) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notas
                SET nota1 = :nota1,
                    nota2 = :nota2
                WHERE id = :id
                RETURNING *
            ");

            $stmt->bindParam(':nota1', $nota1);
            $stmt->bindParam(':nota2', $nota2);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $notaAtualizada = $stmt->fetch(PDO::FETCH_ASSOC);
            return $notaAtualizada ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao atualizar nota",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Exclui uma nota
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM notas
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["mensagem" => "Nota excluida com sucesso"];
            } else {
                return ["erro" => "Nota não encontrada"];
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao excluir nota",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}