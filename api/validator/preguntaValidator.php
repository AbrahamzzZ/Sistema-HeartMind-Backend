<?php

require_once __DIR__ . '/Validator.php';

class PreguntaValidator extends Validator
{
    private const PREGUNTA_MIN = 5;
    private const PREGUNTA_MAX = 500;
    
    public function validate(array $data): bool
    {
        $this->errors = [];
        
        if (!$this->validateRequired($data['pregunta'] ?? '', 'texto de la pregunta')) {
            return false;
        }
        
        $this->validateMinLength($data['pregunta'], self::PREGUNTA_MIN, 'texto de la pregunta');
        $this->validateMaxLength($data['pregunta'], self::PREGUNTA_MAX, 'texto de la pregunta');
        $this->validateNumeric($data['cuestionarioId'] ?? 0, 'ID del cuestionario');
        return !$this->hasErrors();
    }
}
