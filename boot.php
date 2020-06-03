<?php

// Classes load
require __DIR__ . '/vendor/autoload.php';

/**
 * Environment setting
 * This will set the production values.
 * The test suite will eventually override those, so let's not
 * think about this now. if possible we want to move testing logic away.
 *
 * Please note that if you need to manage environmental variables there
 * are better proved alternatives out there, like https://github.com/vlucas/phpdotenv
 * which are good solutions also for docker environments.
 */
$env_file = __DIR__ . '/.prod.env';
$env      = fopen( $env_file, 'r' );
if ( $env ) {
    $lines = explode( "\n", fread( $env, filesize( $env_file ) ) );
    while ( $line = array_pop( $lines ) ) {
        \App\Config\Env::setRaw( trim( $line ) );
    }
}