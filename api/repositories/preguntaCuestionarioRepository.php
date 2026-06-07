<?php

class PreguntaCuestionarioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerPorCuestionario(
        int $cuestionarioId
    ): array {

        $sql = "
            SELECT *
            FROM preguntas_cuestionario
            WHERE cuestionario_id = :cuestionario_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'cuestionario_id' => $cuestionarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(
        PreguntaCuestionario $pregunta
    ): bool {

        $sql = "
            INSERT INTO preguntas_cuestionario
            (
                cuestionario_id,
                pregunta
            )
            VALUES
            (
                :cuestionario_id,
                :pregunta
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'cuestionario_id' => $pregunta->cuestionarioId,
            'pregunta' => $pregunta->pregunta
        ]);
    }

    public function actualizar(
        PreguntaCuestionario $pregunta
    ): bool {

        $sql = "
            UPDATE preguntas_cuestionario
            SET pregunta = :pregunta
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $pregunta->id,
            'pregunta' => $pregunta->pregunta
        ]);
    }

    public function eliminar(
        int $id
    ): bool {

        $sql = "
            DELETE FROM preguntas_cuestionario
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
