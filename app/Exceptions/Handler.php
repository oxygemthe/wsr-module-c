<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->is("api/*")) {
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Not found'
                ], 404);
            }
        }
        return parent::render($request, $e);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return response()->json([
            'success' => false,
            'message' => $e->errors()
        ], 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => 'Login failed'
            ], 403);
        }
    }
}
