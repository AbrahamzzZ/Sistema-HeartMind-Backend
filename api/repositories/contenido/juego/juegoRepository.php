<?php

class JuegoRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT *
            FROM juegos
            WHERE activo = 1
            ORDER BY fecha_creacion DESC
        ";

        return $this->db->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCodigo(string $codigo): ?array
    {
        $sql = "
            SELECT *
            FROM juegos
            WHERE codigo = :codigo
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO juegos (nombre, codigo, descripcion, tipo)
            VALUES (:nombre, :codigo, :descripcion, :tipo)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'nombre' => $data['nombre'],
            'codigo' => $data['codigo'],
            'descripcion' => $data['descripcion'] ?? null,
            'tipo' => $data['tipo']
        ]);

        return (int) $this->db->lastInsertId();
    }
}
