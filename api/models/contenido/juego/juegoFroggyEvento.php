<?php

class JuegoFroggyEvento
{
    public ?int $id = null;
    public int $juego_id;
    public string $descripcion;
    public bool $es_correcto = true;
    public int $puntaje = 0;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
