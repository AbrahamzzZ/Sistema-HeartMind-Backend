<?php

require_once __DIR__ . '/../repositories/contenido/juego/froggyCardioRepository.php';

class FroggyCardioService
{
    private FroggyCardioRepository $repository;

    public function __construct(FroggyCardioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerEventos(int $juegoId): array
    {
        if ($juegoId <= 0) {
            return [
                'success' => false,
                'message' => 'Juego inválido'
            ];
        }

        return [
            'success' => true,
            'data' => $this->repository->obtenerEventos($juegoId)
        ];
    }
}
