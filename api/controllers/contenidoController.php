<?php

require_once __DIR__ . '/../services/contenidoService.php';
require_once __DIR__ . '/../services/cloudinaryService.php';
require_once __DIR__ . '/../models/contenido.php';

class ContenidoController
{
    private const CONTENT_TYPE_JSON = 'Content-Type: application/json';

    private ContenidoService $service;

    public function __construct(
        ContenidoService $service
    ) {
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

        try {

            $datos = $_POST;

            if (!$datos) {

                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos.'
                ]);

                return;
            }

            $contenido = new Contenido($datos);

            if (!empty($_FILES['archivo'])) {

                $cloudinary = new CloudinaryService();
                if ($contenido->tipo === 'video') {

                    $archivo = $cloudinary->subirVideo(
                        $_FILES['archivo']['tmp_name']
                    );

                } else {

                    $archivo = $cloudinary->subirDocumento(
                        $_FILES['archivo']['tmp_name']
                    );
                }

                $contenido->url = $archivo['secure_url'];
                $contenido->publicId = $archivo['public_id'];
            }

            $resultado = $this->service->crearContenido(
                $contenido
            );

            echo json_encode($resultado);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizarContenido(): void
    {
        header(self::CONTENT_TYPE_JSON);

        try {

            $datos = $_POST;

            if (!$datos) {

                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos.'
                ]);

                return;
            }

            $contenido = new Contenido($datos);
            $existente = $this->service->obtenerContenido(
                (int) $contenido->id
            );

            if (!$existente['success']) {

                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' => 'Contenido no encontrado.'
                ]);

                return;
            }

            if (!empty($_FILES['archivo'])) {

                $cloudinary = new CloudinaryService();
                if (!empty($existente['data']['public_id'])) {

                    $cloudinary->eliminarArchivo(
                        $existente['data']['public_id'],
                        $existente['data']['tipo']
                    );
                }
                if ($contenido->tipo === 'video') {

                    $archivo = $cloudinary->subirVideo(
                        $_FILES['archivo']['tmp_name']
                    );

                } else {

                    $archivo = $cloudinary->subirDocumento(
                        $_FILES['archivo']['tmp_name'],
                        $_FILES['archivo']['name']
                    );
                }

                $contenido->url = $archivo['secure_url'];
                $contenido->publicId = $archivo['public_id'];

            } else {

                $contenido->url = $existente['data']['url'];
                $contenido->publicId = $existente['data']['public_id'];
            }

            $resultado = $this->service->actualizarContenido($contenido);

            echo json_encode($resultado);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarContenido(
        int $id
    ): void {

        header(self::CONTENT_TYPE_JSON);

        $resultado = $this->service->eliminarContenido($id);

        if (!$resultado['success']) {
            http_response_code(404);
        }

        echo json_encode($resultado);
    }
}
