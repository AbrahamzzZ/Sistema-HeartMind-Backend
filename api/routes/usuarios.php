<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/usuario.php';
require_once __DIR__ . '/../repositories/usuarioRepository.php';
require_once __DIR__ . '/../services/usuarioService.php';
require_once __DIR__ . '/../controllers/usuarioController.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

$db = Conexion::obtenerConexion();
$repository = new UsuarioRepository($db);
$service = new UsuarioService($repository);
$controller = new UsuarioController($service);
$method = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? null;

switch ($method) {

    case 'GET':
        if ($accion === 'perfil') {

            try {
                AuthMiddleware::validarToken();
                $controller->obtenerPerfil();
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        break;

    case 'POST':
        switch ($accion) {
            case 'registro':
                $controller->registrar();
                break;
            
            case 'login':
                $controller->login();
                break;

            default:
                http_response_code(400);
                echo json_encode(['message' => 'Acción inválida.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido.']);
}
