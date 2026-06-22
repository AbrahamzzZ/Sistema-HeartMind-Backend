<?php

require_once __DIR__ . '/../../../repositories/contenido/juego/clasificaHabitosRepository.php';

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

        $data = $this->repository->obtenerJuegoCompleto($juegoId);

        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function crearJuegoCompleto(array $data): array
    {
        if (empty($data['juegoId']) || empty($data['categorias']) || empty($data['items'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        $resultado = $this->repository->crearJuegoCompleto($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Juego creado correctamente'
                : 'Error al crear el juego'
        ];
    }

    public function actualizarJuegoCompleto(array $data): array
    {
        if (empty($data['juego_id']) || empty($data['categorias']) || empty($data['items'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        $resultado = $this->repository->actualizarJuegoCompleto($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Juego actualizado correctamente'
                : 'Error al actualizar el juego'
        ];
    }
}
