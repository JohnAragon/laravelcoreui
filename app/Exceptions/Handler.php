<?php

namespace App\Exceptions;

use App\Utilities\helpers;
use BadMethodCallException;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
   * Report or log an exception.
   *
   * @param  \Throwable  $exception
   * @return void
   *
   * @throws \Throwable
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
   * @return \Symfony\Component\HttpFoundation\Response
   *
   * @throws \Throwable
   */
  public function render($request, Throwable $exception)
  {
    if ($exception instanceof AuthorizationException && $request->isJson()) {
      return helpers::ResponseJson("Usuario no autenticado", null, "ERROR", helpers::fail_auth);
    }
    if ($exception instanceof MethodNotAllowedHttpException && $request->isJson()) {
      return helpers::ResponseJson(helpers::$error, ["Method" => "Esta ruta no soporta el metodo usado"], "ERROR", helpers::error_general);
    }
    if ($exception instanceof ModelNotFoundException && $request->isJson()) {
      return helpers::ResponseJson(helpers::$not_result, null, "ERROR", helpers::nofound);
    }
    if ($exception instanceof QueryException && $request->isJson()) {
      return helpers::ResponseJson($exception->errorInfo[0], $exception->errorInfo[2], "ERROR", helpers::error_db_handled);
    }
    if ($exception instanceof BadMethodCallException && $request->isJson()) {
      return helpers::ResponseJson($exception->getMessage(), null, "ERROR", helpers::error_processing_data);
    }
    return parent::render($request, $exception);
  }
}
