<?php

class PreguntaCuestionario
{
    public ?int $id = null;
    public int $cuestionarioId;
    public string $pregunta;
    public array $opciones = [];

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
