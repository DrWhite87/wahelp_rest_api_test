<?php

use Buki\Router\Router;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// инициализируем маршрутизатор
$router = new Router([
    'paths' => [
        'controllers' => 'app\Controllers',
    ],
    'namespaces' => [
        'controllers' => 'App\Controllers',
    ],
], null, new Response('', Response::HTTP_OK, ['content-type' => 'application/json']));

// кастомизируем ошибку 404
$router->notFound(function (Request $request, Response $response) {
    $response->setStatusCode(Response::HTTP_NOT_FOUND);
    $response->setContent(json_encode([
        'status' => Response::HTTP_NOT_FOUND,
        'message' => 'Looks like page not found or something went wrong. Please try again.'
    ]));
    return $response;
});

// кастомизируем ошибку 500
$router->error(function (Request $request, Response $response, Exception $exception) {
    $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    $response->setContent(json_encode([
        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
        'message' => 'Oops! Something went wrong. Please try again.',
        'error' => $exception->getMessage()
    ]));
    return $response;
});


// создаем маршруты
$router->post('/generate', [App\Controllers\RandomNumController::class, 'generate']);
$router->get('/retrieve/:id', [App\Controllers\RandomNumController::class, 'retrieve']);
$router->get('/list', [App\Controllers\RandomNumController::class, 'list']);

$router->run();