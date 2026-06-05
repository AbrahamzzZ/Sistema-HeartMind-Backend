<?php

class Usuario
{
    public ?int $id;
    public string $nombre;
    public string $correo;
    public string $contrasena;
    public string $rol;
    public ?int $edad;
    public ?string $genero;

    public function __construct(
        ?int $id,
        string $nombre,
        string $correo,
        string $contrasena,
        string $rol = 'Usuario',
        ?int $edad = null,
        ?string $genero = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
        $this->edad = $edad;
        $this->genero = $genero;
    }
}
