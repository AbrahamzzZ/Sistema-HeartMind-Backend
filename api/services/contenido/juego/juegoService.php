<?php

require_once __DIR__ . '/../../../repositories/contenido/juego/juegoRepository.php';

class JuegoService
{
    private JuegoRepository $repository;

    public function __construct(JuegoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerJuegos(): array
    {
        $juegos = $this->repository->obtenerTodos();

        return [
            'success' => true,
            'data' => $juegos,
            'message' => empty($juegos) ? 'No hay juegos disponibles.' : null
        ];
    }

    public function obtenerPorCodigo(string $codigo): array
    {
        if (empty($codigo)) {
            return [
                'success' => false,
                'message' => 'Código inválido'
            ];
        }

        $juego = $this->repository->obtenerPorCodigo($codigo);

        if (!$juego) {
            return [
                'success' => false,
                'message' => 'Juego no encontrado'
            ];
        }

        return [
            'success' => true,
            'data' => $juego
        ];
    }

    public function crearJuego(array $data): array
    {
        if (empty($data['nombre']) || empty($data['codigo']) || empty($data['tipo'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        $resultado = $this->repository->crear($data);

        return [
            'success' => $resultado,
            'message' => $resultado
                ? 'Juego creado correctamente'
                : 'Error al crear juego'
        ];
    }
}
