<?php

require_once __DIR__ . '/../config/Database.php';

class Boletim {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Verifica se o aluno existe
    public function alunoExiste($aluno_id) {
        $stmt = $this->conn->prepare("
            SELECT id
            FROM alunos
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $aluno_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Retorna o boletim do aluno
    public function getBoletimByAlunoId($aluno_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    a.id AS aluno_id,
                    a.nome AS aluno_nome,
                    a.email AS aluno_email,
                    a.rgm,
                    d.id AS disciplina_id,
                    d.nome AS disciplina_nome,
                    d.codigo AS disciplina_codigo,
                    n.nota1,
                    n.nota2,
                    ROUND(((COALESCE(n.nota1, 0) + COALESCE(n.nota2, 0)) / 2), 2) AS media
                FROM alunos a
                INNER JOIN aluno_disciplina ad ON ad.aluno_id = a.id
                INNER JOIN disciplinas d ON d.id = ad.disciplina_id
                LEFT JOIN notas n ON n.aluno_disciplina_id = ad.id
                WHERE a.id = :aluno_id
                ORDER BY d.nome ASC
            ");

            $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao buscar boletim",
                "mensagem" => $e->getMessage()
            ]);
            exit;
        }
    }
}