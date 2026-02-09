<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests with consistent error format
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions with consistent format.
     */
    protected function handleApiException($request, Throwable $e)
    {
        // Handle validation exceptions
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных.',
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle HTTP exceptions (like abort())
        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Произошла ошибка.',
                'errors' => null,
            ], $e->getStatusCode());
        }

        // Handle model not found
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Ресурс не найден.',
                'errors' => null,
            ], 404);
        }

        // Handle other exceptions
        $statusCode = method_exists($e, 'getStatusCode') 
            ? $e->getStatusCode() 
            : 500;

        $message = config('app.debug') 
            ? $e->getMessage() 
            : 'Внутренняя ошибка сервера.';

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => null,
        ], $statusCode);
    }
}

