<?php

require_once __DIR__ . '/../services/contenido/juego/clasificaHabitosService.php';

class ClasificaHabitosController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    private const FILE_GET_CONTENTS = 'php://input';

    private ClasificaHabitosService $service;

    public function __construct(ClasificaHabitosService $service)
    {
        $this->service = $service;
    }

    public function obtenerDatosJuego(int $juegoId): void
    {
        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->obtenerDatosJuego($juegoId);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function crearCategoria(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        $resultado = $this->service->crearCategoria($datos);

        echo json_encode($resultado);
    }

    public function crearItem(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        $resultado = $this->service->crearItem($datos);

        echo json_encode($resultado);
    }
}
