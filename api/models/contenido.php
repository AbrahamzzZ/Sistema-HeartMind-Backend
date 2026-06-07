<?php

class Contenido
{
    public ?int $id = null;
    public string $titulo;
    public ?string $descripcion = null;
    public string $tipo;
    public string $categoria;
    public ?string $contenido = null;
    public ?string $url = null;
    public function __construct(
        array $data
    ) {
        foreach ($data as $propiedad => $valor) {

            if (
                property_exists(
                    $this,
                    $propiedad
                )
            ) {
                $this->$propiedad = $valor;
            }
        }
    }
}
