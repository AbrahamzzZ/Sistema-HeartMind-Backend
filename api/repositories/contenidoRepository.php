<?php

class ContenidoRepository
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
            FROM contenidos
            ORDER BY fecha_creacion DESC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function obtenerPorId(
        int $id
    ): ?array
    {
        $sql = "
            SELECT *
            FROM contenidos
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch(
            PDO::FETCH_ASSOC
        ) ?: null;
    }

    public function crear(
        Contenido $contenido
    ): bool
    {
        $sql = "
            INSERT INTO contenidos
            (
                titulo,
                descripcion,
                tipo,
                categoria,
                url,
                public_id
            )
            VALUES
            (
                :titulo,
                :descripcion,
                :tipo,
                :categoria,
                :url,
                :public_id
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'titulo' => $contenido->titulo,
            'descripcion' => $contenido->descripcion,
            'tipo' => $contenido->tipo,
            'categoria' => $contenido->categoria,
            'url' => $contenido->url,
            'public_id' => $contenido->publicId
        ]);
    }

    public function actualizar(
        Contenido $contenido
    ): bool
    {
        $sql = "
            UPDATE contenidos
            SET
                titulo = :titulo,
                descripcion = :descripcion,
                tipo = :tipo,
                categoria = :categoria,
                url = :url,
                public_id = :public_id
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $contenido->id,
            'titulo' => $contenido->titulo,
            'descripcion' => $contenido->descripcion,
            'tipo' => $contenido->tipo,
            'categoria' => $contenido->categoria,
            'url' => $contenido->url,
            'public_id' => $contenido->publicId
        ]);
    }

    public function eliminar(
        int $id
    ): bool
    {
        $sql = "
            DELETE FROM contenidos
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
