<?php

require_once __DIR__ . '/../repositories/contenidoRepository.php';
require_once __DIR__ . '/../models/contenido.php';
require_once __DIR__ . '/../validator/contenidoValidator.php';

class ContenidoService
{
    private ContenidoRepository $repository;
    private ContenidoValidator $validator;

    public function __construct(
        ContenidoRepository $repository
    ) {
        $this->repository = $repository;
        $this->validator = new ContenidoValidator();
    }

    public function obtenerContenidos(): array
    {
        $contenidos = $this->repository->obtenerTodos();

        $response = [
            'success' => true,
            'data' => $contenidos
        ];

        if (empty($contenidos)) {
            $response['message'] = 'No hay información que mostrar.';
        }

        return $response;
    }

    public function obtenerContenido(int $id): array
    {
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de contenido inválido.'
            ];
        }

        $contenido = $this->repository->obtenerPorId($id);

        if (!$contenido) {
            return [
                'success' => false,
                'message' => 'No se encuentra nada por ese id.'
            ];
        }

        return [
            'success' => true,
            'data' => $contenido
        ];
    }

    public function crearContenido(Contenido $contenido): array
    {
        $validacion = $this->validarContenido($contenido);

        if (!$validacion['success']) {
            return $validacion;
        }

        $resultado = $this->repository->crear($contenido);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Contenido creado correctamente.' : 'No se pudo registrar el contenido.'
        ];
    }

    public function actualizarContenido(Contenido $contenido): array
    {
        if (!$contenido->id || $contenido->id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de contenido inválido.'
            ];
        }

        $existe = $this->repository->obtenerPorId($contenido->id);

        if (!$existe) {
            return [
                'success' => false,
                'message' => 'No se encuentra nada por ese id.'
            ];
        }

        $validacion = $this->validarContenido($contenido);

        if (!$validacion['success']) {
            return $validacion;
        }

        $resultado = $this->repository->actualizar($contenido);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Contenido actualizado correctamente.' : 'No se pudo actualizar el contenido.'
        ];
    }

    public function eliminarContenido(int $id): array
    {
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de contenido inválido.'
            ];
        }

        $contenido = $this->repository->obtenerPorId($id);

        if (!$contenido) {
            return [
                'success' => false,
                'message' => 'No se encuentra nada por ese id.'
            ];
        }

        $resultado = $this->repository->eliminar($id);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Contenido eliminado correctamente.' : 'No se pudo eliminar el contenido.'
        ];
    }

    private function validarContenido(Contenido $contenido): array
    {
        $datos = [
            'titulo' => trim($contenido->titulo ?? ''),
            'descripcion' => trim($contenido->descripcion ?? ''),
            'contenido' => trim($contenido->contenido ?? ''),
            'tipo' => trim($contenido->tipo ?? ''),
            'categoria' => trim($contenido->categoria ?? ''),
            'url' => trim($contenido->url ?? '')
        ];

        if (!$this->validator->validate($datos)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'Error de validación: ' . implode(', ', $this->validator->getErrors())
            ];
        }

        $contenido->titulo = $datos['titulo'];
        $contenido->descripcion = $datos['descripcion'];
        $contenido->contenido = $datos['contenido'];
        $contenido->tipo = $datos['tipo'];
        $contenido->categoria = $datos['categoria'];
        $contenido->url = $datos['url'];

        return [
            'success' => true
        ];
    }
}
