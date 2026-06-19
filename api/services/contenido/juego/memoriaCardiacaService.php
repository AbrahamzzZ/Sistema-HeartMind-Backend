<?php

require_once __DIR__ . '/../repositories/contenido/juego/memoriaCardiacaRepository.php';

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
            return [
                'success' => false,
                'message' => 'Juego inválido'
            ];
        }

        return [
            'success' => true,
            'data' => $this->repository->obtenerCartas($juegoId)
        ];
    }
}
