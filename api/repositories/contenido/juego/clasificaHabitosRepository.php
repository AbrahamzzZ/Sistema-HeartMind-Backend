<?php

class ClasificaHabitosRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerCategorias(int $juegoId): array
    {
        $sql = "
            SELECT *
            FROM juego_categorias
            WHERE juego_id = :juego_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['juego_id' => $juegoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearCategoria(array $data): bool
    {
        $sql = "
            INSERT INTO juego_categorias (juego_id, nombre)
            VALUES (:juego_id, :nombre)
        ";

        return $this->db->prepare($sql)->execute($data);
    }

    public function obtenerItems(int $juegoId): array
    {
        $sql = "
            SELECT i.*, c.nombre AS categoria
            FROM juego_items i
            JOIN juego_categorias c ON i.categoria_correcta_id = c.id
            WHERE i.juego_id = :juego_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['juego_id' => $juegoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearItem(array $data): bool
    {
        $sql = "
            INSERT INTO juego_items (juego_id, texto, categoria_correcta_id)
            VALUES (:juego_id, :texto, :categoria_correcta_id)
        ";

        return $this->db->prepare($sql)->execute($data);
    }
}
