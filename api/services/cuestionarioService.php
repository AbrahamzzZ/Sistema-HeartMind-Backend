<?php

require_once __DIR__ . '/../repositories/cuestionarioRepository.php';
require_once __DIR__ . '/../repositories/resultadoCuestionarioRepository.php';

require_once __DIR__ . '/../models/cuestionario.php';
require_once __DIR__ . '/../models/opcionRespuesta.php';
require_once __DIR__ . '/../models/preguntaCuestionario.php';
require_once __DIR__ . '/../models/resultadoCuestionario.php';

require_once __DIR__ . '/../validator/cuestionarioValidator.php';


class CuestionarioService
{
    private CuestionarioRepository $cuestionarioRepository;
    private PreguntaCuestionarioRepository $preguntaRepository;
    private OpcionRespuestaRepository $opcionRepository;
    private ResultadoCuestionarioRepository $resultadoRepository;
    private CuestionarioValidator $cuestionarioValidator;

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
        $this->cuestionarioValidator = new CuestionarioValidator();
    }

    public function obtenerCuestionarios(): array {
        return $this->cuestionarioRepository->obtenerTodos();
    }

    public function obtenerCuestionarioCompleto(int $cuestionarioId): ?array {
        $cuestionario = $this->cuestionarioRepository->obtenerPorId($cuestionarioId);
        if (!$cuestionario) {
            return null;
        }

        $preguntas = $this->preguntaRepository->obtenerPorCuestionario($cuestionarioId);
        foreach ($preguntas as &$pregunta) {

            $pregunta['opciones'] =
                $this->opcionRepository->obtenerPorPregunta($pregunta['id']);
        }
        $cuestionario['preguntas'] = $preguntas;

        return $cuestionario;
    }

    public function resolverCuestionario(
        int $usuarioId,
        int $cuestionarioId,
        array $respuestas
    ): array {

        if ($usuarioId <= 0 || $cuestionarioId <= 0) {
            return [
                'success' => false,
                'message' => 'Usuario o cuestionario inválido.'
            ];
        }

        $cuestionario = $this->cuestionarioRepository->obtenerPorId($cuestionarioId);
        if (!$cuestionario) {
            return [
                'success' => false,
                'message' => 'Cuestionario no encontrado.'
            ];
        }

        if (!is_array($respuestas)) {
            return [
                'success' => false,
                'message' => 'Las respuestas deben enviarse como un arreglo válido.'
            ];
        }

        $aciertos = 0;
        $detalles = [];

        foreach ($respuestas as $respuesta) {
            if (
                !isset($respuesta['preguntaId']) ||
                !isset($respuesta['opcionId'])
            ) {
                return [
                    'success' => false,
                    'message' => 'Cada respuesta debe incluir preguntaId y opcionId.'
                ];
            }

            $preguntaId = (int) $respuesta['preguntaId'];
            $opcionId = (int) $respuesta['opcionId'];

            $pregunta = $this->preguntaRepository->obtenerPorId($preguntaId);
            if (!$pregunta || (int) $pregunta['cuestionario_id'] !== $cuestionarioId) {
                return [
                    'success' => false,
                    'message' => "Pregunta inválida para el cuestionario: {$preguntaId}."
                ];
            }

            $opcion = $this->opcionRepository->obtenerPorId($opcionId);
            if (!$opcion || (int) $opcion['pregunta_id'] !== $preguntaId) {
                return [
                    'success' => false,
                    'message' => "Opción inválida para la pregunta: {$opcionId}."
                ];
            }

            $esCorrecta = (bool) $opcion['es_correcta'];
            if ($esCorrecta) {
                $aciertos++;
            }

            $detalles[] = [
                'preguntaId' => $preguntaId,
                'opcionId' => $opcionId,
                'correcta' => $esCorrecta,
                'mensaje' => $esCorrecta
                    ? 'Respuesta correcta.'
                    : 'Respuesta incorrecta.'
            ];
        }

        $resultado = new ResultadoCuestionario([
            'usuarioId' => $usuarioId,
            'cuestionarioId' => $cuestionarioId,
            'puntaje' => $aciertos
        ]);

        $this->resultadoRepository->guardar($resultado);

        return [
            'puntaje' => $aciertos,
            'respuestas' => $detalles
        ];
    }

    public function crearCuestionarioCompleto(array $data): array {
        $ok = $this->cuestionarioRepository->crearCompleto($data);

        return [
            'success' => $ok,
            'message' => $ok ? 'Cuestionario creado correctamente' : 'Error al crear cuestionario completo'
        ];
    }

    public function actualizarCuestionarioCompleto(array $data): array {
        $ok = $this->cuestionarioRepository->actualizarCompleto($data);

        return [
            'success' => $ok,
            'message' => $ok ? 'Cuestionario actualizado correctamente' : 'Error al actualizar cuestionario'
        ];
    }

    public function eliminarCuestionario(int $id): array {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID de cuestionario inválido.'];
        }
        
        $existe = $this->cuestionarioRepository->obtenerPorId($id);
        
        if (!$existe) {
            return ['success' => false, 'message' => 'Cuestionario no encontrado.'];
        }
        
        $resultado = $this->cuestionarioRepository->eliminar($id);
        
        return [
            'success' => $resultado,
            'message' => $resultado ? 'Cuestionario eliminado correctamente.' : 'Error al eliminar el cuestionario.'
        ];
    }
    
    public function obtenerHistorial(int $usuarioId): array {
        return $this->resultadoRepository->obtenerPorUsuario($usuarioId);
    }
}
