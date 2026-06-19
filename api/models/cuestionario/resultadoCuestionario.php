<?php

class ResultadoCuestionario
{
    public ?int $id = null;
    public int $usuarioId;
    public int $cuestionarioId;
    public int $puntaje;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
