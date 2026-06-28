<?php

class EvaluacionException extends RuntimeException
{
    public function __construct(string $message = "Error en evaluación", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
