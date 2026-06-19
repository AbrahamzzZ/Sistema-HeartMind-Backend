<?php

class OpcionRespuesta
{
    public ?int $id = null;
    public int $preguntaId;
    public string $textoOpcion;
    public bool $esCorrecta = false;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
