<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, \Throwable $throwable): bool => $request->is('api/*')
        );

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Ошибка валидации.',
                'errors' => $exception->errors(),
            ], $exception->status);
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Ресурс не найден.',
            ], 404);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $statusCode = $exception->getStatusCode();

            return response()->json([
                'message' => match ($statusCode) {
                    401 => 'Требуется авторизация.',
                    403 => 'Доступ запрещён.',
                    404 => 'Маршрут не найден.',
                    405 => 'Метод запроса не поддерживается.',
                    409 => 'Конфликт запроса.',
                    422 => 'Ошибка валидации.',
                    429 => 'Слишком много запросов. Повторите позже.',
                    default => 'Ошибка запроса.',
                },
            ], $statusCode);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Внутренняя ошибка сервера.',
            ], 500);
        });
    })->create();
