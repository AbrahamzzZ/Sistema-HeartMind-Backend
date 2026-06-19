<?php

class JuegoCategoria
{
    public ?int $id = null;
    public int $juego_id;
    public string $nombre;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
