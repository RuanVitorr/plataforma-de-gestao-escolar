<?php

require_once __DIR__ . '/../models/Matricula.php';

class MatriculaController {
    private $model;

    public function __construct() {
        $this->model = new Matricula();
    }

    // GET /aluno_disciplina
    public function index() {
        $matriculas = $this->model->getAll();
        echo json_encode($matriculas);
    }

    // GET /aluno_disciplina/{id}
    public function show($id) {
        $matricula = $this->model->getById($id);

        if ($matricula) {
            echo json_encode($matricula);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Matrícula não encontrada']);
        }
    }

    // POST /aluno_disciplina
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !$data ||
            !isset($data['aluno_id']) ||
            !isset($data['disciplina_id'])
        ) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Dados inválidos. aluno_id e disciplina_id são obrigatórios.'
            ]);
            return;
        }

        $aluno_id = $data['aluno_id'];
        $disciplina_id = $data['disciplina_id'];

        if (!$this->model->alunoExiste($aluno_id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'Aluno não encontrado']);
            return;
        }

        if (!$this->model->disciplinaExiste($disciplina_id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'Disciplina não encontrada']);
            return;
        }

        if ($this->model->matriculaJaExiste($aluno_id, $disciplina_id)) {
            http_response_code(409);
            echo json_encode(['erro' => 'Aluno já matriculado nesta disciplina']);
            return;
        }

        $matricula = $this->model->create($aluno_id, $disciplina_id);

        if ($matricula) {
            http_response_code(201);
            echo json_encode($matricula);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Falha ao criar matrícula']);
        }
    }

    // DELETE /aluno_disciplina/{id}
    public function delete($id) {
        $resultado = $this->model->delete($id);

        if (isset($resultado['erro'])) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }
}