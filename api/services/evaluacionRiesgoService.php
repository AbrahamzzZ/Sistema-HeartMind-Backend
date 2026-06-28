<?php

require_once __DIR__ . '/../models/evaluacion/evaluacionRiesgo.php';
require_once __DIR__ . '/../repositories/evaluacion/evaluacionRiesgoRepository.php';
require_once __DIR__ . '/../validator/exceptions/mlServiceException.php';
require_once __DIR__ . '/../validator/exceptions/evaluacionException.php';

class EvaluacionRiesgoService
{
    private EvaluacionRiesgoRepository $repository;
    private string $mlServiceUrl;

    public function __construct(EvaluacionRiesgoRepository $repository)
    {
        $this->repository = $repository;
        $this->mlServiceUrl = getenv('ML_SERVICE_URL') ?: 'http://ml-service:5000';
    }

    public function evaluar(array $datos): array {
        try {
            $this->validarDatos($datos);
            $altura_metros = $datos['altura'] / 100;
            $imc = $datos['peso'] / ($altura_metros ** 2);
            $nivelColesterol = $this->mapearColesterol($datos['nivelColesterol'] ?? 180);
            $glucosa = $this->mapearGlucosa($datos['glucosa'] ?? 1);

            $respuestaML = $this->llamarML([
                'edad' => (int)$datos['edad'],
                'genero' => (int)($datos['genero'] ?? 1),
                'altura' => (int)($datos['altura'] * 100),
                'peso' => (float)$datos['peso'],
                'presionSistolica' => (int)$datos['presionSistolica'],
                'presionDiastolica' => (int)$datos['presionDiastolica'],
                'colesterol' => $nivelColesterol,
                'glucosa' => $glucosa,
                'fumador' => (int)(bool)$datos['fumador'],
                'alcohol' => (int)($datos['alcohol'] ?? 0),
                'actividadFisica' => (int)(bool)$datos['actividadFisica']
            ]);

            $recomendaciones = $this->generarRecomendaciones($datos, $respuestaML['riesgo']);

            $evaluacion = new EvaluacionRiesgo([
                'usuarioId' => $datos['usuarioId'],
                'edad' => $datos['edad'],
                'genero' => (int)($datos['genero'] ?? 1),
                'altura' => (int)$datos['altura'],
                'peso' => (float)$datos['peso'],
                'imc' => round($imc, 2),
                'presionSistolica' => (int)$datos['presionSistolica'],
                'presionDiastolica' => (int)$datos['presionDiastolica'],
                'nivelColesterol' => $nivelColesterol,
                'glucosa' => $glucosa,
                'fumador' => (bool)$datos['fumador'],
                'alcohol' => (bool)($datos['alcohol'] ?? false),
                'actividadFisica' => (bool)$datos['actividadFisica'],
                'probabilidadRiesgo' => $respuestaML['probabilidad'],
                'resultadoRiesgo' => $respuestaML['riesgo'],
                'recomendaciones' => json_encode($recomendaciones)
            ]);

            $this->repository->guardar($evaluacion);

            return [
                'success' => true,
                'data' => [
                    'imc' => round($imc, 2),
                    'probabilidadRiesgo' => $respuestaML['probabilidad'],
                    'porcentaje' => $respuestaML['porcentaje'],
                    'resultadoRiesgo' => $respuestaML['riesgo'],
                    'recomendaciones' => $recomendaciones
                ]
            ];

        } catch (MLServiceException $e) {
            error_log("ML Service Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()];
        } catch (Exception $e) {
            error_log("Error inesperado: " . $e->getMessage());
            return ['success' => false, 'message' => "Error inesperado en la evaluación"];
        }
    }

    public function obtenerHistorial(int $usuarioId): array {
        try {
            $historial = $this->repository->obtenerPorUsuario($usuarioId);
            
            return [
                'success' => true,
                'data' => $historial
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtenerHistoriales(): array {
        try {
            $historiales = $this->repository->obtenerTodos();
            
            return [
                'success' => true,
                'data' => $historiales
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function llamarML(array $datos): array {
        $ch = curl_init($this->mlServiceUrl . '/predecir');
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new MLServiceException("Error conectando con ML Service: $error", 500);
        }

        if ($httpCode !== 200) {
            error_log("ML Service HTTP $httpCode: $response");
            throw new MLServiceException("Error ML Service (HTTP $httpCode)", $httpCode);
        }

        $resultado = json_decode($response, true);

        if (!isset($resultado['success']) || !$resultado['success']) {
            throw new MLServiceException("Error en predicción: " . ($resultado['error'] ?? 'Desconocido'), 400);
        }

        return $resultado;
    }

    private function mapearColesterol(int $valor): int {
        if ($valor < 200) {
            return 1;
        }

        if ($valor < 240) {
            return 2;
        }

        return 3;
    }

    private function mapearGlucosa(int $valor): int {
        if ($valor < 100) {
            return 1;
        }

        if ($valor < 126) {
            return 2;
        }

        return 3;
    }

    private function generarRecomendaciones(array $datos, string $riesgo): array {
        $recomendaciones = [];

        if ($riesgo === 'Alto') {
            $recomendaciones[] = "Consulte inmediatamente con su cardiólogo.";
        }

        if ($riesgo === 'Moderado') {
            $recomendaciones[] = "Aumente la actividad física semanal.";
        }

        if ($datos['presionSistolica'] > 130 || $datos['presionDiastolica'] > 80) {
            $recomendaciones[] = "Monitoree regularmente su presión arterial.";
        }

        if (($datos['nivelColesterol'] ?? 180) > 200) {
            $recomendaciones[] = "Reduzca grasas saturadas y aumente fibra.";
        }

        if (!$datos['actividadFisica']) {
            $recomendaciones[] = "Aumente actividad física a 150 min/semana.";
        }

        if ($datos['fumador']) {
            $recomendaciones[] = "Deje de fumar inmediatamente.";
        }

        return array_slice($recomendaciones, 0, 4);
    }

    private function validarDatos(array $datos): void {
        $requeridos = [
            'usuarioId', 'edad', 'peso', 'altura',
            'presionSistolica', 'presionDiastolica',
            'fumador', 'actividadFisica'
        ];

        foreach ($requeridos as $campo) {
            if (!isset($datos[$campo])) {
                throw new EvaluacionException("Campo requerido: $campo");
            }
        }

        if ($datos['edad'] < 18 || $datos['edad'] > 120) {
            throw new EvaluacionException("Edad debe estar entre 18 y 120 años");
        }

        if ($datos['altura'] < 100 || $datos['altura'] > 220) {
            throw new EvaluacionException("Altura debe estar entre 100 y 220 cm");
        }

        if ($datos['peso'] < 30 || $datos['peso'] > 200) {
            throw new EvaluacionException("Peso debe estar entre 30 y 200 kg");
        }
    }
}
