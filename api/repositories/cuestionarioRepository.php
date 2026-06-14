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

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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

    public function crearCompleto(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Cuestionario
            $stmt = $this->db->prepare("
                INSERT INTO cuestionarios (titulo, descripcion)
                VALUES (:titulo, :descripcion)
            ");

            $stmt->execute([
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion']
            ]);

            $cuestionarioId = $this->db->lastInsertId();

            // 2. Preguntas
            foreach ($data['preguntas'] as $pregunta) {

                $stmt = $this->db->prepare("
                    INSERT INTO preguntas_cuestionario (cuestionario_id, pregunta)
                    VALUES (:cuestionario_id, :pregunta)
                ");

                $stmt->execute([
                    'cuestionario_id' => $cuestionarioId,
                    'pregunta' => $pregunta['pregunta']
                ]);

                $preguntaId = $this->db->lastInsertId();

                // 3. Opciones
                foreach ($pregunta['opciones'] as $opcion) {

                    $stmt = $this->db->prepare("
                        INSERT INTO opciones_respuesta
                        (pregunta_id, texto_opcion, es_correcta)
                        VALUES (:pregunta_id, :texto_opcion, :es_correcta)
                    ");

                    $stmt->execute([
                        'pregunta_id' => $preguntaId,
                        'texto_opcion' => $opcion['texto'],
                        'es_correcta' => (int)$opcion['esCorrecta']
                    ]);
                }
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            $this->db->rollBack();

            return false;
        }
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
        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existePorTitulo(string $titulo, ?int $excluirId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM cuestionarios WHERE titulo = :titulo";
        $params = ['titulo' => $titulo];
        
        if ($excluirId) {
            $sql .= " AND id != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
}
