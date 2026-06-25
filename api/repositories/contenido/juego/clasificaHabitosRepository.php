<?php

class ClasificaHabitosRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

  public function obtenerJuegoCompleto(int $juegoId): array
    {
        $sqlJuego = "SELECT nombre, codigo, descripcion FROM juegos WHERE id = :id";
        $stmtJuego = $this->db->prepare($sqlJuego);
        $stmtJuego->execute(['id' => $juegoId]);
        $juego = $stmtJuego->fetch(PDO::FETCH_ASSOC);

        $sqlCategorias = "
            SELECT id, nombre
            FROM juego_categorias
            WHERE juego_id = :juego_id
            ORDER BY orden ASC
        ";
        $stmt = $this->db->prepare($sqlCategorias);
        $stmt->execute(['juego_id' => $juegoId]);
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlItems = "
            SELECT id, texto, categoria_correcta_id
            FROM juego_items
            WHERE juego_id = :juego_id
            ORDER BY orden ASC
        ";
        $stmt = $this->db->prepare($sqlItems);
        $stmt->execute(['juego_id' => $juegoId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'nombre' => $juego['nombre'],
            'codigo' => $juego['codigo'],
            'descripcion' => $juego['descripcion'],
            'categorias' => $categorias,
            'items' => $items
        ];
    }

    public function crearJuegoCompleto(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $categorias = $data['categorias'];
            $items = $data['items'];
            $juegoId = $data['juego_id'];
            $categoriaMap = [];

            foreach ($categorias as $index => $cat) {

                $sql = "
                    INSERT INTO juego_categorias (juego_id, nombre, orden)
                    VALUES (:juego_id, :nombre, :orden)
                ";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'juego_id' => $juegoId,
                    'nombre' => $cat['nombre'],
                    'orden' => $index
                ]);

                $categoriaId = $this->db->lastInsertId();
                $categoriaMap[$index] = $categoriaId;
            }

            foreach ($items as $index => $item) {

                $sql = "
                    INSERT INTO juego_items
                    (juego_id, texto, categoria_correcta_id, orden)
                    VALUES
                    (:juego_id, :texto, :categoria_correcta_id, :orden)
                ";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'juego_id' => $juegoId,
                    'texto' => $item['texto'],
                    'categoria_correcta_id' => $categoriaMap[$item['categoria_correcta_id']],
                    'orden' => $index
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
            $this->db->prepare("DELETE FROM juego_items WHERE juego_id = :id")->execute(['id' => $juegoId]);
            $this->db->prepare("DELETE FROM juego_categorias WHERE juego_id = :id")->execute(['id' => $juegoId]);
            $this->crearJuegoCompleto($data);
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
