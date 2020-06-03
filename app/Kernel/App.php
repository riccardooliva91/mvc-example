<?php

namespace App\Kernel;

/**
 * Class App
 *
 * @package App\Kernel
 */
class App
{

    /**
     * @var App
     */
    protected static $app = null;

    /**
     * @var Kernel
     */
    protected $kernel = null;


    /**
     * App constructor.
     *
     * @param  Kernel  $kernel
     */
    private function __construct( Kernel $kernel )
    {
        $this->kernel = $kernel;
    }

    /**
     * Boot the app
     *
     * @param  Kernel  $kernel
     *
     * @return static
     */
    public static function make( Kernel $kernel ) : self
    {
        if ( empty( self::$app ) ) {
            self::$app = new static( $kernel );
            self::$app->boot();
        }

        return self::$app;
    }

    /**
     * Kill the app
     *
     * @return void
     */
    public function terminate() : void
    {
        if ( ! empty( self::$app ) ) {
            self::$app->shutdown();
        }
    }

    /**
     * Sent the HTTP response
     *
     * @return void
     *
     * @todo
     */
    public function sendResponse() : void
    {
        echo $this->kernel->getResponse();
    }

    /**
     * Ask the kernel to boot the whole stuff
     *
     * @return void
     */
    protected function boot() : void
    {
        $this->kernel->boot();
    }

    /**
     * Ask the kernel to terminate the application
     *
     * @return void
     */
    protected function shutdown() : void
    {
        $this->kernel->shutdown();
    }

}