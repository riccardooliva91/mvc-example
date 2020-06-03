<?php

namespace App\Kernel;

use App\Db\Db;
use App\Request;
use App\Response;
use App\Router\Router;

/**
 * Class Kernel
 *
 * This is an abstraction of the boot/shutdown processes of the application
 *
 * @package App\Kernel
 */
class Kernel
{

    /**
     * Global middlewares executed before the response is built
     *
     * @var array
     */
    protected $beforeMiddlewares = [];

    /**
     * Global middlewares executed after the response is built
     *
     * @var array
     */
    protected $afterMiddlewares = [];

    /**
     * @var Router
     */
    protected $router = null;

    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var Response
     */
    protected $response = null;


    /**
     * Boot operations
     *
     * @return void
     */
    public function boot() : void
    {
        $this->request = Request::createFromEnvironment();
        $this->router  = Router::getInstance();

        $this->registerRoutes();
        $this->router->resolve();

        $handler     = $this->getHandler();
        $middlewares = $this->getMiddlewares();

        $this->response = ( new Pipeline( $middlewares ) )->run( $this->request, new Response( $handler ) );
    }

    /**
     * Get the result of the pipeline
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Shutdown operations
     *
     * @return void
     */
    public function shutdown()
    {
        Db::shutdown();
    }

    /**
     * Get the response handler
     *
     * @return array|callable|\Closure
     */
    private function getHandler()
    {
        $route = $this->router->getCurrentRoute();

        return ! empty( $route ) ? $route->handler : function () {
            return '404 - Page not found!'; //@todo
        };
    }

    /**
     * List the middlewares that need to be run
     *
     * @return array
     */
    private function getMiddlewares() : array
    {
        $route = $this->router->getCurrentRoute();

        return empty( $route ) ?
            array_merge( $this->beforeMiddlewares, $this->afterMiddlewares ) :
            array_merge( $this->beforeMiddlewares, $route->middlewares, $this->afterMiddlewares );
    }

    /**
     * Register available routes. We could include a separate file as well,
     * but that's fine if we don't need third parties to customize the routing
     * or if we know what to touch if we need to.
     *
     * @return void
     */
    protected function registerRoutes() : void
    {
        //@todo
        $this->router->post( 'home', '/', function () {
            return 'home';
        }, [] );
    }

}