<?php

namespace App;

use App\Exceptions\AmbiguousAction;

/**
 * Class Response
 *
 * Here we decide what is the appropriate response to emit
 *
 * @package App
 */
class Response
{

    /**
     * @var array|callable
     */
    protected $logic = null;


    /**
     * Response constructor.
     *
     * @param  array|callable  $core
     */
    public function __construct( $core )
    {
        $this->logic = $core;
    }

    /**
     * Trigger the content building
     *
     * @return mixed
     *
     * @throws AmbiguousAction
     */
    public function __invoke()
    {
        $response = null;
        if ( is_array( $this->logic ) ) {
            $count = count( $this->logic );
            if ( 2 > $count ) {
                throw new AmbiguousAction();
            } elseif ( 2 == $count ) {
                $class    = new $this->logic[0];
                $response = $class->{$this->logic[1]}( ...func_get_args() );
            } else {
                $response = ( new $this->logic[0] )( ...func_get_args() );
            }
        } elseif ( is_callable( $this->logic ) ) {
            $logic    = $this->logic;
            $response = $logic( ...func_get_args() );
        }

        return $response;
    }

}