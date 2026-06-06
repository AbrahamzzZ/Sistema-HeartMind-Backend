<?php

class EvaluacionRiesgoService
{
    public function evaluar(array $datos): array
    {
        $imc = $this->calcularImc(
            $datos['peso'],
            $datos['altura']
        );

        $puntaje = $this->calcularPuntaje(
            edad: $datos['edad'],
            imc: $imc,
            presionSistolica: $datos['presionSistolica'],
            nivelColesterol: $datos['nivelColesterol'],
            fumador: $datos['fumador'],
            diabetico: $datos['diabetico'],
            actividadFisica: $datos['actividadFisica'],
            antecedentesFamiliares: $datos['antecedentesFamiliares']
        );

        $riesgo = $this->determinarRiesgo(
            $puntaje
        );

        $recomendaciones = $this->generarRecomendaciones(
            $riesgo
        );

        return [
            'imc' => $imc,
            'puntaje' => $puntaje,
            'resultadoRiesgo' => $riesgo,
            'recomendaciones' => $recomendaciones
        ];
    }

    private function calcularImc(
        float $peso,
        float $altura
    ): float
    {
        return round(
            $peso / ($altura * $altura),
            2
        );
    }

    private function calcularPuntaje(
        int $edad,
        float $imc,
        int $presionSistolica,
        float $nivelColesterol,
        bool $fumador,
        bool $diabetico,
        bool $actividadFisica,
        bool $antecedentesFamiliares
    ): int
    {
        $puntaje = 0;

        // Edad
        if ($edad >= 60) {
            $puntaje += 3;
        } elseif ($edad >= 40) {
            $puntaje += 2;
        }

        // IMC
        if ($imc >= 30) {
            $puntaje += 2;
        } elseif ($imc >= 25) {
            $puntaje += 1;
        }

        // Presión arterial
        if ($presionSistolica >= 140) {
            $puntaje += 2;
        } elseif ($presionSistolica >= 120) {
            $puntaje += 1;
        }

        // Colesterol
        if ($nivelColesterol >= 200) {
            $puntaje += 2;
        }

        // Fumador
        if ($fumador) {
            $puntaje += 3;
        }

        // Diabético
        if ($diabetico) {
            $puntaje += 3;
        }

        // Actividad física
        if (!$actividadFisica) {
            $puntaje += 2;
        }

        // Antecedentes familiares
        if ($antecedentesFamiliares) {
            $puntaje += 2;
        }

        return $puntaje;
    }

    private function determinarRiesgo(
        int $puntaje
    ): string
    {
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
    ): array
    {
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
