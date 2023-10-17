<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Controllers\Teste;

$app->get('/', function (Request $request, Response $response) {
	$teste = new Teste();
	return $response;
});

?>