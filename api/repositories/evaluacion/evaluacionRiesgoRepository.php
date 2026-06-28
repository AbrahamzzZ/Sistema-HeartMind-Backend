<?php

class EvaluacionRiesgoRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function guardar(EvaluacionRiesgo $evaluacion): bool
    {
        $sql = "
            INSERT INTO evaluaciones_riesgo
            (
                usuario_id,
                edad,
                genero,
                altura,
                peso,
                imc,
                presion_sistolica,
                presion_diastolica,
                nivel_colesterol,
                glucosa,
                fumador,
                alcohol,
                actividad_fisica,
                probabilidad_riesgo,
                resultado_riesgo,
                recomendaciones
            )
            VALUES
            (
                :usuario_id,
                :edad,
                :genero,
                :altura,
                :peso,
                :imc,
                :presion_sistolica,
                :presion_diastolica,
                :nivel_colesterol,
                :glucosa,
                :fumador,
                :alcohol,
                :actividad_fisica,
                :probabilidad_riesgo,
                :resultado_riesgo,
                :recomendaciones
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'usuario_id' => $evaluacion->usuarioId,
            'edad' => $evaluacion->edad,
            'genero' => $evaluacion->genero,
            'altura' => $evaluacion->altura,
            'peso' => $evaluacion->peso,
            'imc' => $evaluacion->imc,
            'presion_sistolica' => $evaluacion->presionSistolica,
            'presion_diastolica' => $evaluacion->presionDiastolica,
            'nivel_colesterol' => $evaluacion->nivelColesterol,
            'glucosa' => $evaluacion->glucosa,
            'fumador' => (int)$evaluacion->fumador,
            'alcohol' => (int)$evaluacion->alcohol,
            'actividad_fisica' => (int)$evaluacion->actividadFisica,
            'probabilidad_riesgo' => $evaluacion->probabilidadRiesgo,
            'resultado_riesgo' => $evaluacion->resultadoRiesgo,
            'recomendaciones' => $evaluacion->recomendaciones
        ]);
    }

    public function obtenerPorUsuario(int $usuarioId): array
    {
        $sql = "
            SELECT
                id,
                usuario_id,
                edad,
                genero,
                altura,
                peso,
                imc,
                presion_sistolica,
                presion_diastolica,
                nivel_colesterol,
                glucosa,
                fumador,
                alcohol,
                actividad_fisica,
                ROUND(probabilidad_riesgo * 100, 1) as porcentaje_riesgo,
                resultado_riesgo,
                recomendaciones,
                fecha_evaluacion
            FROM evaluaciones_riesgo
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_evaluacion DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT
                id,
                usuario_id,
                edad,
                imc,
                ROUND(probabilidad_riesgo * 100, 1) as porcentaje_riesgo,
                resultado_riesgo,
                fecha_evaluacion
            FROM evaluaciones_riesgo
            ORDER BY fecha_evaluacion DESC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUltimaEvaluacion(int $usuarioId): ?array
    {
        $sql = "
            SELECT *
            FROM evaluaciones_riesgo
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_evaluacion DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }
}
