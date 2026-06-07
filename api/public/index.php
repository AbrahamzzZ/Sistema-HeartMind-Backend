<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

    default:
        http_response_code(404);

        echo json_encode([
            'mensaje' => 'Ruta no encontrada'
        ]);
}
