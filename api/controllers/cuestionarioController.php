<?php

require_once __DIR__ . '/../services/cuestionarioService.php';
require_once __DIR__ . '/../models/cuestionario.php';
require_once __DIR__ . '/../models/preguntaCuestionario.php';
require_once __DIR__ . '/../models/opcionRespuesta.php';
require_once __DIR__ . '/../models/resultadoCuestionario.php';

class CuestionarioController
{
    private const CONTENT_TYPE_JSON ='Content-Type: application/json';
    private const FILE_GET_CONTENTS ='php://input';
    private CuestionarioService $service;

    public function __construct(
        CuestionarioService $service
    ) {
        $this->service = $service;
    }

    public function obtenerCuestionarios(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $cuestionarios = $this->service->obtenerCuestionarios();

        echo json_encode([
            'success' => true,
            'data' => $cuestionarios
        ]);
    }

    public function obtenerCuestionario(
        int $cuestionarioId
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $cuestionario = $this->service->obtenerCuestionarioCompleto($cuestionarioId);

        if (!$cuestionario) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' =>
                    'Cuestionario no encontrado.'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $cuestionario
        ]);
    }

    public function resolverCuestionario(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        if (!$datos) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos.'
            ]);

            return;
        }

        $resultado = $this->service->resolverCuestionario(
            $datos['usuarioId'],
            $datos['cuestionarioId'],
            $datos['respuestas']
        );

        echo json_encode([
            'success' => true,
            'data' => $resultado
        ]);
    }

    public function obtenerHistorial(
        int $usuarioId
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $historial = $this->service->obtenerHistorial($usuarioId);

        echo json_encode([
            'success' => true,
            'data' => $historial
        ]);
    }

    // CUESTIONARIOS

    public function crearCuestionario(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $cuestionario = new Cuestionario($datos);
        $resultado = $this->service->crearCuestionario($cuestionario);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    public function actualizarCuestionario(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $cuestionario = new Cuestionario( $datos);
        $resultado = $this->service->actualizarCuestionario($cuestionario);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    public function eliminarCuestionario(
        int $id
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->eliminarCuestionario($id);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    // PREGUNTAS

    public function crearPregunta(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $pregunta = new PreguntaCuestionario($datos);
        $resultado = $this->service->crearPregunta($pregunta);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    public function actualizarPregunta(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $pregunta = new PreguntaCuestionario($datos);
        $resultado = $this->service->actualizarPregunta($pregunta);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    public function eliminarPregunta(
        int $id
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->eliminarPregunta($id);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    // OPCIONES

    public function crearOpcion(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $opcion = new OpcionRespuesta($datos);
        $resultado = $this->service->crearOpcion($opcion);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    public function actualizarOpcion(): void
    {
        header(self::CONTENT_TYPE_JSON);

        $datos = json_decode(
            file_get_contents(self::FILE_GET_CONTENTS),
            true
        );

        $opcion = new OpcionRespuesta($datos);
        $resultado = $this->service->actualizarOpcion($opcion);

        echo json_encode([
            'success' => $resultado
        ]);
    }

    public function eliminarOpcion(
        int $id
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->eliminarOpcion($id);

        echo json_encode([
            'success' => $resultado
        ]);
    }
}
