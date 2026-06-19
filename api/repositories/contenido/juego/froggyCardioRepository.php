<?php

class FroggyCardioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerEventos(int $juegoId): array
    {
        $sql = "
            SELECT *
            FROM juego_froggy_eventos
            WHERE juego_id = :juego_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['juego_id' => $juegoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearEvento(array $data): bool
    {
        $sql = "
            INSERT INTO juego_froggy_eventos
            (juego_id, descripcion, es_correcto, puntaje)
            VALUES (:juego_id, :descripcion, :es_correcto, :puntaje)
        ";

        return $this->db->prepare($sql)->execute($data);
    }
}
