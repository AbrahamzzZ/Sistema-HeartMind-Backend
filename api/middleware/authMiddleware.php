<?php

require_once __DIR__ . '/../helpers/JwtHelper.php';
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware
{
    public static function validarToken(): object
    {
        $headers = getallheaders();

        if (empty($headers['Authorization'])) {
            throw new Exception('Token no proporcionado.');
        }

        $token = trim(str_replace('Bearer ', '', $headers['Authorization']));

        if (!$token) {
            throw new Exception('Token no proporcionado.');
        }

        try {
            return JwtHelper::validarToken($token);
        } catch (ExpiredException $e) {
            throw new Exception('Token expirado.');
        } catch (SignatureInvalidException $e) {
            throw new Exception('Token inválido.');
        } catch (Exception $e) {
            throw new Exception('Token inválido.');
        }
    }

    public static function validarRol(
        array|string $rolesRequeridos
    ): object {

        $usuario = self::validarToken();
        $roles = is_array($rolesRequeridos)
            ? $rolesRequeridos
            : [$rolesRequeridos];

        if (
            !isset($usuario->rol) ||
            !in_array($usuario->rol, $roles, true)
        ) {
            if ($usuario->rol !== 'Administrador') {
                throw new Exception('No autorizado.');
            }
        }

        return $usuario;
    }
}
