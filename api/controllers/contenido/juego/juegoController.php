<?php

require_once __DIR__ . '/../services/contenido/juego/juegoService.php';
require_once __DIR__ . '/../models/contenido/juego/juego.php';

class JuegoController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    private const FILE_GET_CONTENTS = 'php://input';

    private JuegoService $service;

    public function __construct(JuegoService $service)
    {
        $this->service = $service;
    }

    public function obtenerJuegos(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->obtenerJuegos();

        echo json_encode($resultado);
    }

    public function obtenerJuego(string $codigo): void
    {
        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->obtenerPorCodigo($codigo);

        if (!$resultado['success']) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }

    public function crearJuego(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            return;
        }

        $resultado = $this->service->crearJuego($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }
}
