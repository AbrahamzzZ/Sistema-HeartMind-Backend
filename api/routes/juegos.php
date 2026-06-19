<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

require_once __DIR__ . '/../repositories/juegoRepository.php';
require_once __DIR__ . '/../services/juego/juegoService.php';
require_once __DIR__ . '/../controllers/juego/juegoController.php';

require_once __DIR__ . '/../repositories/juegoSesionRepository.php';
require_once __DIR__ . '/../services/juego/juegoSesionService.php';
require_once __DIR__ . '/../controllers/juego/juegoSesionController.php';

require_once __DIR__ . '/../repositories/clasificaHabitosRepository.php';
require_once __DIR__ . '/../services/juego/clasificaHabitosService.php';
require_once __DIR__ . '/../controllers/juego/clasificaHabitosController.php';

require_once __DIR__ . '/../repositories/memoriaCardiacaRepository.php';
require_once __DIR__ . '/../services/juego/memoriaCardiacaService.php';
require_once __DIR__ . '/../controllers/juego/memoriaCardiacaController.php';

require_once __DIR__ . '/../repositories/froggyCardioRepository.php';
require_once __DIR__ . '/../services/juego/froggyCardioService.php';
require_once __DIR__ . '/../controllers/juego/froggyCardioController.php';

$db = Conexion::obtenerConexion();
$juegoController = new JuegoController(new JuegoService(new JuegoRepository($db)));
$sesionController = new JuegoSesionController(new JuegoSesionService(new JuegoSesionRepository($db)));
$clasificaController = new ClasificaHabitosController(new ClasificaHabitosService(new ClasificaHabitosRepository($db)));
$memoriaController = new MemoriaCardiacaController(new MemoriaCardiacaService(new MemoriaCardiacaRepository($db)));
$froggyController = new FroggyCardioController(new FroggyCardioService(new FroggyCardioRepository($db)));

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

        // froggy
        if (isset($_GET['froggy_id'])) {
            $froggyController->obtenerEventos((int) $_GET['froggy_id']);
            break;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        break;

    case 'POST':

        // sesión de juego
        if ($accion === 'iniciar') {
            $sesionController->iniciarJuego();
            break;
        }

        if ($accion === 'finalizar') {
            $sesionController->finalizarJuego();
            break;
        }

        // clasifica
        if ($accion === 'categoria') {
            $clasificaController->crearCategoria();
            break;
        }

        if ($accion === 'item') {
            $clasificaController->crearItem();
            break;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción inválida']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false,'message' => 'Método no permitido']);
}