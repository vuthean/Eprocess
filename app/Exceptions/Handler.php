<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // $this->renderable(function (isHttpException $e, $request) {
        //     return response()->view('errors.404', [], 404);
        // });
        // if ($this->isHttpException($exception)) {
        //     return response()->view('error.404');
        // }
        // else{
        //     return response()->view('error.404');
        // }
    }

    public function render($request, Throwable $exception)
    {
         // Let Laravel handle unauthenticated separately
         if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        // Force all other exceptions to 404 page
        return response()->view('error.404', [
            'message' => $exception->getMessage(),
        ], 404);
    }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
