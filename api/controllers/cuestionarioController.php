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

    public function obtenerCuestionarios(): void {
        header(self::CONTENT_TYPE_JSON);
        $cuestionarios = $this->service->obtenerCuestionarios();
        $response = ['success' => true, 'data' => $cuestionarios];

        if (empty($cuestionarios)) {
            $response['message'] = 'No hay información que mostrar.';
        }

        echo json_encode($response);
    }

    public function obtenerCuestionario(int $cuestionarioId): void {
        header(self::CONTENT_TYPE_JSON);
        $cuestionario = $this->service->obtenerCuestionarioCompleto($cuestionarioId);

        if (!$cuestionario) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cuestionario no encontrado.']);

            return;
        }

        echo json_encode(['success' => true, 'data' => $cuestionario]);
    }

    public function resolverCuestionario(int $usuarioId): void{
        header(self::CONTENT_TYPE_JSON);
        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos || !isset($datos['cuestionarioId'], $datos['respuestas'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);

            return;
        }

        $resultado = $this->service->resolverCuestionario($usuarioId, $datos['cuestionarioId'], $datos['respuestas']);

        if (isset($resultado['success']) && $resultado['success'] === false) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $resultado['message'] ?? 'Error al resolver el cuestionario.']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $resultado]);
    }

    public function obtenerHistorial(int $usuarioId): void {
        header(self::CONTENT_TYPE_JSON);
        $historial = $this->service->obtenerHistorial($usuarioId);
        $response = ['success' => true, 'data' => $historial];

        if (empty($historial)) {
            $response['message'] = 'No hay información que mostrar.';
        }

        echo json_encode($response);
    }

    public function crearCuestionarioCompleto(): void{
        header(self::CONTENT_TYPE_JSON);
        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);
        if (!$datos) {
            http_response_code(400);
            echo json_encode(['success' => false,'message' => 'Datos inválidos.']);
            return;
        }

        $resultado = $this->service->crearCuestionarioCompleto($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function actualizarCuestionarioCompleto(): void {
        header(self::CONTENT_TYPE_JSON);
        $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);

        if (!$datos) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            return;
        }

        if (!isset($datos['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'El id del cuestionario es obligatorio.']);
            return;
        }

        $resultado = $this->service->actualizarCuestionarioCompleto($datos);

        if (!$resultado['success']) {
            http_response_code(400);
        }

        echo json_encode($resultado);
    }

    public function eliminarCuestionario(int $id): void {
        header(self::CONTENT_TYPE_JSON);
        $resultado = $this->service->eliminarCuestionario($id);

        if (!$resultado['success']) {
            http_response_code(404);
            echo json_encode($resultado);
            return;
        }

        echo json_encode($resultado);
    }
}
