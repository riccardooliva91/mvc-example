<?php

namespace App\Exceptions;

/**
 * Class InvalidDbCredentialMissingDatabase
 *
 * @package App\Exceptions
 */
class InvalidDbCredentialMissingDatabase extends \Exception
{

    /**
     * @param  mixed  $message
     */
    public function setMessage( $message ) : void
    {
        $this->message = 'The database is required (it\'s not actually, but we want it).';
    }

}