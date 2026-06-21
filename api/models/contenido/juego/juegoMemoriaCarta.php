<?php

class JuegoMemoriaCarta
{
    public ?int $id = null;
    public int $juegoId;
    public string $contenido;
    public string $tipo = 'texto';
    public int $parId;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
