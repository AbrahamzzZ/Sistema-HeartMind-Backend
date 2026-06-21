<?php

require_once __DIR__ . '/../../../services/contenido/juego/memoriaCardiacaService.php';

class MemoriaCardiacaController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    private const FILE_GET_CONTENTS = 'php://input';
    private MemoriaCardiacaService $service;

    public function __construct(MemoriaCardiacaService $service)
    {
        $this->service = $service;
    }

    public function obtenerCartas(int $juegoId): void
    {
        header(self::CONTENT_TYPE_JSON);
        $resultado = $this->service->obtenerCartas($juegoId);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function crearJuegoCompleto(): void
    {
        header(self::CONTENT_TYPE_JSON);
        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $resultado = $this->service->crearJuegoCompleto($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function actualizarJuegoCompleto(): void
    {
        header(self::CONTENT_TYPE_JSON);
        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $resultado = $this->service->actualizarJuegoCompleto($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }
}
