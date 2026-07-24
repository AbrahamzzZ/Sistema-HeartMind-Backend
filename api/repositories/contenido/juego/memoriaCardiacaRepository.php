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
            SELECT id, juego_id, contenido, tipo, par_id
            FROM juego_memoria_cartas
            WHERE juego_id = :juego_id
            ORDER BY par_id ASC, id ASC
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

            if (empty($pares) || !is_array($pares)) {
                throw new Exception('Pares inválidos');
            }

            $sql = "
                INSERT INTO juego_memoria_cartas
                (juego_id, contenido, tipo, par_id)
                VALUES (:juego_id, :contenido, :tipo, :par_id)
            ";
            $stmt = $this->db->prepare($sql);

            foreach ($pares as $parIndex => $par) {
                
                if (!isset($par['carta1']) || !isset($par['carta2'])) {
                    throw new Exception("Par $parIndex no tiene estructura válida");
                }

                $stmt->execute([
                    'juego_id' => $juegoId,
                    'contenido' => trim($par['carta1']),
                    'tipo' => 'texto',
                    'par_id' => $parIndex
                ]);

                $stmt->execute([
                    'juego_id' => $juegoId,
                    'contenido' => trim($par['carta2']),
                    'tipo' => 'texto',
                    'par_id' => $parIndex
                ]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error creando juego memoria: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizarJuegoCompleto(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $juegoId = $data['juego_id'];
            
            if ($juegoId <= 0) {
                throw new Exception('Juego ID inválido');
            }

            $deleteStmt = $this->db->prepare("DELETE FROM juego_memoria_cartas WHERE juego_id = :id");
            $deleteStmt->execute(['id' => $juegoId]);

            $this->crearJuegoCompleto($data);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error actualizando juego memoria: ' . $e->getMessage());
            return false;
        }
    }
}
