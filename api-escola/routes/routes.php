<?php

require_once __DIR__ . '/../controllers/AlunoController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$alunoController = new AlunoController();

// Rota GET /list/alunos
if ($uri === '/list/alunos' && $method === 'GET') {
    $alunoController->index();

// Rota POST /create/alunos
} elseif ($uri === '/create/alunos' && $method === 'POST') {
    $alunoController->store();

}
// PUT /update/alunos/{id}
elseif (preg_match('/\/update\/alunos\/(\d+)/', $uri, $matches) && $method === 'PUT') {
    $id = $matches[1];
    $alunoController->update($id);
} 
// DELETE /delete/alunos/{id}
elseif ($method === 'DELETE' && preg_match('#^/delete/alunos/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $alunoController->delete($id);
    return;
}else {
    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada"]);
}
