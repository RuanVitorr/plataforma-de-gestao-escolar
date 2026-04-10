<?php

require_once __DIR__ . '/../models/Boletim.php';

class BoletimController {
    private $model;

    public function __construct() {
        $this->model = new Boletim();
    }

    // GET /boletim/{aluno_id}
    public function show($aluno_id) {
        if (!$this->model->alunoExiste($aluno_id)) {
            http_response_code(404);
            echo json_encode(['erro' => 'Aluno não encontrado']);
            return;
        }

        $boletim = $this->model->getBoletimByAlunoId($aluno_id);

        if (!$boletim || count($boletim) === 0) {
            echo json_encode([
                "mensagem" => "Aluno encontrado, mas não possui disciplinas matriculadas",
                "aluno_id" => (int) $aluno_id,
                "disciplinas" => []
            ]);
            return;
        }

        $aluno = [
            "id" => (int) $boletim[0]['aluno_id'],
            "nome" => $boletim[0]['aluno_nome'],
            "email" => $boletim[0]['aluno_email'],
            "rgm" => $boletim[0]['rgm']
        ];

        $disciplinas = [];

        foreach ($boletim as $item) {
            $disciplinas[] = [
                "disciplina_id" => (int) $item['disciplina_id'],
                "nome" => $item['disciplina_nome'],
                "codigo" => $item['disciplina_codigo'],
                "nota1" => $item['nota1'] !== null ? (float) $item['nota1'] : null,
                "nota2" => $item['nota2'] !== null ? (float) $item['nota2'] : null,
                "media" => $item['media'] !== null ? (float) $item['media'] : null
            ];
        }

        echo json_encode([
            "aluno" => $aluno,
            "disciplinas" => $disciplinas
        ]);
    }
}