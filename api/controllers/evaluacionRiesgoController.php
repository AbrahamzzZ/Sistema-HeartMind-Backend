<?php

require_once __DIR__ . '/../services/EvaluacionRiesgoService.php';

class EvaluacionRiesgoController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    private const FILE_GET_CONTENTS = 'php://input';

    private EvaluacionRiesgoService $service;

    public function __construct(
        EvaluacionRiesgoService $service
    ) {
        $this->service = $service;
    }

    public function evaluar(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {

            http_response_code(400);

            echo json_encode([
                'mensaje' => 'Datos inválidos.'
            ]);

            return;
        }

        $resultado = $this->service->evaluar($datos);

        echo json_encode([
            'success' => true,
            'data' => $resultado
        ]);
    }

    public function obtenerHistorial(
        int $usuarioId
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $evaluaciones = $this->service->obtenerHistorial($usuarioId);

        echo json_encode([
            'success' => true,
            'data' => $evaluaciones
        ]);
    }
}
