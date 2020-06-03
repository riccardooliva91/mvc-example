<?php

namespace App\Kernel;

use App\Request;
use App\Exceptions\InvalidMiddleware;
use App\Middlewares\MiddlewareInterface;

/**
 * Class Pipeline
 *
 * This will build an inception-style structure of lambdas which
 * will execute our middlewares
 *
 * @package App\Kernel
 */
class Pipeline
{

    /**
     * @var string[]
     */
    protected $middlewares = [];


    /**
     * Pipeline constructor.
     *
     * @param  array  $middlewares
     */
    public function __construct( array $middlewares )
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Build and run the stack.
     * $controllerLogic is a lambda ready to fire the logic we defined
     * in our controllers.
     *
     * @param  Request  $request
     * @param  callable  $controllerLogic
     *
     * @return mixed
     */
    public function run( Request $request, callable $controllerLogic )
    {
        $core        = $this->createCore( $controllerLogic );
        $middlewares = $this->prepareMiddlewares();
        $stack       = $this->prepareStack( $middlewares, $core );

        return $stack( $request );
    }

    /**
     * The core functionality (the controller logic)
     *
     * @param  callable  $controllerLogic
     *
     * @return callable
     */
    protected function createCore( callable $controllerLogic )
    {
        return function ( Request $request ) use ( $controllerLogic ) {
            return $controllerLogic( $request );
        };
    }

    /**
     * Instantiate and validate the middlewares. Since with this structure they
     * are going to run from last to first, we'll reverse them.
     *
     * @return array
     */
    protected function prepareMiddlewares() : array
    {
        return array_map( function ( $middleware ) {
            $layer = new $middleware;
            if ( ! $layer instanceof MiddlewareInterface ) {
                throw new InvalidMiddleware( $middleware );
            }

            return $layer;
        }, array_reverse( $this->middlewares ) );
    }

    /**
     * Prepare the stack
     *
     * @param  array  $middlewares
     * @param  callable  $core
     *
     * @return callable
     */
    protected function prepareStack( array $middlewares, callable $core ) : callable
    {
        return array_reduce( $middlewares, function ( $next, $current ) {
            return $this->createLayer( $next, $current );
        }, $core );
    }

    /**
     * Build a step
     *
     * @param $next
     * @param $current
     *
     * @return \Closure
     */
    protected function createLayer( $next, $current )
    {
        return function ( $object ) use ( $next, $current ) {
            return $current->handle( $object, $next );
        };
    }

}