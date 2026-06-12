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
                peso,
                altura,
                imc,
                presion_sistolica,
                presion_diastolica,
                nivel_colesterol,
                fumador,
                diabetico,
                actividad_fisica,
                antecedentes_familiares,
                puntaje,
                resultado_riesgo
            )
            VALUES
            (
                :usuario_id,
                :edad,
                :peso,
                :altura,
                :imc,
                :presion_sistolica,
                :presion_diastolica,
                :nivel_colesterol,
                :fumador,
                :diabetico,
                :actividad_fisica,
                :antecedentes_familiares,
                :puntaje,
                :resultado_riesgo
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'usuario_id' => $evaluacion->usuarioId,
            'edad' => $evaluacion->edad,
            'peso' => $evaluacion->peso,
            'altura' => $evaluacion->altura,
            'imc' => $evaluacion->imc,
            'presion_sistolica' => $evaluacion->presionSistolica,
            'presion_diastolica' => $evaluacion->presionDiastolica,
            'nivel_colesterol' => $evaluacion->nivelColesterol,
            'fumador' => (int) $evaluacion->fumador,
            'diabetico' =>  (int) $evaluacion->diabetico,
            'actividad_fisica' => (int) $evaluacion->actividadFisica,
            'antecedentes_familiares' => (int) $evaluacion->antecedentesFamiliares,
            'puntaje' => $evaluacion->puntaje,
            'resultado_riesgo' => $evaluacion->resultadoRiesgo
        ]);
    }

    public function obtenerPorUsuario(int $usuarioId)
    {
        $sql = "
            SELECT *
            FROM evaluaciones_riesgo
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_evaluacion DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos(){
        $sql = "
            SELECT *
            FROM evaluaciones_riesgo
            ORDER BY fecha_evaluacion DESC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}
