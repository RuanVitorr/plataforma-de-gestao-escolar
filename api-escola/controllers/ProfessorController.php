<?php

require_once __DIR__ . '/../models/Professor.php';

class ProfessorController {

    private $model;

    public function __construct() {
        $this->model = new Professor();
    }

    // GET /list/professores
    public function index() {
        $professores = $this->model->getAll();
        echo json_encode($professores);
    }

    // GET /professores/{id}
    public function show($id) {
        $professor = $this->model->getById($id);

        if ($professor) {
            echo json_encode($professor);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Professor não encontrado']);
        }
    }

    // POST /create/professores
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !$data ||
            !isset($data['nome']) ||
            !isset($data['email']) ||
            !isset($data['disciplina_id'])
        ) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Dados inválidos. Nome, email e disciplina_id são obrigatórios.'
            ]);
            return;
        }

        $professor = $this->model->create(
            $data['nome'],
            $data['email'],
            $data['disciplina_id']
        );

        if ($professor) {
            http_response_code(201);
            echo json_encode($professor);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Falha ao criar professor']);
        }
    }

    // PUT /update/professores/{id}
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !$data ||
            !isset($data['nome']) ||
            !isset($data['email']) ||
            !isset($data['disciplina_id'])
        ) {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Dados inválidos. Nome, email e disciplina_id são obrigatórios.'
            ]);
            return;
        }

        $professorAtualizado = $this->model->update(
            $id,
            $data['nome'],
            $data['email'],
            $data['disciplina_id']
        );

        if ($professorAtualizado) {
            echo json_encode($professorAtualizado);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Professor não encontrado']);
        }
    }

    // DELETE /delete/professores/{id}
    public function delete($id) {
        $resultado = $this->model->delete($id);

        if (isset($resultado['erro'])) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }
}