<?php

namespace App\Exceptions;

use App\Exceptions\Auth\CreateTokenException;
use App\Exceptions\Auth\InvalidEmailException;
use App\Exceptions\Auth\InvalidPasswordException;
use App\Exceptions\Auth\RegisterException;
use App\Exceptions\Cart\CartProductLimitException;
use App\Exceptions\Cart\CartProductOutStockException;
use App\Exceptions\Cart\DuplicateCartProductException;
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

        $this->renderable(function (InvalidPasswordException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Invalid password'], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }
            return parent::render($request, $e);
        });

        $this->renderable(function (InvalidEmailException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Invalid email'], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
            }
            return parent::render($request, $e);
        });

        $this->renderable(function (CreateTokenException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'An error occurred when issuing tokens'], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
            }
            return parent::render($request, $e);
        });

        $this->renderable(function (RegisterException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'An error occurred while trying to register'], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
            }
            return parent::render($request, $e);
        });

        $this->renderable(function (DuplicateCartProductException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'The product is already in the shopping cart'], ResponseAlias::HTTP_BAD_REQUEST);
            }
            return parent::render($request, $e);
        });

        $this->renderable(function (CartProductOutStockException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'The product is currently out of stock'], ResponseAlias::HTTP_BAD_REQUEST);
            }
            return parent::render($request, $e);
        });

        $this->renderable(function (CartProductLimitException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'The limit on the number of products in the cart of this category has been reached'], ResponseAlias::HTTP_BAD_REQUEST);
            }
            return parent::render($request, $e);
        });
    }
}
