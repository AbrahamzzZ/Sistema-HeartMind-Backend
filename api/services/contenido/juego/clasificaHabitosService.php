<?php

require_once __DIR__ . '/../repositories/contenido/juego/clasificaHabitosRepository.php';

class ClasificaHabitosService
{
    private ClasificaHabitosRepository $repository;

    public function __construct(ClasificaHabitosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerDatosJuego(int $juegoId): array
    {
        if ($juegoId <= 0) {
            return [
                'success' => false,
                'message' => 'Juego inválido'
            ];
        }

        $categorias = $this->repository->obtenerCategorias($juegoId);
        $items = $this->repository->obtenerItems($juegoId);

        return [
            'success' => true,
            'data' => [
                'categorias' => $categorias,
                'items' => $items
            ]
        ];
    }

    public function crearCategoria(array $data): array
    {
        if (empty($data['nombre']) || empty($data['juego_id'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        $resultado = $this->repository->crearCategoria($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Categoría creada'
                : 'Error al crear categoría'
        ];
    }

    public function crearItem(array $data): array
    {
        if (empty($data['texto']) || empty($data['categoria_correcta_id'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        $resultado = $this->repository->crearItem($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Item creado'
                : 'Error al crear item'
        ];
    }
}
