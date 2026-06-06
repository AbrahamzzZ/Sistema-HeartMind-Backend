<?php

require_once __DIR__ . '/../config/conexion.php';

require_once __DIR__ . '/../models/EvaluacionRiesgo.php';

require_once __DIR__ . '/../repositories/EvaluacionRiesgoRepository.php';

require_once __DIR__ . '/../services/EvaluacionRiesgoService.php';

require_once __DIR__ . '/../controllers/EvaluacionRiesgoController.php';

$db = Conexion::obtenerConexion();
$repository = new EvaluacionRiesgoRepository($db);
$service = new EvaluacionRiesgoService($repository);
$controller = new EvaluacionRiesgoController($service);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':

        if (isset($_GET['usuarioId'])) {

            $controller->obtenerHistorial(
                (int) $_GET['usuarioId']
            );

        } else {

            http_response_code(400);

            echo json_encode([
                'message' => 'Debe enviar usuarioId.'
            ]);
        }

        break;

    case 'POST':

        $controller->evaluar();

        break;

    default:

        http_response_code(405);

        echo json_encode([
            'message' => 'Método no permitido.'
        ]);
}
