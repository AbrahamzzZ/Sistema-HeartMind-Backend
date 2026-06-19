<?php

class JuegoMemoriaCarta
{
    public ?int $id = null;
    public int $juego_id;
    public string $contenido;
    public string $tipo = 'texto';
    public int $par_id;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
