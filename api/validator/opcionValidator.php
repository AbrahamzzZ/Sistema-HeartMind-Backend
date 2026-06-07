<?php
// validators/OpcionValidator.php

require_once __DIR__ . '/Validator.php';

class OpcionValidator extends Validator
{
    private const TEXTO_MIN = 2;
    private const TEXTO_MAX = 255;

    public function validate(array $data): bool
    {
        $this->errors = [];

        $texto = $data['texto'] ?? ($data['textoOpcion'] ?? '');

        if (!$this->validateRequired($texto, 'texto de la opción')) {
            return false;
        }

        $this->validateMinLength($texto, self::TEXTO_MIN, 'texto de la opción');
        $this->validateMaxLength($texto, self::TEXTO_MAX, 'texto de la opción');
        $this->validateNumeric($data['preguntaId'] ?? 0, 'ID de la pregunta');

        return !$this->hasErrors();
    }
}
