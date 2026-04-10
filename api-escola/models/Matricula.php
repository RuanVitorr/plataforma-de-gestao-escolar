<?php

require_once __DIR__ . '/../config/Database.php';

class Matricula {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Lista todas as matrículas
    public function getAll() {
        try {
            $stmt = $this->conn->query("
                SELECT * 
                FROM aluno_disciplina
                ORDER BY id ASC
            ");

            $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $matriculas;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar matrículas",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Busca uma matrícula pelo ID
    public function getById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM aluno_disciplina
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $matricula = $stmt->fetch(PDO::FETCH_ASSOC);
            return $matricula ?: null;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar matrícula",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Verifica se o aluno existe
    public function alunoExiste($aluno_id) {
        $stmt = $this->conn->prepare("SELECT id FROM alunos WHERE id = :id");
        $stmt->bindParam(':id', $aluno_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Verifica se a disciplina existe
    public function disciplinaExiste($disciplina_id) {
        $stmt = $this->conn->prepare("SELECT id FROM disciplinas WHERE id = :id");
        $stmt->bindParam(':id', $disciplina_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Verifica se a matrícula já existe
    public function matriculaJaExiste($aluno_id, $disciplina_id) {
        $stmt = $this->conn->prepare("
            SELECT id 
            FROM aluno_disciplina 
            WHERE aluno_id = :aluno_id AND disciplina_id = :disciplina_id
        ");

        $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
        $stmt->bindParam(':disciplina_id', $disciplina_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Cria uma matrícula
    public function create($aluno_id, $disciplina_id) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO aluno_disciplina (aluno_id, disciplina_id)
                VALUES (:aluno_id, :disciplina_id)
                RETURNING *
            ");

            $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
            $stmt->bindParam(':disciplina_id', $disciplina_id, PDO::PARAM_INT);

            $stmt->execute();

            $novaMatricula = $stmt->fetch(PDO::FETCH_ASSOC);
            return $novaMatricula;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao criar matrícula",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }

    // Exclui uma matrícula
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM aluno_disciplina
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["mensagem" => "Matrícula excluida com sucesso"];
            } else {
                return ["erro" => "Matrícula não encontrada"];
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao excluir matrícula",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}