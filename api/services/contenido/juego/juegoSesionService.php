<?php

require_once __DIR__ . '/../../../repositories/contenido/juego/juegoSesionRepository.php';

class JuegoSesionService
{
    private JuegoSesionRepository $repository;

    public function __construct(JuegoSesionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function iniciarJuego(int $usuarioId, int $juegoId): array
    {
        if ($usuarioId <= 0 || $juegoId <= 0) {
            return [
                'success' => false,
                'message' => 'Datos inválidos'
            ];
        }

        $sesionId = $this->repository->iniciarSesion($usuarioId, $juegoId);

        return [
            'success' => true,
            'data' => [
                'sesion_id' => $sesionId
            ],
            'message' => 'Sesión iniciada'
        ];
    }

    public function obtenerSesionActiva(int $usuarioId, int $juegoId): array
    {
        $sesion = $this->repository->obtenerSesionActiva($usuarioId, $juegoId);

        return [
            'success' => true,
            'data' => $sesion
        ];
    }

    public function finalizarJuego(int $sesionId, int $puntaje, int $tiempo): array
    {
        if ($sesionId <= 0) {
            return [
                'success' => false,
                'message' => 'Sesión inválida'
            ];
        }

        $resultado = $this->repository->finalizarSesion($sesionId, $puntaje, $tiempo);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Juego finalizado correctamente'
                : 'Error al finalizar el juego'
        ];
    }
}
