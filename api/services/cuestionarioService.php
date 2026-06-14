<?php

require_once __DIR__ . '/../repositories/cuestionarioRepository.php';
require_once __DIR__ . '/../repositories/opcionRespuestaRepository.php';
require_once __DIR__ . '/../repositories/preguntaCuestionarioRepository.php';
require_once __DIR__ . '/../repositories/resultadoCuestionarioRepository.php';

require_once __DIR__ . '/../models/cuestionario.php';
require_once __DIR__ . '/../models/opcionRespuesta.php';
require_once __DIR__ . '/../models/preguntaCuestionario.php';
require_once __DIR__ . '/../models/resultadoCuestionario.php';

require_once __DIR__ . '/../validator/cuestionarioValidator.php';
require_once __DIR__ . '/../validator/preguntaValidator.php';
require_once __DIR__ . '/../validator/opcionValidator.php';


class CuestionarioService
{
    private CuestionarioRepository $cuestionarioRepository;
    private PreguntaCuestionarioRepository $preguntaRepository;
    private OpcionRespuestaRepository $opcionRepository;
    private ResultadoCuestionarioRepository $resultadoRepository;
    private CuestionarioValidator $cuestionarioValidator;
    private PreguntaValidator $preguntaValidator;
    private OpcionValidator $opcionValidator;

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
        $this->preguntaValidator = new PreguntaValidator();
        $this->opcionValidator = new OpcionValidator();
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


    public function crearCuestionario(Cuestionario $cuestionario): array
    {
        $validacion = $this->validarCuestionario($cuestionario);

        if (!$validacion['success']) {
            return $validacion;
        }

        $resultado = $this->cuestionarioRepository->crear($cuestionario);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Cuestionario creado correctamente.' : 'No se pudo registrar el cuestionario.'
        ];
    }

    public function crearCuestionarioCompleto(array $data): array
    {
        $ok = $this->cuestionarioRepository->crearCompleto($data);

        return [
            'success' => $ok,
            'message' => $ok ? 'Cuestionario creado correctamente' : 'Error al crear cuestionario completo'
        ];
    }

    public function actualizarCuestionario(Cuestionario $cuestionario): array
    {
        if (!$cuestionario->id || $cuestionario->id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de cuestionario inválido.'
            ];
        }
        
        $existe = $this->cuestionarioRepository->obtenerPorId($cuestionario->id);
        
        if (!$existe) {
            return [
                'success' => false,
                'message' => 'Cuestionario no encontrado.'
            ];
        }

        $validacion = $this->validarCuestionario($cuestionario, $cuestionario->id);

        if (!$validacion['success']) {
            return $validacion;
        }

        $resultado = $this->cuestionarioRepository->actualizar($cuestionario);
        
        return [
            'success' => $resultado,
            'message' => $resultado ? 'Cuestionario actualizado correctamente.' : 'No se pudo actualizar el cuestionario.'
        ];
    }

    private function validarCuestionario(Cuestionario $cuestionario, ?int $excluirId = null): array
    {
        $datos = [
            'titulo' => trim($cuestionario->titulo ?? ''),
            'descripcion' => trim($cuestionario->descripcion ?? '')
        ];

        if (!$this->cuestionarioValidator->validate($datos)) {
            return [
                'success' => false,
                'errors' => $this->cuestionarioValidator->getErrors(),
                'message' => 'Error de validación: ' . implode(', ', $this->cuestionarioValidator->getErrors())
            ];
        }

        $cuestionario->titulo = $datos['titulo'];
        $cuestionario->descripcion = $datos['descripcion'];

        if ($this->cuestionarioRepository->existePorTitulo($datos['titulo'], $excluirId)) {
            return [
                'success' => false,
                'errors' => ["Ya existe un cuestionario con el título '{$datos['titulo']}'."],
                'message' => 'Error de validación: Ya existe un cuestionario con el título proporcionado.'
            ];
        }

        return [
            'success' => true
        ];
    }

    public function eliminarCuestionario(int $id): array
    {
        if ($id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de cuestionario inválido.'
            ];
        }
        
        $existe = $this->cuestionarioRepository->obtenerPorId($id);
        
        if (!$existe) {
            return [
                'success' => false,
                'message' => 'Cuestionario no encontrado.'
            ];
        }
        
        $resultado = $this->cuestionarioRepository->eliminar($id);
        
        return [
            'success' => $resultado,
            'message' => $resultado ? 'Cuestionario eliminado correctamente.' : 'Error al eliminar el cuestionario.'
        ];
    }


    public function crearPregunta(
        PreguntaCuestionario $pregunta
    ): array {
        $validacion = $this->validarPregunta($pregunta);

        if (!$validacion['success']) {
            return $validacion;
        }

        $cuestionario = $this->cuestionarioRepository->obtenerPorId($pregunta->cuestionarioId);
        if (!$cuestionario) {
            return [
                'success' => false,
                'message' => 'El id de cuestionario proporcionado no existe.'
            ];
        }

        try {
            $resultado = $this->preguntaRepository->crear($pregunta);
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en la base de datos al crear la pregunta.'
            ];
        }

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Pregunta creada correctamente.' : 'No se pudo registrar la pregunta.'
        ];
    }

    public function actualizarPregunta(
        PreguntaCuestionario $pregunta
    ): array {
        if (!$pregunta->id || $pregunta->id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de pregunta inválido.'
            ];
        }
        $existe = $this->preguntaRepository->obtenerPorId($pregunta->id);
        if (!$existe) {
            return [
                'success' => false,
                'message' => 'Pregunta no encontrada.'
            ];
        }

        $validacion = $this->validarPregunta($pregunta);

        if (!$validacion['success']) {
            return $validacion;
        }

        try {
            $resultado = $this->preguntaRepository->actualizar($pregunta);
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en la base de datos al actualizar la pregunta.'
            ];
        }

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Pregunta actualizada correctamente.' : 'No se pudo actualizar la pregunta.'
        ];
    }

    public function eliminarPregunta(
        int $id
    ): bool {
        return $this->preguntaRepository->eliminar($id);
    }

    private function validarPregunta(PreguntaCuestionario $pregunta): array
    {
        $datos = [
            'pregunta' => trim($pregunta->pregunta ?? ''),
            'cuestionarioId' => $pregunta->cuestionarioId ?? 0
        ];

        if (!$this->preguntaValidator->validate($datos)) {
            return [
                'success' => false,
                'errors' => $this->preguntaValidator->getErrors(),
                'message' => 'Error de validación: ' . implode(', ', $this->preguntaValidator->getErrors())
            ];
        }

        $pregunta->pregunta = $datos['pregunta'];

        return [
            'success' => true
        ];
    }


    public function crearOpcion(
        OpcionRespuesta $opcion
    ): array {
        $validacion = $this->validarOpcion($opcion);

        if (!$validacion['success']) {
            return $validacion;
        }

        $preg = $this->preguntaRepository->obtenerPorId($opcion->preguntaId);
        if (!$preg) {
            return [
                'success' => false,
                'message' => 'La pregunta asociada no existe.'
            ];
        }

        if ($this->opcionRepository->existePorTexto($opcion->textoOpcion, $opcion->preguntaId)) {
            return [
                'success' => false,
                'errors' => ["Ya existe una opción con el mismo texto para esta pregunta."],
                'message' => 'Error de validación: opción duplicada.'
            ];
        }

        $opcionesExistentes = $this->opcionRepository->obtenerPorPregunta($opcion->preguntaId);
        if (count($opcionesExistentes) >= 6) {
            return [
                'success' => false,
                'message' => 'No se pueden agregar más de 6 opciones a una pregunta.'
            ];
        }

        $opcion->esCorrecta = $opcion->esCorrecta ? 1 : 0;

        try {
            if ($opcion->esCorrecta) {
                $this->opcionRepository->beginTransaction();
                $this->opcionRepository->unsetCorrectasByPregunta($opcion->preguntaId);
                $created = $this->opcionRepository->crear($opcion);
                if (!$created) {
                    $this->opcionRepository->rollBack();
                    return [
                        'success' => false,
                        'message' => 'No se pudo registrar la opción.'
                    ];
                }
                $this->opcionRepository->commit();
                $resultado = $created;
            } else {
                $resultado = $this->opcionRepository->crear($opcion);
            }
        } catch (PDOException $e) {
            try { $this->opcionRepository->rollBack(); } catch (Exception $ex) {}
            return [
                'success' => false,
                'message' => 'Error en la base de datos al crear la opción: ' . $e->getMessage()
            ];
        }

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Opción creada correctamente.' : 'No se pudo registrar la opción.'
        ];
    }

    public function actualizarOpcion(
        OpcionRespuesta $opcion
    ): array {
        if (!$opcion->id || $opcion->id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de opción inválido.'
            ];
        }

        $validacion = $this->validarOpcion($opcion);

        if (!$validacion['success']) {
            return $validacion;
        }

        $preg = $this->preguntaRepository->obtenerPorId($opcion->preguntaId);
        if (!$preg) {
            return [
                'success' => false,
                'message' => 'La pregunta asociada no existe.'
            ];
        }

        if ($this->opcionRepository->existePorTexto($opcion->textoOpcion, $opcion->preguntaId, $opcion->id)) {
            return [
                'success' => false,
                'errors' => ["Ya existe otra opción con el mismo texto para esta pregunta."],
                'message' => 'Error de validación: opción duplicada.'
            ];
        }

        $opcion->esCorrecta = $opcion->esCorrecta ? 1 : 0;

        try {
            if ($opcion->esCorrecta) {
                $this->opcionRepository->beginTransaction();
                $this->opcionRepository->unsetCorrectasByPregunta($opcion->preguntaId, $opcion->id);
                $updated = $this->opcionRepository->actualizar($opcion);
                if (!$updated) {
                    $this->opcionRepository->rollBack();
                    return [
                        'success' => false,
                        'message' => 'No se pudo actualizar la opción.'
                    ];
                }
                $this->opcionRepository->commit();
                $resultado = $updated;
            } else {
                $resultado = $this->opcionRepository->actualizar($opcion);
            }
        } catch (PDOException $e) {
            try { $this->opcionRepository->rollBack(); } catch (Exception $ex) {}
            return [
                'success' => false,
                'message' => 'Error en la base de datos al actualizar la opción: ' . $e->getMessage()
            ];
        }

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Opción actualizada correctamente.' : 'No se pudo actualizar la opción.'
        ];
    }

    public function eliminarOpcion(
        int $id
    ): array {
        $op = $this->opcionRepository->obtenerPorId($id);
        if (!$op) {
            return [
                'success' => false,
                'message' => 'Opción no encontrada.'
            ];
        }

        $preguntaId = (int)$op['pregunta_id'];
        $opciones = $this->opcionRepository->obtenerPorPregunta($preguntaId);
        if (count($opciones) <= 2) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar la opción: una pregunta debe tener al menos 2 opciones.'
            ];
        }

        try {
            $resultado = $this->opcionRepository->eliminar($id);
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en la base de datos al eliminar la opción.'
            ];
        }

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Opción eliminada correctamente.' : 'No se pudo eliminar la opción.'
        ];
    }
    
    private function validarOpcion(OpcionRespuesta $opcion): array
    {
        $datos = [
            'texto' => trim($opcion->textoOpcion ?? ''),
            'preguntaId' => $opcion->preguntaId ?? 0
        ];

        if (!$this->opcionValidator->validate($datos)) {
            return [
                'success' => false,
                'errors' => $this->opcionValidator->getErrors(),
                'message' => 'Error de validación: ' . implode(', ', $this->opcionValidator->getErrors())
            ];
        }

        $opcion->textoOpcion = $datos['texto'];

        return [
            'success' => true
        ];
    }
    

    public function obtenerHistorial(
        int $usuarioId
    ): array {
        return $this->resultadoRepository->obtenerPorUsuario($usuarioId);
    }
}
