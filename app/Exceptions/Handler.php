<?php

namespace App\Exceptions;

use App\Traits\HttpResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    use HttpResponseTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return $this->error('Route not found', [], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->error('Method not allowed', [], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return $this->error('Error on fields', ['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof TokenExpiredException) {
            return $this->error('Token expired', [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof TokenInvalidException) {
            return $this->error('Token invalid', [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof JWTException) {
            return $this->error('Token absent', [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof \ErrorException) {
            return $this->error('An unexpected error occurred on the server. Please try again later or contact support if the problem persists.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof RoleAlreadyExists) {
            return $this->error('The role already exists', [], Response::HTTP_FORBIDDEN);
        }

        return parent::render($request, $exception);
    }
}
