<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use Slim\Factory\AppFactory;
use App\Config;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// Create Twig
$twig = Twig::create('src/Views/templates', ['cache' => false]);

$app->add(TwigMiddleware::create($app, $twig));

$app->setBasePath(Config::BASE_DIR);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

//Define URL Attribute Request
$app->add(function (Request $request, RequestHandler $handler) {
    $url =  $request->getUri()->getScheme().'://'.$request->getUri()->getHost().Config::BASE_DIR;
    $request = $request->withAttribute('URL', $url);   
    return $handler->handle($request);
});

require_once("routes/redirect.php");
require_once("routes/home.php");

$app->get('/hi/{name}', function ($request, $response, $args) {
    $view = Twig::fromRequest($request);
    $uri = $request->getUri()->getHost();
    return $view->render($response, 'pages/hi.html', [
        'NAME' => $args['name'],
        'URL' => $request->getAttribute('URL')
    ]);
})->setName('hi');


$app->get('/teste', function ($request, $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/teste.html');
})->setName('teste');

$app->run();