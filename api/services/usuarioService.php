<?php

class UsuarioService
{
    private UsuarioRepository $repository;

    public function __construct(
        UsuarioRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function registrar(
        array $datos
    ): array {

        if (
            empty($datos['nombre']) ||
            empty($datos['correo']) ||
            empty($datos['contrasena'])
        ) {
            throw new Exception(
                'Todos los campos obligatorios deben ser enviados.'
            );
        }

        $usuarioExistente = $this->repository->obtenerPorCorreo($datos['correo']);

        if ($usuarioExistente) {
            throw new Exception(
                'El correo ya se encuentra registrado.'
            );
        }

        $usuario = new Usuario(
            id: null,
            nombre: $datos['nombre'],
            correo: $datos['correo'],
            contrasena: password_hash(
                $datos['contrasena'],
                PASSWORD_BCRYPT
            ),
            rol: 'Usuario',
            edad: $datos['edad'] ?? null,
            genero: $datos['genero'] ?? null
        );

        $this->repository->crear($usuario);

        return [
            'mensaje' => 'Usuario registrado correctamente.'
        ];
    }

    public function login(
        string $correo,
        string $contrasena
    ): array {

        $usuario = $this->repository->obtenerPorCorreo($correo);

        if (!$usuario) {
            throw new Exception(
                'Credenciales inválidas.'
            );
        }

        if (
            !password_verify(
                $contrasena,
                $usuario['contrasena']
            )
        ) {
            throw new Exception(
                'Credenciales inválidas.'
            );
        }

        $token = JwtHelper::generarToken(
            $usuario['id'],
            $usuario['correo'],
            $usuario['rol']
        );

        return [
            'token' => $token,
            'usuario' => [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol' => $usuario['rol']
            ]
        ];
    }

    public function obtenerPerfil(
        int $usuarioId
    ): array {

        $usuario = $this->repository->obtenerPorId($usuarioId);

        if (!$usuario) {
            throw new Exception(
                'Usuario no encontrado.'
            );
        }

        return $usuario;
    }
}
