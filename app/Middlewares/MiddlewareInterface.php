<?php

namespace App\Middlewares;

use App\Request;

/**
 * Interface MiddlewareInterface
 *
 * Middlewares are layers the request will pass through before being executed.
 * You can use those to perform checks (e.g. is the user authenticated?) or even
 * to intercept the outgoing response.
 *
 * @package App\Middleware
 */
interface MiddlewareInterface
{

    /**
     * Handle the incoming/outgoing request
     *
     * @param  Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle( Request $request, \Closure $next );

}