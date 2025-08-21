<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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

 // In app/Exceptions/Handler.php
public function render($request, Throwable $e)
{
    if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json([
            'message' => 'An error occurred.',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
        ], $this->getHttpStatusCode($e));
    }
    return parent::render($request, $e);
}

private function getHttpStatusCode(Throwable $e): int
{
    if (method_exists($e, 'getStatusCode')) {
        return $e->getStatusCode();
    }
    if ($e instanceof \Illuminate\Validation\ValidationException) {
        return 422;
    }
    return 500;
}
}
