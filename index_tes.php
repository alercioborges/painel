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

if (PHP_SAPI == 'cli-server') {

    $_SERVER['SCRIPT_NAME'] = basename(__FILE__);

    // To help the built-in PHP dev server, check if the request was actually for

    // something which should probably be served as a static file

    $url  = parse_url($_SERVER['REQUEST_URI']);

    $file = __DIR__ . $url['path'];

    if (is_file($file)) {

        return false;

    }

}

$app->run();

?>