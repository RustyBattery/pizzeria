<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $previousException = $e->getPrevious();
                $previousExceptionClass = $previousException ? new ReflectionClass($previousException) : null;
                if ($previousExceptionClass && $previousExceptionClass->getShortName() === 'ModelNotFoundException') {
                    preg_match('/.*\[(.+)].*/', $previousException->getMessage(), $matches);
                    $modelClassName = $matches[1] ?? null;
                    if (!$modelClassName) {
                        return response()->json(['message' => 'Not found'], ResponseAlias::HTTP_NOT_FOUND);
                    }
                    $modelClass = new ReflectionClass($modelClassName);
                    return response()->json(['message' => $modelClass->getShortName() . ' not found'], ResponseAlias::HTTP_NOT_FOUND);
                }
                return response()->json(['message' => 'Not found'], ResponseAlias::HTTP_NOT_FOUND);
            }
            return parent::render($request, $e);
        });
    }
}
