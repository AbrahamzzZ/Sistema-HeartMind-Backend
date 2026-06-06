<?php

class UsuarioController
{
    private UsuarioService $service;

    public function __construct(
        UsuarioService $service
    ) {
        $this->service = $service;
    }

    public function registrar(): void
    {
        header('Content-Type: application/json');

        try {

            $datos = json_decode(
                file_get_contents('php://input'),
                true
            );

            $resultado = $this->service
                ->registrar($datos);

            http_response_code(201);

            echo json_encode([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (Exception $e) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}
