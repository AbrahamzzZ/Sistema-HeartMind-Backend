<?php

class Conexion
{
    public static function obtenerConexion(): PDO
    {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $password
        );

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        return $pdo;
    }
}
