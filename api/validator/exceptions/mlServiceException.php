<?php

class MLServiceException extends RuntimeException
{
    public function __construct(string $message = "Error en servicio ML", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
