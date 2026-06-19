<?php

class JuegoItem
{
    public ?int $id = null;
    public int $juego_id;
    public string $texto;
    public int $categoria_correcta_id;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
