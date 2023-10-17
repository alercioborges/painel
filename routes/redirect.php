<?php

use Psr\Http\Message\RequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponsePsr7;

$app->add(function (RequestInterface $request, RequestHandler $handler) {
    $uri = $request->getUri();
    $path = $uri->getPath();

    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $protocol = $isHttps ? 'https://' : 'http://';

    $index = $protocol . $_SERVER['HTTP_HOST'] . $path;
    
    if ($path != '/' && substr($path, -1) == '/' && $uri != $index) {
        // recursively remove slashes when its more than 1 slash
        $path = rtrim($path, '/');

        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath($path);
        
        if ($request->getMethod() == 'GET') {
            $response = new ResponsePsr7();
            return $response
            ->withHeader('Location', (string) $uri)
            ->withStatus(301);
        } else {
            $request = $request->withUri($uri);
        }
    }

    return $handler->handle($request);
});