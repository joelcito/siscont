<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
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

        // $this->renderable(function (TokenMismatchException $e, $request) {
        //     return redirect()
        //         ->back()
        //         ->withInput($request->except('_token'))
        //         ->with('error', 'Tu sesión ha expirado. Por favor, vuelve a intentarlo.');
        // });

        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                // Manejar la respuesta para solicitudes AJAX
                return response()->json([
                    'message' => 'Tu sesión ha expirado. Por favor, vuelve a cargar la página y vuelve a intentarlo.',
                    'error' => 'CSRF token mismatch'
                ], 419); // 419 es el código de estado HTTP para errores relacionados con CSRF
            } else {
                // Manejar la respuesta para solicitudes no AJAX
                return redirect()
                    ->back()
                    ->withInput($request->except('_token'))
                    ->with('error', 'Tu sesión ha expirado. Por favor, vuelve a intentarlo.');
            }
        });
    }
}
