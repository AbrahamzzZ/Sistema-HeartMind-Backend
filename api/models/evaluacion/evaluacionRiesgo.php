<?php

class EvaluacionRiesgo
{
    public ?int $id = null;
    public int $usuarioId;
    public int $edad;
    public int $genero;
    public int $altura;
    public float $peso;
    public float $imc;
    public int $presionSistolica;
    public int $presionDiastolica;
    public int $nivelColesterol;
    public int $glucosa;
    public bool $fumador;
    public bool $alcohol;
    public bool $actividadFisica;
    public float $probabilidadRiesgo;
    public string $resultadoRiesgo;
    public string $recomendaciones;

    public function __construct(array $data = [])
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
