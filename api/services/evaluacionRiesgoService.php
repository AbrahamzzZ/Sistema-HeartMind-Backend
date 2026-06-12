<?php

require_once __DIR__ . '/../models/EvaluacionRiesgo.php';
require_once __DIR__ . '/../repositories/EvaluacionRiesgoRepository.php';

class EvaluacionRiesgoService
{
    private EvaluacionRiesgoRepository $repository;

    public function __construct(
        EvaluacionRiesgoRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function evaluar(
        array $datos
    ): array {

        $imc = $this->calcularImc($datos['peso'], $datos['altura']);
        $puntaje = $this->calcularPuntaje($datos, $imc);
        $riesgo = $this->determinarRiesgo($puntaje);
        $recomendaciones = $this->generarRecomendaciones($riesgo);
        $evaluacion = new EvaluacionRiesgo([
            'usuarioId' => $datos['usuarioId'],
            'edad' => $datos['edad'],
            'peso' => $datos['peso'],
            'altura' => $datos['altura'],
            'imc' => $imc,
            'presionSistolica' => $datos['presionSistolica'],
            'presionDiastolica' => $datos['presionDiastolica'],
            'nivelColesterol' => $datos['nivelColesterol'],
            'fumador' => $datos['fumador'],
            'diabetico' => $datos['diabetico'],
            'actividadFisica' => $datos['actividadFisica'],
            'antecedentesFamiliares' => $datos['antecedentesFamiliares'],
            'puntaje' => $puntaje,
            'resultadoRiesgo' => $riesgo
        ]);

        $this->repository->guardar($evaluacion);

        return [
            'imc' => $imc,
            'puntaje' => $puntaje,
            'resultadoRiesgo' => $riesgo,
            'recomendaciones' => $recomendaciones
        ];
    }

    public function obtenerHistorial(
        int $usuarioId
    ): array {

        return $this->repository->obtenerPorUsuario($usuarioId);
    }

    public function obtenerHistoriales(): array{
        return $this->repository->obtenerTodos();
    }

    private function calcularImc(
        float $peso,
        float $altura
    ): float {

        return round($peso / ($altura * $altura), 2);
    }

    private function calcularPuntaje(
        array $datos,
        float $imc
    ): int {

        $puntaje = 0;

        // Edad
        if ($datos['edad'] >= 60) {
            $puntaje += 3;
        } elseif ($datos['edad'] >= 40) {
            $puntaje += 2;
        }

        // IMC
        if ($imc >= 30) {
            $puntaje += 2;
        } elseif ($imc >= 25) {
            $puntaje += 1;
        }

        // Presión arterial
        if ($datos['presionSistolica'] >= 140) {
            $puntaje += 2;
        } elseif ($datos['presionSistolica'] >= 120) {
            $puntaje += 1;
        }

        // Colesterol
        if ($datos['nivelColesterol'] >= 200) {
            $puntaje += 2;
        }

        // Fumador
        if ($datos['fumador']) {
            $puntaje += 3;
        }

        // Diabético
        if ($datos['diabetico']) {
            $puntaje += 3;
        }

        // Actividad física
        if (!$datos['actividadFisica']) {
            $puntaje += 2;
        }

        // Antecedentes familiares
        if ($datos['antecedentesFamiliares']) {
            $puntaje += 2;
        }

        return $puntaje;
    }

    private function determinarRiesgo(
        int $puntaje
    ): string {

        if ($puntaje <= 5) {
            return 'Bajo';
        }

        if ($puntaje <= 10) {
            return 'Moderado';
        }

        return 'Alto';
    }

    private function generarRecomendaciones(
        string $riesgo
    ): array {

        return match ($riesgo) {

            'Bajo' => [
                'Mantenga hábitos saludables.',
                'Realice controles médicos periódicos.',
                'Continúe realizando actividad física.'
            ],

            'Moderado' => [
                'Aumente la actividad física semanal.',
                'Controle regularmente su presión arterial.',
                'Reduzca el consumo de grasas y sal.'
            ],

            'Alto' => [
                'Acuda a una valoración médica.',
                'Controle periódicamente su presión arterial.',
                'Considere abandonar el tabaquismo.',
                'Implemente actividad física bajo supervisión profesional.'
            ],

            default => []
        };
    }
}
