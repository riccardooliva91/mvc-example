<?php

namespace App\Middlewares;

use App\Request;

/**
 * Class Authentication
 *
 * @package App\Middleware
 */
class Authentication implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function handle( Request $request, \Closure $next )
    {
        //@todo
        return $next( $request );
    }
}