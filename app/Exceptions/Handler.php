<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
            // Log all errors with context
            if ($this->shouldReport($e)) {
                \Log::error('Application Error: ' . get_class($e), [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user_id' => auth()->id() ?? 'guest',
                    'user_agent' => request()->userAgent(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : 'Hidden in production',
                ]);
            }
        });

        // Custom exception rendering
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    /**
     * Handle exception rendering
     */
    private function handleException(Throwable $e, $request)
    {
        // Check if the request is for Filament admin panel
        $isFilamentRequest = $request->is('admin/*') ||
                           $request->is('filament/*') ||
                           str_contains($request->path(), 'livewire');

        // Handle specific exceptions
        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($e, $request, $isFilamentRequest);
        }

        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            return $this->handleNotFoundException($e, $request, $isFilamentRequest);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($e, $request, $isFilamentRequest);
        }

        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e, $request, $isFilamentRequest);
        }

        if ($e instanceof HttpException && $e->getStatusCode() === 403) {
            return $this->handleForbiddenException($e, $request, $isFilamentRequest);
        }

        if ($e instanceof HttpException && $e->getStatusCode() === 400) {
            return $this->handleBadRequestException($e, $request, $isFilamentRequest);
        }

        // Handle all other exceptions as 500
        return $this->handleServerErrorException($e, $request, $isFilamentRequest);
    }

    /**
     * Handle 404 Not Found errors
     */
    private function handleNotFoundException(Throwable $e, $request, $isFilamentRequest)
    {
        $statusCode = 404;

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'The requested resource could not be found.'
            ], $statusCode);
        }

        if ($isFilamentRequest) {
            return response()->view('errors.filament.404', [
                'title' => 'Page Not Found',
                'message' => 'The page you are looking for could not be found.',
                'exception' => config('app.debug') ? $e : null,
            ], $statusCode);
        }

        return response()->view('errors.generic', [
            'errorCode' => $statusCode,
            'errorTitle' => 'Page Not Found',
            'errorMessage' => 'The page you are looking for could not be found.',
            'showDetails' => config('app.debug'),
            'exception' => config('app.debug') ? $e : null,
        ], $statusCode);
    }

    /**
     * Handle 500 Internal Server errors
     */
    private function handleServerErrorException(Throwable $e, $request, $isFilamentRequest)
    {
        $statusCode = 500;

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => config('app.debug') ? $e->getMessage() : 'An internal server error occurred.'
            ], $statusCode);
        }

        if ($isFilamentRequest) {
            return response()->view('errors.filament.500', [
                'title' => 'Internal Server Error',
                'message' => config('app.debug') ? $e->getMessage() : 'Something went wrong. Please try again later.',
                'exception' => config('app.debug') ? $e : null,
            ], $statusCode);
        }

        return response()->view('errors.generic', [
            'errorCode' => $statusCode,
            'errorTitle' => 'Internal Server Error',
            'errorMessage' => config('app.debug') ? $e->getMessage() : 'Something went wrong. Please try again later.',
            'showDetails' => config('app.debug'),
            'exception' => config('app.debug') ? $e : null,
        ], $statusCode);
    }

    /**
     * Handle 400 Bad Request errors
     */
    private function handleBadRequestException(Throwable $e, $request, $isFilamentRequest)
    {
        $statusCode = 400;

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => $e->getMessage() ?: 'The request could not be understood by the server.'
            ], $statusCode);
        }

        if ($isFilamentRequest) {
            return response()->view('errors.filament.400', [
                'title' => 'Bad Request',
                'message' => $e->getMessage() ?: 'The request contains invalid data.',
                'exception' => config('app.debug') ? $e : null,
            ], $statusCode);
        }

        return response()->view('errors.generic', [
            'errorCode' => $statusCode,
            'errorTitle' => 'Bad Request',
            'errorMessage' => $e->getMessage() ?: 'The request contains invalid data.',
            'showDetails' => config('app.debug'),
            'exception' => config('app.debug') ? $e : null,
        ], $statusCode);
    }

    /**
     * Handle 403 Forbidden errors
     */
    private function handleForbiddenException(Throwable $e, $request, $isFilamentRequest)
    {
        $statusCode = 403;

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.'
            ], $statusCode);
        }

        if ($isFilamentRequest) {
            return response()->view('errors.filament.403', [
                'title' => 'Access Denied',
                'message' => 'You do not have permission to access this resource.',
                'exception' => config('app.debug') ? $e : null,
            ], $statusCode);
        }

        return response()->view('errors.generic', [
            'errorCode' => $statusCode,
            'errorTitle' => 'Access Denied',
            'errorMessage' => 'You do not have permission to access this resource.',
            'showDetails' => config('app.debug'),
            'exception' => config('app.debug') ? $e : null,
        ], $statusCode);
    }

    /**
     * Handle Authentication errors
     */
    private function handleAuthenticationException(Throwable $e, $request, $isFilamentRequest)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be logged in to access this resource.'
            ], 401);
        }

        if ($isFilamentRequest) {
            return redirect()->route('filament.admin.auth.login');
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Handle 405 Method Not Allowed errors
     */
    private function handleMethodNotAllowedException(Throwable $e, $request, $isFilamentRequest)
    {
        $statusCode = 405;

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Method Not Allowed',
                'message' => 'The requested method is not allowed for this resource.'
            ], $statusCode);
        }

        if ($isFilamentRequest) {
            return response()->view('errors.filament.405', [
                'title' => 'Method Not Allowed',
                'message' => 'The requested method is not allowed for this resource.',
                'allowedMethods' => $e->getHeaders()['Allow'] ?? 'GET, POST, PUT, DELETE, PATCH',
            ], $statusCode);
        }

        return response()->view('errors.generic', [
            'errorCode' => $statusCode,
            'errorTitle' => 'Method Not Allowed',
            'errorMessage' => 'The requested method is not allowed for this resource.',
            'showDetails' => config('app.debug'),
        ], $statusCode);
    }

    /**
     * Handle Validation errors
     */
    private function handleValidationException(ValidationException $e, $request, $isFilamentRequest)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }

        // For Filament, let it handle validation errors normally
        if ($isFilamentRequest) {
            return parent::render($request, $e);
        }

        return redirect()->back()
            ->withInput($request->input())
            ->withErrors($e->errors());
    }
}
