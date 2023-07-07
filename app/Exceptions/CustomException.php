<?php
namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public function __construct(string $message = '')
    {
        $code = '404';
        if (empty($message)) {
            $message = StatusManage::CODE_MAPPING[$code] ?? '';
        }
        parent::__construct($message, $code);
    }

    public function errors()
    {
        return $this->message;
    }
}
