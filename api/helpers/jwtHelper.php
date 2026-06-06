<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static function getSecretKey(): string
    {
        return getenv('JWT_SECRET');
    }

    public static function generarToken(
        int $usuarioId,
        string $correo,
        string $rol
    ): string {

        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'usuarioId' => $usuarioId,
            'correo' => $correo,
            'rol' => $rol
        ];

        return JWT::encode(
            $payload,
            self::getSecretKey(),
            'HS256'
        );
    }

    public static function validarToken(
        string $token
    ): object {

        return JWT::decode($token, new Key(self::getSecretKey(), 'HS256')
        );
    }
}
