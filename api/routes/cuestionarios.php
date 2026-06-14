<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

require_once __DIR__ . '/../models/cuestionario.php';
require_once __DIR__ . '/../models/preguntaCuestionario.php';
require_once __DIR__ . '/../models/opcionRespuesta.php';
require_once __DIR__ . '/../models/resultadoCuestionario.php';

require_once __DIR__ . '/../repositories/cuestionarioRepository.php';
require_once __DIR__ . '/../repositories/preguntaCuestionarioRepository.php';
require_once __DIR__ . '/../repositories/opcionRespuestaRepository.php';
require_once __DIR__ . '/../repositories/resultadoCuestionarioRepository.php';

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
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    return;
}

switch ($method) {

    case 'GET':

        if (isset($_GET['id'])) {
            $controller->obtenerCuestionario((int) $_GET['id']);

        } elseif (($accion ?? null) === 'historial' || isset($_GET['usuarioId'])) {
            $usuarioId = $usuario->usuarioId;
            if (isset($_GET['usuarioId']) && $usuario->rol === 'Administrador') {
                $usuarioId = (int) $_GET['usuarioId'];
            }

            $controller->obtenerHistorial((int) $usuarioId);

        } else {

            $controller->obtenerCuestionarios();
        }

        break;

    case 'POST':

        switch ($accion) {

            case 'resolver':
                $controller->resolverCuestionario((int) $usuario->usuarioId);
                break;

            case 'crear-cuestionario':
                AuthMiddleware::validarRol('Administrador');
                $controller->crearCuestionario();
                break;

            case 'crear-completo':
                AuthMiddleware::validarRol('Administrador');
                $controller->crearCuestionarioCompleto();
                break;

            case 'crear-pregunta':
                AuthMiddleware::validarRol('Administrador');
                $controller->crearPregunta();
                break;

            case 'crear-opcion':
                AuthMiddleware::validarRol('Administrador');
                $controller->crearOpcion();
                break;

            default:

                http_response_code(400);

                echo json_encode([
                    'message' => 'Acción inválida.'
                ]);
        }

        break;

    case 'PUT':

        switch ($accion) {

            case 'actualizar-cuestionario':
                AuthMiddleware::validarRol('Administrador');
                $controller->actualizarCuestionario();
                break;

            case 'actualizar-pregunta':
                AuthMiddleware::validarRol('Administrador');
                $controller->actualizarPregunta();
                break;

            case 'actualizar-opcion':
                AuthMiddleware::validarRol('Administrador');
                $controller->actualizarOpcion();
                break;

            default:

                http_response_code(400);

                echo json_encode([
                    'message' => 'Acción inválida.'
                ]);
        }

        break;

    case 'DELETE':

        switch ($accion) {

            case 'eliminar-cuestionario':
                AuthMiddleware::validarRol('Administrador');
                $controller->eliminarCuestionario((int) $_GET['id']);
                break;

            case 'eliminar-pregunta':
                AuthMiddleware::validarRol('Administrador');
                $controller->eliminarPregunta((int) $_GET['id']);
                break;

            case 'eliminar-opcion':
                AuthMiddleware::validarRol('Administrador');
                $controller->eliminarOpcion((int) $_GET['id']);
                break;

            default:

                http_response_code(400);

                echo json_encode([
                    'message' => 'Acción inválida.'
                ]);
        }

        break;

    default:

        http_response_code(405);

        echo json_encode([
            'message' => 'Método no permitido.'
        ]);
}
