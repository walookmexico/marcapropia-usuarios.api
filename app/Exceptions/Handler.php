<?php

namespace App\Exceptions;

use App\Traits\HttpResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use PDOException;
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
            return $this->error(trans('exception.not_found'), [], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->error(trans('exception.method_not_allowed'), [], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return $this->error(trans('exception.validation_error'), ['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof TokenExpiredException) {
            return $this->error(trans('token.token_expired'), [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof TokenInvalidException) {
            return $this->error(trans('token.token_invalid'), [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof JWTException) {
            return $this->error(trans('token.token_absent'), [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof \ErrorException) {
            return $this->error(trans('exception.unexpected_error'), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof PDOException) {
            return $this->error(trans('exception.unexpected_error'), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof RoleAlreadyExists) {
            return $this->error(trans('role.role_already_exists'), [], Response::HTTP_FORBIDDEN);
        }

        return parent::render($request, $exception);
    }
}
