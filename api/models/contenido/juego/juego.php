<?php

class Juego
{
    public ?int $id = null;
    public string $nombre;
    public string $codigo;
    public ?string $descripcion = null;
    public string $tipo;
    public bool $activo = true;
    public ?string $fecha_creacion = null;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
