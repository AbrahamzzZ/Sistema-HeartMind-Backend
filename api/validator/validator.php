<?php

abstract class Validator
{
    protected array $errors = [];
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    protected function addError(string $error): void
    {
        $this->errors[] = $error;
    }
    
    protected function validateRequired(string $value, string $fieldName): bool
    {
        if (empty($value)) {
            $this->addError("El campo '{$fieldName}' es obligatorio.");
            return false;
        }
        return true;
    }
    
    protected function validateMinLength(string $value, int $min, string $fieldName): bool
    {
        if (strlen($value) < $min) {
            $this->addError("El campo '{$fieldName}' debe tener al menos {$min} caracteres.");
            return false;
        }
        return true;
    }
    
    protected function validateMaxLength(string $value, int $max, string $fieldName): bool
    {
        if (strlen($value) > $max) {
            $this->addError("El campo '{$fieldName}' no puede exceder los {$max} caracteres.");
            return false;
        }
        return true;
    }
    
    protected function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError("El formato del correo electrónico no es válido.");
            return false;
        }
        return true;
    }
    
    protected function validateNumeric(int $value, string $fieldName): bool
    {
        if ($value <= 0) {
            $this->addError("El campo '{$fieldName}' debe ser un número válido.");
            return false;
        }
        return true;
    }
}
