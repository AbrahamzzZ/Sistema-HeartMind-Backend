<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

require_once __DIR__ . '/../repositories/contenido/juego/juegoRepository.php';
require_once __DIR__ . '/../services/contenido/juego/juegoService.php';
require_once __DIR__ . '/../controllers/contenido/juego/juegoController.php';

require_once __DIR__ . '/../repositories/contenido/juego/clasificaHabitosRepository.php';
require_once __DIR__ . '/../services/contenido/juego/clasificaHabitosService.php';
require_once __DIR__ . '/../controllers/contenido/juego/clasificaHabitosController.php';

require_once __DIR__ . '/../repositories/contenido/juego/memoriaCardiacaRepository.php';
require_once __DIR__ . '/../services/contenido/juego/memoriaCardiacaService.php';
require_once __DIR__ . '/../controllers/contenido/juego/memoriaCardiacaController.php';


$db = Conexion::obtenerConexion();
$juegoController = new JuegoController(new JuegoService(new JuegoRepository($db)));
$clasificaController = new ClasificaHabitosController(new ClasificaHabitosService(new ClasificaHabitosRepository($db)));
$memoriaController = new MemoriaCardiacaController(new MemoriaCardiacaService(new MemoriaCardiacaRepository($db)));

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

        // juegos
        if (isset($_GET['juego'])) {
            $juegoController->obtenerJuego($_GET['juego']);
            break;
        }

        if (isset($_GET['listar'])) {
            $juegoController->obtenerJuegos();
            break;
        }

        // clasifica hábitos
        if (isset($_GET['clasifica_id'])) {
            $clasificaController->obtenerDatosJuego((int) $_GET['clasifica_id']);
            break;
        }

        // memoria
        if (isset($_GET['memoria_id'])) {
            $memoriaController->obtenerCartas((int) $_GET['memoria_id']);
            break;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        break;

    case 'POST':

        // juegos
        if ($accion === 'crear-juego') {
            $juegoController->crearJuego();
            break;
        }

        // clasifica
        if ($accion === 'clasifica-crear-completo') {
            $clasificaController->crearJuegoCompleto();
            break;
        }

        if ($accion === 'clasifica-actualizar-completo') {
            $clasificaController->actualizarJuegoCompleto();
            break;
        }

        // memoria
        if ($accion === 'memoria-crear-completo') {
            $memoriaController->crearJuegoCompleto();
            break;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción inválida']);
        break;

    case 'PUT':

        // memoria
        if ($accion === 'memoria-actualizar-completo') {
            $memoriaController->actualizarJuegoCompleto();
            break;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción inválida']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false,'message' => 'Método no permitido']);
}
