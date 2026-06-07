<?php

require_once __DIR__ . '/../repositories/cuestionarioRepository.php';
require_once __DIR__ . '/../repositories/opcionRespuestaRepository.php';
require_once __DIR__ . '/../repositories/preguntaCuestionarioRepository.php';
require_once __DIR__ . '/../repositories/resultadoCuestionarioRepository.php';

require_once __DIR__ . '/../models/cuestionario.php';
require_once __DIR__ . '/../models/opcionRespuesta.php';
require_once __DIR__ . '/../models/preguntaCuestionario.php';
require_once __DIR__ . '/../models/resultadoCuestionario.php';


class CuestionarioService
{
    private CuestionarioRepository $cuestionarioRepository;
    private PreguntaCuestionarioRepository $preguntaRepository;
    private OpcionRespuestaRepository $opcionRepository;
    private ResultadoCuestionarioRepository $resultadoRepository;

    public function __construct(
        CuestionarioRepository $cuestionarioRepository,
        PreguntaCuestionarioRepository $preguntaRepository,
        OpcionRespuestaRepository $opcionRepository,
        ResultadoCuestionarioRepository $resultadoRepository
    ) {
        $this->cuestionarioRepository = $cuestionarioRepository;
        $this->preguntaRepository = $preguntaRepository;
        $this->opcionRepository = $opcionRepository;
        $this->resultadoRepository = $resultadoRepository;
    }

    public function obtenerCuestionarios(): array
    {
        return $this->cuestionarioRepository->obtenerTodos();
    }

    public function obtenerCuestionarioCompleto(
        int $cuestionarioId
    ): ?array {

        $cuestionario = $this->cuestionarioRepository->obtenerPorId($cuestionarioId);

        if (!$cuestionario) {
            return null;
        }

        $preguntas = $this->preguntaRepository->obtenerPorCuestionario($cuestionarioId);

        foreach ($preguntas as &$pregunta) {

            $pregunta['opciones'] =
                $this->opcionRepository->obtenerPorPregunta(
                    $pregunta['id']
                );
        }

        $cuestionario['preguntas'] = $preguntas;

        return $cuestionario;
    }

    public function resolverCuestionario(
        int $usuarioId,
        int $cuestionarioId,
        array $respuestas
    ): array {

        $aciertos = 0;

        foreach ($respuestas as $respuesta) {

            $opcionCorrecta =
                $this->opcionRepository
                    ->obtenerOpcionCorrecta(
                        $respuesta['preguntaId']
                    );

            if (
                $opcionCorrecta ===
                (int) $respuesta['opcionId']
            ) {
                $aciertos++;
            }
        }

        $resultado = new ResultadoCuestionario([
            'usuarioId' => $usuarioId,
            'cuestionarioId' => $cuestionarioId,
            'puntaje' => $aciertos
        ]);

        $this->resultadoRepository->guardar($resultado);

        return [
            'puntaje' => $aciertos
        ];
    }


    public function crearCuestionario(
        Cuestionario $cuestionario
    ): bool {
        return $this->cuestionarioRepository->crear($cuestionario);
    }

    public function actualizarCuestionario(
        Cuestionario $cuestionario
    ): bool {
        return $this->cuestionarioRepository->actualizar($cuestionario);
    }

    public function eliminarCuestionario(
        int $id
    ): bool {
        return $this->cuestionarioRepository->eliminar($id);
    }


    public function crearPregunta(
        PreguntaCuestionario $pregunta
    ): bool {
        return $this->preguntaRepository->crear($pregunta);
    }

    public function actualizarPregunta(
        PreguntaCuestionario $pregunta
    ): bool {
        return $this->preguntaRepository->actualizar($pregunta);
    }

    public function eliminarPregunta(
        int $id
    ): bool {
        return $this->preguntaRepository->eliminar($id);
    }


    public function crearOpcion(
        OpcionRespuesta $opcion
    ): bool {
        return $this->opcionRepository->crear($opcion);
    }

    public function actualizarOpcion(
        OpcionRespuesta $opcion
    ): bool {
        return $this->opcionRepository->actualizar($opcion);
    }

    public function eliminarOpcion(
        int $id
    ): bool {
        return $this->opcionRepository->eliminar($id);
    }
    

    public function obtenerHistorial(
        int $usuarioId
    ): array {
        return $this->resultadoRepository->obtenerPorUsuario($usuarioId);
    }
}
