<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

require_once __DIR__ . '/../models/contenido.php';
require_once __DIR__ . '/../repositories/contenidoRepository.php';
require_once __DIR__ . '/../services/contenidoService.php';
require_once __DIR__ . '/../controllers/contenidoController.php';

$db = Conexion::obtenerConexion();
$repository = new ContenidoRepository($db);
$service = new ContenidoService($repository);
$controller = new contenidoController($service);
$method = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? null;

try {
    $usuario = AuthMiddleware::validarToken();
} catch (Exception $e){
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    return;
}

switch ($method) {

    case 'GET':

        if (isset($_GET['id'])) {

            $controller->obtenerContenido((int) $_GET['id']);
        } else {

            $controller->obtenerContenidos();
        }

        break;

    case 'POST':
        try {
            AuthMiddleware::validarRol('Administrador');
            $controller->crearContenido();
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'PUT':
        try {
            AuthMiddleware::validarRol('Administrador');
            $controller->actualizarContenido();
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'DELETE':
        try {
            AuthMiddleware::validarRol('Administrador');
            $controller->eliminarContenido(
                (int) $_GET['id']
            );
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido.'
        ]);
}
