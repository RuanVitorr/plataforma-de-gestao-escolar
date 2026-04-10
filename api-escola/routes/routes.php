<?php

require_once __DIR__ . '/../controllers/AlunoController.php';
require_once __DIR__ . '/../controllers/DisciplinaController.php';
require_once __DIR__ . '/../controllers/ProfessorController.php';
require_once __DIR__ . '/../controllers/MatriculaController.php';
require_once __DIR__ . '/../controllers/NotaController.php';
require_once __DIR__ . '/../controllers/BoletimController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$alunoController = new AlunoController();
$disciplinaController = new DisciplinaController();
$professorController = new ProfessorController();
$matriculaController = new MatriculaController();
$notaController = new NotaController();
$boletimController = new BoletimController();

// =========================
// ROTAS DE ALUNOS
// =========================
if ($uri === '/list/alunos' && $method === 'GET') {
    $alunoController->index();

} elseif ($method === 'GET' && preg_match('#^/alunos/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $alunoController->show($id);

} elseif ($uri === '/create/alunos' && $method === 'POST') {
    $alunoController->store();

} elseif ($method === 'PUT' && preg_match('#^/update/alunos/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $alunoController->update($id);

} elseif ($method === 'DELETE' && preg_match('#^/delete/alunos/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $alunoController->delete($id);

// =========================
// ROTAS DE DISCIPLINAS
// =========================
} elseif ($uri === '/list/disciplinas' && $method === 'GET') {
    $disciplinaController->index();

} elseif ($method === 'GET' && preg_match('#^/disciplinas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $disciplinaController->show($id);

} elseif ($uri === '/create/disciplinas' && $method === 'POST') {
    $disciplinaController->store();

} elseif ($method === 'PUT' && preg_match('#^/update/disciplinas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $disciplinaController->update($id);

} elseif ($method === 'DELETE' && preg_match('#^/delete/disciplinas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $disciplinaController->delete($id);

// =========================
// ROTAS DE PROFESSORES
// =========================
} elseif ($uri === '/list/professores' && $method === 'GET') {
    $professorController->index();

} elseif ($method === 'GET' && preg_match('#^/professores/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $professorController->show($id);

} elseif ($uri === '/create/professores' && $method === 'POST') {
    $professorController->store();

} elseif ($method === 'PUT' && preg_match('#^/update/professores/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $professorController->update($id);

} elseif ($method === 'DELETE' && preg_match('#^/delete/professores/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $professorController->delete($id);

// =========================
// ROTAS DE MATRÍCULAS
// =========================
} elseif ($uri === '/aluno_disciplina' && $method === 'GET') {
    $matriculaController->index();

} elseif ($method === 'GET' && preg_match('#^/aluno_disciplina/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $matriculaController->show($id);

} elseif ($uri === '/aluno_disciplina' && $method === 'POST') {
    $matriculaController->store();

} elseif ($method === 'DELETE' && preg_match('#^/aluno_disciplina/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $matriculaController->delete($id);

// =========================
// ROTAS DE NOTAS
// =========================
} elseif ($uri === '/notas' && $method === 'GET') {
    $notaController->index();

} elseif ($method === 'GET' && preg_match('#^/notas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $notaController->show($id);

} elseif ($uri === '/notas' && $method === 'POST') {
    $notaController->store();

} elseif ($method === 'PUT' && preg_match('#^/notas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $notaController->update($id);

} elseif ($method === 'DELETE' && preg_match('#^/notas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $notaController->delete($id);

// =========================
// ROTA DE BOLETIM
// =========================
} elseif ($method === 'GET' && preg_match('#^/boletim/(\d+)$#', $uri, $matches)) {
    $aluno_id = $matches[1];
    $boletimController->show($aluno_id);

} else {
    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada"]);
}