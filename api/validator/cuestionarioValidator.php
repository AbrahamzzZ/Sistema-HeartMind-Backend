<?php

require_once __DIR__ . '/Validator.php';

class CuestionarioValidator extends Validator
{
    private const TITULO_MIN = 2;
    private const TITULO_MAX = 255;
    private const DESCRIPCION_MAX = 250;
    
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
        
        $this->validateMaxLength($data['descripcion'], self::DESCRIPCION_MAX, 'descripción');
        
        return !$this->hasErrors();
    }
    
    public function validateTituloUnico(string $titulo, callable $existsCallback, ?int $excluirId = null): bool
    {
        if ($existsCallback($titulo, $excluirId)) {
            $this->addError("Ya existe un cuestionario con el título '{$titulo}'.");
            return false;
        }
        return true;
    }
}
