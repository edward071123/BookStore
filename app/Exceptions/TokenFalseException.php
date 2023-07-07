<?php
namespace App\Exceptions;

class TokenFalseException extends \Exception
{
    protected $message;

    public function __construct(string $message = '', int $code = 400, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;
    }

    public function errors()
    {
        return $this->message;
    }
}
