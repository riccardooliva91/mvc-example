<?php

namespace App\Exceptions;

/**
 * Class AmbiguousAction
 *
 * @package App\Exceptions
 */
class AmbiguousAction extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = 'Invalid required action. Please specify a closure, a callable (either instance or namespace) or a class/method pair.';
    }

}