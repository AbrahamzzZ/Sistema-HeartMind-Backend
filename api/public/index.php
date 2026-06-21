<?php

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$ruta = $_GET['ruta'] ?? '';

switch ($ruta) {

    case 'usuarios':
        require_once __DIR__ . '/../routes/usuarios.php';
        break;

    case 'evaluaciones':
        require_once __DIR__ . '/../routes/evaluaciones.php';
        break;

    case 'cuestionarios':
        require_once __DIR__ . '/../routes/cuestionarios.php';
        break;

    case 'contenidos':
        require_once __DIR__ . '/../routes/contenidos.php';
        break;

    case 'juegos':
        require_once __DIR__ . '/../routes/juegos.php';
        break;

    default:
        http_response_code(404);

        echo json_encode([
            'mensaje' => 'Ruta no encontrada'
        ]);
}
