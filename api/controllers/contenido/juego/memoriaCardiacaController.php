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
        
        if ($juegoId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID juego inválido']);
            return;
        }

        $resultado = $this->service->obtenerCartas($juegoId);
        echo json_encode($resultado);
    }

    public function crearJuegoCompleto(array $usuario): void
    {
        header(self::CONTENT_TYPE_JSON);

        if ($usuario['rol'] !== 'Administrador') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'No tienes permisos para crear juegos'
            ]);
            return;
        }

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
            return;
        }

        $resultado = $this->service->crearJuegoCompleto($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        } else {
            http_response_code(201);
        }

        echo json_encode($resultado);
    }

    public function actualizarJuegoCompleto(array $usuario): void
    {
        header(self::CONTENT_TYPE_JSON);

        if ($usuario['rol'] !== 'Administrador') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'No tienes permisos para actualizar juegos'
            ]);
            return;
        }

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
            return;
        }

        $resultado = $this->service->actualizarJuegoCompleto($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }
}
