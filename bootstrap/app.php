<?php

use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use PHPUnit\Util\InvalidJsonException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '/api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([ValidateJsonApiHeaders::class]);
        $middleware->api([ValidateJsonApiDocument::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request){

            return new JsonApiValidationErrorResponse($exception,);
        });
    })->create();