<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use Slim\Factory\AppFactory;
use App\Config;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// Create Twig
$twig = Twig::create('src/Views/templates', ['cache' => false]);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

$app->setBasePath(Config::BASE_DIR);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);



// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('path/to/templates');

    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    $assetManager = new Glazilla\TwigAsset\TwigAssetManagement([
        'verion' => '1'
    ]);
    $assetManager->addPath('css', '/css');
    $assetManager->addPath('img', '/images');
    $assetManager->addPath('js', '/js');
    $view->addExtension($assetManager->getAssetExtension());

    return $view;
};

   


require_once("routes/redirect.php");
require_once("routes/home.php");

$app->get('/hi/{name}', function ($request, $response, $args) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/base.html', [
        'NAME' => $args['name']
    ]);
})->setName('base');


$app->get('/teste', function ($request, $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/teste.html');
})->setName('teste');



$app->run();