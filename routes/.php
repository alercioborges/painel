<?php

use App\Teste;

$app->get('/', function (Request $request, Response $response, $args) {
	$response->getBody()->write("<br>Hello world!");
	$teste = new Teste();
	return $response;

});

?>