<?php

require_once __DIR__ . '/../config/conexion.php';

require_once __DIR__ . '/../models/usuario.php';

require_once __DIR__ . '/../repositories/usuarioRepository.php';

require_once __DIR__ . '/../services/usuarioService.php';

require_once __DIR__ . '/../controllers/usuarioController.php';

$repository = new UsuarioRepository($pdo);

$service = new UsuarioService(
    $repository
);

$controller = new UsuarioController(
    $service
);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $controller->registrar();
}
