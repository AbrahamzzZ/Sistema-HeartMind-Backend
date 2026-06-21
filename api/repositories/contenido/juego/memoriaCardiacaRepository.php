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
            ORDER BY par_id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['juego_id' => $juegoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearJuegoCompleto(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $juegoId = $data['juego_id'];
            $pares = $data['pares'];

            foreach ($pares as $index => $par) {

                $sql = "
                    INSERT INTO juego_memoria_cartas
                    (juego_id, contenido, tipo, par_id)
                    VALUES (:juego_id, :contenido, :tipo, :par_id)
                ";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'juego_id' => $juegoId,
                    'contenido' => $par['carta1'],
                    'tipo' => 'texto',
                    'par_id' => $index
                ]);

                $stmt->execute([
                    'juego_id' => $juegoId,
                    'contenido' => $par['carta2'],
                    'tipo' => 'texto',
                    'par_id' => $index
                ]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function actualizarJuegoCompleto(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $juegoId = $data['juego_id'];
            $this->db->prepare("DELETE FROM juego_memoria_cartas WHERE juego_id = :id")->execute(['id' => $juegoId]);
            $this->crearJuegoCompleto($data);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
