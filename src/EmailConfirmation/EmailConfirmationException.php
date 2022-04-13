<?php

namespace TallmanCode\AuthBundle\EmailConfirmation;

use Throwable;

class EmailConfirmationException extends \Exception
{
    public function __construct(Throwable $previous = null, $message = 'A confirmation email could not be sent', $code = 500)
    {
        parent::__construct($message, $code, $previous);
    }
}
