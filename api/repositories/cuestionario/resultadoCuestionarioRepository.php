<?php

class ResultadoCuestionarioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function guardar(
        ResultadoCuestionario $resultado
    ): bool {

        $sql = "
            INSERT INTO resultados_cuestionario
            (
                usuario_id,
                cuestionario_id,
                puntaje
            )
            VALUES
            (
                :usuario_id,
                :cuestionario_id,
                :puntaje
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'usuario_id' => $resultado->usuarioId,
            'cuestionario_id' => $resultado->cuestionarioId,
            'puntaje' => $resultado->puntaje
        ]);
    }

    public function obtenerPorUsuario(
        int $usuarioId
    ): array {

        $sql = "
            SELECT
                rc.id,
                rc.puntaje,
                rc.fecha_realizacion,
                c.titulo
            FROM resultados_cuestionario rc
            INNER JOIN cuestionarios c
                ON c.id = rc.cuestionario_id
            WHERE rc.usuario_id = :usuario_id
            ORDER BY rc.fecha_realizacion DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
