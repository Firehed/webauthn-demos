<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

chdir(dirname(__DIR__));

require 'bootstrap.php';

$c = require 'config.php';

$app = AppFactory::create(container: $c);
// Middleware is LIFO
$app->add($c->get(App\Middlewares\JsonBodyParserMiddleware::class));
$app->add($c->get(App\Middlewares\CORSMiddleware::class));
$app->add($c->get(App\Middlewares\ErrorHandlingMiddleware::class));
$app->add($c->get(App\Middlewares\AccessLogMiddleware::class));

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();
