<?php

class JuegoResultadoDetalle
{
    public ?int $id = null;
    public int $sesion_id;
    public int $juego_id;
    public ?int $item_id = null;
    public ?string $respuesta_usuario = null;
    public ?bool $es_correcto = null;

    public function __construct(array $data)
    {
        foreach ($data as $propiedad => $valor) {
            if (property_exists($this, $propiedad)) {
                $this->$propiedad = $valor;
            }
        }
    }
}
