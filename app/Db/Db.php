<?php

namespace App\Db;

use App\Registry;
use App\Config\Config;
use App\Db\Handlers\{AbstractDbHandler, MysqliDbHandler, PdoDbHandler};
use App\Exceptions\{InvalidDbHandler, InvalidConfigFile, InvalidConfig, InvalidDbCredentials, InvalidDbCredentialMissingDatabase};

/**
 * Class Db
 *
 * @package App\Db
 */
class Db
{

    /**
     * Get our database connection. This will be saved into the registry and, if it exists, it will be taken from there.
     * This is perfect because:
     * 1) here we don't alter the state of the handler object and, if that's so, we MUST do it from here so we can manage the registry
     * 2) this way we'll be sure to have only one open connection with the database
     *
     * Please note that this is coupled with the configuration, and it doesn't allow us to swap from an handler to another.
     * On more complex scenarios we could define different databases, specify an handler which could be overwritten and so on,
     * but in our scenario this solution is perfectly fine.
     *
     * @param  array  $options
     *
     * @return AbstractDbHandler
     *
     * @throws InvalidDbHandler|InvalidConfig|InvalidConfigFile|InvalidDbCredentialMissingDatabase|InvalidDbCredentials
     */
    public static function getHandler( array $options = [] ) : AbstractDbHandler
    {
        $registry = Registry::create();
        if ( ! $registry->has( 'db' ) ) {
            $handler = Config::get( 'db.handler' );
            switch ( $handler ) {
                case 'mysqli':
                    $handler = MysqliDbHandler::getConnection( $options );
                    break;
                case 'pdo':
                    $handler = PdoDbHandler::getConnection( $options );
                    break;
                default:
                    throw new InvalidDbHandler( $handler );
            }

            $registry->set( 'db', $handler );
        }

        return $handler ?? $registry->get( 'db' );
    }

    /**
     * Close the database connection and remove the object from the registry
     *
     * @return void
     */
    public static function shutdown() : void
    {
        $registry = Registry::create();
        if ( $registry->has( 'db' ) ) {
            $registry->get( 'db' )->closeConnection();
            $registry->unset( 'db' );
        }
    }

}