<?php
 
use App\Database\Mariadb;
use App\Models\Tarefa;
use App\Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
 
require __DIR__ . '/vendor/autoload.php';
 
$app = AppFactory::create();
$banco = new Mariadb();
 
$app->get('/usuario/{id}/tarefa',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $user_id = $args['id'];
    $tarefa = new Tarefa($banco->getConnection());
    $tarefas = $tarefa->getAllByUser($user_id);
    $response->getBody()->write(json_encode($tarefas));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// cadastra usuário
$app->post('/usuario', function(Request $request, Response $response, array $args) use ($banco)
 {
    $campos_obrigatórios = ['nome', "login", 'senha', "email"];
    $body = $request->getParsedBody();
 
    try{
        $usuario = new Usuario($banco->getConnection());
        $usuario->nome = $body['nome'] ?? '';
        $usuario->email = $body['email'] ?? '';
        $usuario->login = $body['login'] ?? '';
        $usuario->senha = $body['senha'] ?? '';
        $usuario->foto_path = $body['foto_path'] ?? '';
        foreach($campos_obrigatórios as $campo){
            if(empty($usuario->{$campo})){
                throw new \Exception("o campo {$campo} é obrigatório");
            }
        }
        $usuario->create();
    }catch(\Exception $exception){
         $response->getBody()->write(json_encode(['message' => $exception->getMessage() ]));
         return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
 
    $response->getBody()->write(json_encode([
        'message' => 'Usuário cadastrado com sucesso!'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// listando usuário
$app->get('/usuario/{id}',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $id = $args['id'];
    $usuario = new Usuario($banco->getConnection());
    $usuarios = $usuario->getUsuarioById($id);
    $response->getBody()->write(json_encode($usuarios));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// Atualizar usuário
$app->put('/usuario/{id}',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $campos_obrigatórios = ['nome', "login", 'senha', "email"];
    $body = json_decode($request->getBody()->getContents(), true);
 
    try{
        $usuario = new Usuario($banco->getConnection());
        $usuario->id = $args['id'];
        $usuario->nome = $body['nome'] ?? '';
        $usuario->email = $body['email'] ?? '';
        $usuario->login = $body['login'] ?? '';
        $usuario->senha = $body['senha'] ?? '';
        $usuario->foto_path = $body['foto_path'] ?? '';
        foreach($campos_obrigatórios as $campo){
            if(empty($usuario->{$campo})){
                throw new \Exception("o campo {$campo} é obrigatório");
            }
        }
        $usuario->update();
    }catch(\Exception $exception){
         $response->getBody()->write(json_encode(['message' => $exception->getMessage() ]));
         return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
 
    $response->getBody()->write(json_encode([
        'message' => 'Usuário autalizado com sucesso!'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// deletando usuário
$app->delete('/usuario/{id}',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $id = $args['id'];
    $usuario = new Usuario($banco->getConnection());
    $usuario->delete($id);
    $response->getBody()->write(json_encode(['message' => 'Usuário excluído']));
    return $response->withHeader('Content-Type', 'application/json');
});
 
 
 
// cadastra tarefa
$app->post('/tarefas', function(Request $request, Response $response, array $args) use ($banco)
 {
    $campos_obrigatórios = ['titulo', "descricao", 'status', "user_id"];
    $body = $request->getParsedBody();
 
    try{
        $tarefas = new Tarefa($banco->getConnection());
        $tarefas->titulo = $body['titulo'] ?? '';
        $tarefas->descricao = $body['descricao'] ?? '';
        $tarefas->status = $body['status'] ?? '';
        $tarefas->user_id = $body['user_id'] ?? '';
        foreach($campos_obrigatórios as $campo){
            if(empty($tarefas->{$campo})){
                throw new \Exception("o campo {$campo} é obrigatório");
            }
        }
        $tarefas->create();
    }catch(\Exception $exception){
         $response->getBody()->write(json_encode(['message' => $exception->getMessage() ]));
         return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
 
    $response->getBody()->write(json_encode([
        'message' => 'tarefa cadastrado com sucesso!'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// listando tarefa
$app->get('/tarefas/{id}',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $id = $args['id'];
    $tarefas = new Tarefa($banco->getConnection());
    $tarefas = $tarefas->gettarefasById($id);
    $response->getBody()->write(json_encode($tarefas));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// Atualizar tarefa
$app->put('/tarefas/{id}',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $campos_obrigatórios = ['titulo', "descricao", 'status', "user_id"];
    $body = json_decode($request->getBody()->getContents(), true);
 
    try{
        $tarefas = new Tarefa($banco->getConnection());
        $tarefas->id = $args['id'];
        $tarefas->titulo = $body['titulo'] ?? '';
        $tarefas->descricao = $body['descricao'] ?? 0;
        $tarefas->status = $body['status'] ?? '';
        $tarefas->user_id = $body['user_id'] ?? 0;
        foreach($campos_obrigatórios as $campo){
            if(empty($tarefas->{$campo})){
                throw new \Exception("o campo {$campo} é obrigatório");
            }
        }
        $tarefas->update();
    }catch(\Exception $exception){
         $response->getBody()->write(json_encode(['message' => $exception->getMessage() ]));
         return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
 
    $response->getBody()->write(json_encode([
        'message' => 'tarefa autalizada com sucesso!'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
 
// deletando tarefa
$app->delete('/tarefas/{id}',
    function(Request $request, Response $response, array $args) use ($banco)
 {
    $id = $args['id'];
    $tarefas = new Tarefa($banco->getConnection());
    $tarefas->delete($id);
    $response->getBody()->write(json_encode(['message' => 'tarefa excluída']));
    return $response->withHeader('Content-Type', 'application/json');
});
 
 
 
$app->run();