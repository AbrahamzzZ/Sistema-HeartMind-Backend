<?php

class UsuarioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function crear(Usuario $usuario): bool
    {
        $sql = "
            INSERT INTO usuarios
            (
                nombre,
                correo,
                contrasena,
                rol,
                edad,
                genero
            )
            VALUES
            (
                :nombre,
                :correo,
                :contrasena,
                :rol,
                :edad,
                :genero
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'contrasena' => $usuario->contrasena,
            'rol' => $usuario->rol,
            'edad' => $usuario->edad,
            'genero' => $usuario->genero
        ]);
    }

    public function obtenerPorCorreo(string $correo)
    {
        $sql = "
            SELECT *
            FROM usuarios
            WHERE correo = :correo
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'correo' => $correo
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
