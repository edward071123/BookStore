<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

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
        if ($exception instanceof TokenFalseException) {
            $code       = config('code.tokenExceptionError');
            $message    = $exception->errors();
            $statusCode = 400;
            return $this->responseError($code, $statusCode, $message);
        }

        if ($exception instanceof CustomException) {
            $code       = config('code.tokenExceptionError');
            $message    = $exception->errors();
            $statusCode = 404;
            return $this->responseError($code, $statusCode, $message);
        }

        return parent::render($request, $exception);
    }

    private function responseError(int $code, int $status, string $message)
    {
        return response()->json([
            'code'    => $code,
            'message' => $message
        ], $status);
    }
}
