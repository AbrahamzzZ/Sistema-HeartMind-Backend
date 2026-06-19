<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

require_once __DIR__ . '/../models/cuestionario/cuestionario.php';
require_once __DIR__ . '/../models/cuestionario/resultadoCuestionario.php';

require_once __DIR__ . '/../repositories/cuestionario/cuestionarioRepository.php';
require_once __DIR__ . '/../repositories/cuestionario/resultadoCuestionarioRepository.php';
require_once __DIR__ . '/../repositories/cuestionario/preguntaCuestionarioRepository.php';
require_once __DIR__ . '/../repositories/cuestionario/opcionRespuestaRepository.php';

require_once __DIR__ . '/../services/cuestionarioService.php';
require_once __DIR__ . '/../controllers/cuestionarioController.php';

$db = Conexion::obtenerConexion();

$cuestionarioRepository = new CuestionarioRepository($db);
$preguntaRepository = new PreguntaCuestionarioRepository($db);
$opcionRepository = new OpcionRespuestaRepository($db);
$resultadoRepository = new ResultadoCuestionarioRepository($db);

$service = new CuestionarioService($cuestionarioRepository, $preguntaRepository, $opcionRepository, $resultadoRepository);

$controller = new CuestionarioController($service);
$method = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? null;

try {
    $usuario = AuthMiddleware::validarToken();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    return;
}

switch ($method) {

    case 'GET':

        if (isset($_GET['id'])) {

            $controller->obtenerCuestionario((int) $_GET['id']);

        } elseif (isset($_GET['usuarioId'])) {

            $usuarioId = $usuario->usuarioId;

            if ($usuario->rol === 'Administrador') {
                $usuarioId = (int) $_GET['usuarioId'];
            }

            $controller->obtenerHistorial($usuarioId);

        } else {

            $controller->obtenerCuestionarios();
        }

        break;

    case 'POST':

        switch ($accion) {

            case 'resolver':
                $controller->resolverCuestionario((int) $usuario->usuarioId);

                break;

            case 'crear-completo':
                AuthMiddleware::validarRol('Administrador');
                $controller->crearCuestionarioCompleto();

                break;

            default:
                http_response_code(400);
                echo json_encode([ 'success' => false, 'message' => 'Acción inválida.']);
        }
        break;

    case 'PUT':
        if ($accion !== 'actualizar-completo') {

            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción inválida.']);
            break;
        }

        AuthMiddleware::validarRol('Administrador');
        $controller->actualizarCuestionarioCompleto();

        break;
            
    case 'DELETE':

        if ($accion !== 'eliminar-cuestionario') {

            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción inválida.']);
            break;
        }
        AuthMiddleware::validarRol('Administrador');

        if (!isset($_GET['id'])) {

            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'El id es obligatorio.']);
            break;
        }
        $controller->eliminarCuestionario((int) $_GET['id']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
