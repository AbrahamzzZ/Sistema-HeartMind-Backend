<?php

require_once __DIR__ . '/Validator.php';

class ContenidoValidator extends Validator
{
    private const TITULO_MIN = 2;
    private const TITULO_MAX = 255;
    private const DESCRIPCION_MIN = 2;
    private const DESCRIPCION_MAX = 255;
    private const URL_MAX = 500;
    private const TIPO_MAX = 100;
    private const CATEGORIA_MAX = 100;

    public function validate(array $data): bool
    {
        $this->errors = [];

        if (!$this->validateRequired($data['titulo'] ?? '', 'título')) {
            return false;
        }

        $this->validateMinLength($data['titulo'], self::TITULO_MIN, 'título');
        $this->validateMaxLength($data['titulo'], self::TITULO_MAX, 'título');

        if (!$this->validateRequired($data['descripcion'] ?? '', 'descripción')) {
            return false;
        }

        $this->validateMinLength($data['descripcion'], self::DESCRIPCION_MIN, 'descripción');
        $this->validateMaxLength($data['descripcion'], self::DESCRIPCION_MAX, 'descripción');

        if (!$this->validateRequired($data['tipo'] ?? '', 'tipo')) {
            return false;
        }

        $this->validateMaxLength($data['tipo'], self::TIPO_MAX, 'tipo');

        if (!$this->validateRequired($data['categoria'] ?? '', 'categoría')) {
            return false;
        }

        $this->validateMaxLength($data['categoria'], self::CATEGORIA_MAX, 'categoría');

        if (!$this->validateRequired($data['url'] ?? '', 'url')) {
            return false;
        }

        $this->validateMaxLength($data['url'], self::URL_MAX, 'url');
        $this->validateUrl($data['url']);

        return !$this->hasErrors();
    }
}
