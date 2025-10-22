<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
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
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        PhongException::class => 'warning',
        SlotException::class => 'warning',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Xử lý PhongException
        $this->renderable(function (PhongException $e, $request) {
            Log::warning('PhongException: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        });

        // Xử lý SlotException
        $this->renderable(function (SlotException $e, $request) {
            Log::warning('SlotException: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        });

        // Xử lý các exception chung
        $this->reportable(function (Throwable $e) {
            if (!($e instanceof PhongException || $e instanceof SlotException)) {
                Log::error('Exception: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }
}
