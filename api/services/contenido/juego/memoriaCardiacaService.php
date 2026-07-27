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

        $cartas = $this->repository->obtenerCartas($juegoId);

        return [
            'success' => true,
            'data' => $cartas,
            'total' => count($cartas)
        ];
    }

    public function crearJuegoCompleto(array $data): array
    {
        if (empty($data['juego_id'])) {
            return ['success' => false, 'message' => 'Juego ID requerido'];
        }

        foreach ($data['pares'] as $index => $par) {
            if (!isset($par['carta1']) || !isset($par['carta2'])) {
                return [
                    'success' => false,
                    'message' => "Par $index incompleto: se requieren carta1 y carta2"
                ];
            }
        }

        $resultado = $this->repository->crearJuegoCompleto($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Memoria creada correctamente'
                : 'Error al crear memoria'
        ];
    }

    public function actualizarJuegoCompleto(array $data): array
    {
        $validacion = $this->crearJuegoCompleto($data);

        if (!$validacion['success']) {
            return $validacion;
        }

        $resultado = $this->repository->actualizarJuegoCompleto($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Memoria actualizada correctamente'
                : 'Error al actualizar memoria'
        ];
    }
}
