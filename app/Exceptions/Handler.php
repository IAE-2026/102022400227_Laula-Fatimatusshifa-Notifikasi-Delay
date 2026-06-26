<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        if ($request->is('api/*') || $request->expectsJson()) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Method Not Allowed',
                    'errors' => null
                ], 405);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource Not Found',
                    'errors' => null
                ], 404);
            }

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'errors' => null
                ], 401);
            }

            $statusCode = 500;
            if (method_exists($e, 'getStatusCode')) {
                $statusCode = $e->getStatusCode();
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Internal Server Error',
                'errors' => null
            ], $statusCode);
        }

        return parent::render($request, $e);
    }
}
