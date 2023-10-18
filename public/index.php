<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new \DI\Container(); 

AppFactory::setContainer($container); 
$app = AppFactory::create();  

require __DIR__ . '/../src/routes.php';
setup_routes($app);  

$app->run();
