<?php

class JuegoSesionRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function iniciarSesion(int $usuarioId, int $juegoId): int
    {
        $sql = "
            INSERT INTO juego_sesiones (usuario_id, juego_id)
            VALUES (:usuario_id, :juego_id)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'juego_id' => $juegoId
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function obtenerSesionActiva(int $usuarioId, int $juegoId): ?array
    {
        $sql = "
            SELECT *
            FROM juego_sesiones
            WHERE usuario_id = :usuario_id
              AND juego_id = :juego_id
              AND completado = 0
            ORDER BY fecha_inicio DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'juego_id' => $juegoId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function finalizarSesion(int $sesionId, int $puntaje, int $tiempo): bool
    {
        $sql = "
            UPDATE juego_sesiones
            SET
                puntaje = :puntaje,
                tiempo_segundos = :tiempo,
                completado = 1,
                fecha_fin = NOW()
            WHERE id = :id
        ";

        return $this->db->prepare($sql)->execute([
            'id' => $sesionId,
            'puntaje' => $puntaje,
            'tiempo' => $tiempo
        ]);
    }
}
