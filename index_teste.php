<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use App\Config;

require __DIR__ . '/vendor/autoload.php';

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function() {
    return Twig::create('src/Views/templates', ['cache' => false]);
});

// Create App
$app = AppFactory::create();


// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));

$app->setBasePath(Config::BASE_DIR);


// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container = function ($c) {
    $view = new \Slim\Views\Twig('src/Views/templates');

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


// Define named route
$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    return $this->get('view')->render($response, 'pages/profile.html', [
        'name' => $args['name']
    ]);
})->setName('profile');

$app->get('/teste', function ($request, $response) {
    return $this->get('view')->render($response, 'pages/teste.html');
})->setName('teste');

// Render from string
$app->get('/hi/{name}', function ($request, $response, $args) {
    $str = $this->get('view')->fetchFromString(
        '<p>Hi, my name is {{ name }}.</p>',
        [
            'name' => $args['name']
        ]
    );
    $response->getBody()->write($str);
    return $response;
});

// Run app
$app->run();

?>