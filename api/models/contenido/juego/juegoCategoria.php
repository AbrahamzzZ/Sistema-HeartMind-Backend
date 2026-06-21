<?php

class JuegoCategoria
{
    public ?int $id = null;
    public int $juegoId;
    public string $nombre;
    public int $orden;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
