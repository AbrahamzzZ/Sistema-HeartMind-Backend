<?php

class MemoriaCardiacaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerCartas(int $juegoId): array
    {
        $sql = "
            SELECT *
            FROM juego_memoria_cartas
            WHERE juego_id = :juego_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['juego_id' => $juegoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearCarta(array $data): bool
    {
        $sql = "
            INSERT INTO juego_memoria_cartas
            (juego_id, contenido, tipo, par_id)
            VALUES (:juego_id, :contenido, :tipo, :par_id)
        ";

        return $this->db->prepare($sql)->execute($data);
    }
}
