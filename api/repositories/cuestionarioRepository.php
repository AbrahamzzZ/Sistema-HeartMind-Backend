<?php

class CuestionarioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT
                id,
                titulo,
                descripcion
            FROM cuestionarios
            ORDER BY id
        ";

        return $this->db
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(
        int $id
    ): ?array {

        $sql = "
            SELECT
                id,
                titulo,
                descripcion
            FROM cuestionarios
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    public function crear(
        Cuestionario $cuestionario
    ): bool {

        $sql = "
            INSERT INTO cuestionarios
            (
                titulo,
                descripcion
            )
            VALUES
            (
                :titulo,
                :descripcion
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'titulo' => $cuestionario->titulo,
            'descripcion' => $cuestionario->descripcion
        ]);
    }

    public function actualizar(
        Cuestionario $cuestionario
    ): bool {

        $sql = "
            UPDATE cuestionarios
            SET
                titulo = :titulo,
                descripcion = :descripcion
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $cuestionario->id,
            'titulo' => $cuestionario->titulo,
            'descripcion' => $cuestionario->descripcion
        ]);
    }

    public function eliminar(
        int $id
    ): bool {

        $sql = "
            DELETE FROM cuestionarios
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
