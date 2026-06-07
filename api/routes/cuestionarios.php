<?php

require_once __DIR__ . '/../config/conexion.php';

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

switch ($method) {

    case 'GET':

        if (isset($_GET['id'])) {

            $controller->obtenerCuestionario(
                (int) $_GET['id']
            );

        } elseif (isset($_GET['usuarioId'])) {

            $controller->obtenerHistorial(
                (int) $_GET['usuarioId']
            );

        } else {

            $controller->obtenerCuestionarios();
        }

        break;

    case 'POST':

        switch ($accion) {

            case 'resolver':
                $controller->resolverCuestionario();
                break;

            case 'crear-cuestionario':
                $controller->crearCuestionario();
                break;

            case 'crear-pregunta':
                $controller->crearPregunta();
                break;

            case 'crear-opcion':
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
                $controller->actualizarCuestionario();
                break;

            case 'actualizar-pregunta':
                $controller->actualizarPregunta();
                break;

            case 'actualizar-opcion':
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

                $controller->eliminarCuestionario(
                    (int) $_GET['id']
                );

                break;

            case 'eliminar-pregunta':

                $controller->eliminarPregunta(
                    (int) $_GET['id']
                );

                break;

            case 'eliminar-opcion':

                $controller->eliminarOpcion(
                    (int) $_GET['id']
                );

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
