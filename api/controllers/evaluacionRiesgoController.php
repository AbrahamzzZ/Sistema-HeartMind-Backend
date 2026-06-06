<?php

require_once __DIR__ . '/../services/evaluacionRiesgoService.php';
require_once __DIR__ . '/../models/evaluacionRiesgo.php';
require_once __DIR__ . '/../repositories/evaluacionRiesgoRepository.php';

class EvaluacionRiesgoController
{
    private EvaluacionRiesgoService $service;
    private EvaluacionRiesgoRepository $repository;

    public function __construct(
        EvaluacionRiesgoService $service,
        EvaluacionRiesgoRepository $repository
    ) {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function evaluar(): void
    {
        header('Content-Type: application/json');

        $datos = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!$datos) {
            http_response_code(400);

            echo json_encode([
                'mensaje' => 'Datos inválidos.'
            ]);

            return;
        }

        $resultado = $this->service->evaluar($datos);

        $evaluacion = new EvaluacionRiesgo([
            'usuarioId' => $datos['usuarioId'],
            'edad' => $datos['edad'],
            'peso' => $datos['peso'],
            'altura' => $datos['altura'],
            'imc' => $resultado['imc'],
            'presionSistolica' => $datos['presionSistolica'],
            'presionDiastolica' => $datos['presionDiastolica'],
            'nivelColesterol' => $datos['nivelColesterol'],
            'fumador' => $datos['fumador'],
            'diabetico' => $datos['diabetico'],
            'actividadFisica' => $datos['actividadFisica'],
            'antecedentesFamiliares' => $datos['antecedentesFamiliares'],
            'puntaje' => $resultado['puntaje'],
            'resultadoRiesgo' => $resultado['resultadoRiesgo']
        ]);

        $this->repository->guardar(
            $evaluacion
        );

        echo json_encode([
            'success' => true,
            'data' => $resultado
        ]);
    }

    public function obtenerHistorial(
        int $usuarioId
    ): void {
        header('Content-Type: application/json');

        $evaluaciones = $this->repository
            ->obtenerPorUsuario($usuarioId);

        echo json_encode([
            'success' => true,
            'data' => $evaluaciones
        ]);
    }
}
