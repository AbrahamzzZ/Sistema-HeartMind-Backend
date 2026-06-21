<?php

require_once __DIR__ . '/../../../repositories/contenido/juego/memoriaCardiacaRepository.php';

class MemoriaCardiacaService
{
    private MemoriaCardiacaRepository $repository;
    public function __construct(MemoriaCardiacaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerCartas(int $juegoId): array
    {
        if ($juegoId <= 0) {
            return ['success' => false, 'message' => 'Juego inválido'];
        }

        return ['success' => true, 'data' => $this->repository->obtenerCartas($juegoId)];
    }

    public function crearJuegoCompleto(array $data): array
    {
        if (empty($data['juego_id']) || empty($data['pares'])) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $resultado = $this->repository->crearJuegoCompleto($data);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Memoria creada correctamente' : 'Error al crear memoria'
        ];
    }

    public function actualizarJuegoCompleto(array $data): array
    {
        if (empty($data['juego_id']) || empty($data['pares'])) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $resultado = $this->repository->actualizarJuegoCompleto($data);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Memoria actualizada correctamente' : 'Error al actualizar memoria'
        ];
    }
}
