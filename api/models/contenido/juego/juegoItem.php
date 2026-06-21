<?php

class JuegoItem
{
    public ?int $id = null;
    public int $juegoId;
    public string $texto;
    public int $categoriaCorrectaId;
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
