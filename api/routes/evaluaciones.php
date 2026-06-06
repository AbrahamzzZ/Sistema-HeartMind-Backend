<?php

require_once '../config/conexion.php';

require_once '../models/evaluacionRiesgo.php';

require_once '../repositories/evaluacionRiesgoRepository.php';

require_once '../services/evaluacionRiesgoService.php';

require_once '../controllers/evaluacionRiesgoController.php';

$repository = new EvaluacionRiesgoRepository($pdo);

$service = new EvaluacionRiesgoService();

$controller = new EvaluacionRiesgoController(
    $service,
    $repository
);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $controller->evaluar();
}
