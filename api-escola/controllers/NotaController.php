<?php

require_once __DIR__ . '/../models/Nota.php';

class NotaController {
    private $model;

    public function __construct() {
        $this->model = new Nota();
    }

    // GET /notas
    public function index() {
        $notas = $this->model->getAll();
        echo json_encode($notas);
    }

    // GET /notas/{id}
    public function show($id) {
        $nota = $this->model->getById($id);

        if ($nota) {
            echo json_encode($nota);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Nota não encontrada']);
        }
    }

    // POST /notas
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !$data ||
            !isset($data['aluno_disciplina_id']) ||
            !isset($data['nota1']) ||
            !isset($data['nota2'])
        ) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Dados inválidos. aluno_disciplina_id, nota1 e nota2 são obrigatórios.'
            ]);
            return;
        }

        $aluno_disciplina_id = $data['aluno_disciplina_id'];
        $nota1 = $data['nota1'];
        $nota2 = $data['nota2'];

        if (!$this->model->matriculaExiste($aluno_disciplina_id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'Matrícula não encontrada']);
            return;
        }

        if ($this->model->notaJaExiste($aluno_disciplina_id)) {
            http_response_code(409);
            echo json_encode(['erro' => 'Já existe nota cadastrada para esta matrícula']);
            return;
        }

        if ($nota1 < 0 || $nota1 > 10 || $nota2 < 0 || $nota2 > 10) {
            http_response_code(400);
            echo json_encode(['erro' => 'As notas devem estar entre 0 e 10']);
            return;
        }

        $nota = $this->model->create($aluno_disciplina_id, $nota1, $nota2);

        if ($nota) {
            http_response_code(201);
            echo json_encode($nota);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Falha ao criar nota']);
        }
    }

    // PUT /notas/{id}
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !$data ||
            !isset($data['nota1']) ||
            !isset($data['nota2'])
        ) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Dados inválidos. nota1 e nota2 são obrigatórios.'
            ]);
            return;
        }

        $nota1 = $data['nota1'];
        $nota2 = $data['nota2'];

        if ($nota1 < 0 || $nota1 > 10 || $nota2 < 0 || $nota2 > 10) {
            http_response_code(400);
            echo json_encode(['erro' => 'As notas devem estar entre 0 e 10']);
            return;
        }

        $notaAtualizada = $this->model->update($id, $nota1, $nota2);

        if ($notaAtualizada) {
            echo json_encode($notaAtualizada);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Nota não encontrada']);
        }
    }

    // DELETE /notas/{id}
    public function delete($id) {
        $resultado = $this->model->delete($id);

        if (isset($resultado['erro'])) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }
}