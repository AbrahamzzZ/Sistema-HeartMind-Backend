<?php

require_once __DIR__ . '/../services/contenido/juego/froggyCardioService.php';

class FroggyCardioController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    private const FILE_GET_CONTENTS = 'php://input';

    private FroggyCardioService $service;

    public function __construct(FroggyCardioService $service)
    {
        $this->service = $service;
    }

    // =========================
    // OBTENER EVENTOS DEL JUEGO
    // =========================
    public function obtenerEventos(int $juegoId): void
    {
        header(self::CONTENT_TYPE_JSON);

        if ($juegoId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Juego inválido'
            ]);
            return;
        }

        $resultado = $this->service->obtenerEventos($juegoId);

        echo json_encode($resultado);
    }

    // =========================
    // ENVIAR RESULTADO DEL JUEGO
    // =========================
    public function enviarResultado(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (
            !$datos ||
            !isset($datos['usuario_id'], $datos['juego_id'], $datos['puntaje'], $datos['tiempo'])
        ) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            return;
        }

        // reutilizas el flujo de sesión si quieres
        echo json_encode([
            'success' => true,
            'data' => $datos,
            'message' => 'Resultado recibido (implementar service si quieres persistir)'
        ]);
    }
}
