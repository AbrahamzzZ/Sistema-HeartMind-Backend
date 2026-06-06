<?php

require_once __DIR__ . '/../helpers/JwtHelper.php';

class AuthMiddleware
{
    public static function validarToken(): object
    {
        $headers = getallheaders();

        if (
            !isset($headers['Authorization'])
        ) {
            throw new Exception(
                'Token no proporcionado.'
            );
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        return JwtHelper::validarToken($token);
    }
}
