<?php

class UsuarioController
{
    private const CONTENT_TYPE_JSON ='Content-Type: application/json';
    private const FILE_GET_CONTENTS ='php://input';
    private UsuarioService $service;

    public function __construct(
        UsuarioService $service
    ) {
        $this->service = $service;
    }

    public function registrar(): void
    {
        header(self::CONTENT_TYPE_JSON);

        try {

            $datos = json_decode(file_get_contents(self::FILE_GET_CONTENTS), true);
            $resultado = $this->service->registrar($datos);

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

    public function login(): void
    {
        header(self::CONTENT_TYPE_JSON);

        try {

            $datos = json_decode(file_get_contents( self::FILE_GET_CONTENTS), true);
            $resultado = $this->service->login($datos['correo'],$datos['contrasena']);

            echo json_encode([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (Exception $e) {

            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerPerfil(): void
    {
        header(self::CONTENT_TYPE_JSON);

        try {

            $tokenData = AuthMiddleware::validarToken();
            $usuario = $this->service->obtenerPerfil($tokenData->usuarioId);

            echo json_encode([
                'success' => true,
                'data' => $usuario
            ]);

        } catch (Exception $e) {

            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
