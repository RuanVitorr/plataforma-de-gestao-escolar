<?php

require_once __DIR__ . '/../models/Aluno.php';

class AlunoController {

    private $model;

    public function __construct() {
        $this->model = new Aluno();
    }

    // GET /alunos
    public function index() {
        $alunos = $this->model->getAll();
        echo json_encode($alunos);
    }

    
    // POST /alunos
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação simples
        if (!$data || !isset($data['nome']) || !isset($data['email']) || !isset($data['rgm'])) {
            echo json_encode(['erro' => 'Dados inválidos. Nome, email e RGM são obrigatórios.']);
            return;
        }

        // Cria o aluno passando os três parâmetros
        $aluno = $this->model->create($data['nome'], $data['email'], $data['rgm']);

        if ($aluno) {
            echo json_encode($aluno);
        } else {
            echo json_encode(['erro' => 'Falha ao criar aluno']);
        }
    }

    
    // PUT /update/alunos/{id}
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação simples
        if (!$data || !isset($data['nome']) || !isset($data['email']) || !isset($data['rgm'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos. Nome, email e RGM são obrigatórios.']);
            return;
        }

        $alunoAtualizado = $this->model->update($id, $data['nome'], $data['email'], $data['rgm']);

        if ($alunoAtualizado) {
            echo json_encode($alunoAtualizado);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Aluno não encontrado']);
        }
    }

    // DELETE /delete/alunos/{id}
    public function delete($id) {
        $resultado = $this->model->delete($id);
        echo json_encode($resultado);
    }


}