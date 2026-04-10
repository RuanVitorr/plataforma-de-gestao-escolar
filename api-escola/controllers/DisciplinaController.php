
<?php

require_once __DIR__ . '/../models/Disciplina.php';

class DisciplinaController {

    private $model;

    public function __construct() {
        $this->model = new Disciplina();
    }

    // GET /list/disciplinas
    public function index() {
        $disciplinas = $this->model->getAll();
        echo json_encode($disciplinas);
    }

    // GET /disciplinas/{id}
    public function show($id) {
        $disciplina = $this->model->getById($id);

        if ($disciplina) {
            echo json_encode($disciplina);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Disciplina não encontrada']);
        }
    }

    // POST /create/disciplinas
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['nome']) || !isset($data['codigo'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos. Nome e código são obrigatórios.']);
            return;
        }

        $disciplina = $this->model->create($data['nome'], $data['codigo']);

        if ($disciplina) {
            http_response_code(201);
            echo json_encode($disciplina);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Falha ao criar disciplina']);
        }
    }

    // PUT /update/disciplinas/{id}
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['nome']) || !isset($data['codigo'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos. Nome e código são obrigatórios.']);
            return;
        }

        $disciplinaAtualizada = $this->model->update($id, $data['nome'], $data['codigo']);

        if ($disciplinaAtualizada) {
            echo json_encode($disciplinaAtualizada);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Disciplina não encontrada']);
        }
    }

    // DELETE /delete/disciplinas/{id}
    public function delete($id) {
        $resultado = $this->model->delete($id);

        if (isset($resultado['erro'])) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }
}