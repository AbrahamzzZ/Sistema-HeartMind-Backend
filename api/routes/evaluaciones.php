<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

require_once __DIR__ . '/../services/EvaluacionRiesgoService.php';

require_once __DIR__ . '/../controllers/EvaluacionRiesgoController.php';

$db = Conexion::obtenerConexion();
$repository = new EvaluacionRiesgoRepository($db);
$service = new EvaluacionRiesgoService($repository);
$controller = new EvaluacionRiesgoController($service);
$method = $_SERVER['REQUEST_METHOD'];

try {
    $usuario = AuthMiddleware::validarToken();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    return;
}

switch ($method) {

    case 'GET':

        if (isset($_GET['todos']) && $_GET['todos'] === 'true' && $usuario->rol === 'Administrador') {
            $controller->obtenerHistoriales();
            break;
        }

        $usuarioId = (int) $usuario->usuarioId;

        if (isset($_GET['usuarioId']) &&  $usuario->rol === 'Administrador') {
            $usuarioId = (int) $_GET['usuarioId'];
        }

        $controller->obtenerHistorial($usuarioId);
        break;

    case 'POST':
        $controller->evaluar((int) $usuario->usuarioId);
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido.']);
}
