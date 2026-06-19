<?php

class JuegoSesion
{
    public ?int $id = null;
    public int $usuario_id;
    public int $juego_id;
    public int $puntaje = 0;
    public ?int $tiempo_segundos = null;
    public bool $completado = false;
    public ?string $fecha_inicio = null;
    public ?string $fecha_fin = null;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
