<?php

class OpcionRespuestaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerPorPregunta(
        int $preguntaId
    ): array {

        $sql = "
            SELECT *
            FROM opciones_respuesta
            WHERE pregunta_id = :pregunta_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'pregunta_id' => $preguntaId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerOpcionCorrecta(
        int $preguntaId
    ): ?int {

        $sql = "
            SELECT id
            FROM opciones_respuesta
            WHERE pregunta_id = :pregunta_id
            AND es_correcta = TRUE
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'pregunta_id' => $preguntaId
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado
            ? (int)$resultado['id']
            : null;
    }

    public function crear(
        OpcionRespuesta $opcion
    ): bool {

        $sql = "
            INSERT INTO opciones_respuesta
            (
                pregunta_id,
                texto_opcion,
                es_correcta
            )
            VALUES
            (
                :pregunta_id,
                :texto_opcion,
                :es_correcta
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'pregunta_id' => $opcion->preguntaId,
            'texto_opcion' => $opcion->textoOpcion,
            'es_correcta' => $opcion->esCorrecta
        ]);
    }

    public function actualizar(
        OpcionRespuesta $opcion
    ): bool {

        $sql = "
            UPDATE opciones_respuesta
            SET
                texto_opcion = :texto_opcion,
                es_correcta = :es_correcta
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $opcion->id,
            'texto_opcion' => $opcion->textoOpcion,
            'es_correcta' => $opcion->esCorrecta
        ]);
    }

    public function eliminar(
        int $id
    ): bool {

        $sql = "
            DELETE FROM opciones_respuesta
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
