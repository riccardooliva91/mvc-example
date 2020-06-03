<?php

require __DIR__ . '/../boot.php';

$app = \App\Kernel\App::make( new \App\Kernel\Kernel() );
$app->sendResponse();
$app->terminate();