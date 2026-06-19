<?php

class EvaluacionRiesgo
{
    public ?int $id = null;
    public int $usuarioId;
    public int $edad;
    public float $peso;
    public float $altura;
    public float $imc;
    public int $presionSistolica;
    public int $presionDiastolica;
    public float $nivelColesterol;
    public bool $fumador;
    public bool $diabetico;
    public bool $actividadFisica;
    public bool $antecedentesFamiliares;
    public int $puntaje;
    public string $resultadoRiesgo;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
