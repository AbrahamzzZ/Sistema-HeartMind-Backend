<?php

require_once __DIR__ . '/../services/contenidoService.php';
require_once __DIR__ . '/../models/contenido.php';

class ContenidoController{
    private const CONTENT_TYPE_JSON ='Content-Type: application/json';
    private const FILE_GET_CONTENTS ='php://input';
    private ContenidoService $service;

    public function __construct(
        ContenidoService $service
    ){
        $this->service = $service;
    }

    public function obtenerContenidos(): void
    {
        header(self::CONTENT_TYPE_JSON);

        echo json_encode(
            $this->service->obtenerContenidos()
        );
    }

    public function obtenerContenido(
        int $id
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->obtenerContenido($id);

        if (!$resultado['success']) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }

    public function crearContenido(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $contenido = new Contenido($datos);

        $resultado = $this->service->crearContenido(
            $contenido
        );

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function actualizarContenido(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $contenido = new Contenido($datos);

        $resultado = $this->service->actualizarContenido(
            $contenido
        );

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function eliminarContenido(
        int $id
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->eliminarContenido(
            $id
        );

        if (!$resultado['success']) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }
}
