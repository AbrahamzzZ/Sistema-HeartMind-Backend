<?php

require_once __DIR__ . '/../services/contenido/juego/juegoSesionService.php';

class JuegoSesionController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    private const FILE_GET_CONTENTS = 'php://input';

    private JuegoSesionService $service;

    public function __construct(JuegoSesionService $service)
    {
        $this->service = $service;
    }

    // =========================
    // INICIAR JUEGO
    // =========================
    public function iniciarJuego(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos || !isset($datos['usuario_id'], $datos['juego_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            return;
        }

        $resultado = $this->service->iniciarJuego(
            $datos['usuario_id'],
            $datos['juego_id']
        );

        echo json_encode($resultado);
    }

    // =========================
    // OBTENER SESIÓN ACTIVA
    // =========================
    public function obtenerSesionActiva(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos || !isset($datos['usuario_id'], $datos['juego_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            return;
        }

        $resultado = $this->service->obtenerSesionActiva(
            $datos['usuario_id'],
            $datos['juego_id']
        );

        echo json_encode($resultado);
    }

    // =========================
    // FINALIZAR JUEGO
    // =========================
    public function finalizarJuego(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (
            !$datos ||
            !isset($datos['sesion_id'], $datos['puntaje'], $datos['tiempo'])
        ) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            return;
        }

        $resultado = $this->service->finalizarJuego(
            $datos['sesion_id'],
            $datos['puntaje'],
            $datos['tiempo']
        );

        echo json_encode($resultado);
    }
}
