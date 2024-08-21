<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Middleware\AlwaysAcceptJson;
use App\Helpers\APIHelper;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AlwaysAcceptJson::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response) {
            $status_code = $response->getStatusCode();

            if ($status_code === 401) {
                return APIHelper::errorResponse(statusCode: 401, message: 'Unauthenticated');
            }

            if ($status_code === 403) {
                return APIHelper::errorResponse(statusCode: 403, message: 'You have no permission.');
            }
        });
    })->create();