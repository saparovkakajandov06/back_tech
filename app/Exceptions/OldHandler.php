<?php
//
//namespace App\Exceptions;
//
//use App\Responses\ApiError;
//use Exception;
//use Illuminate\Auth\AuthenticationException;
//use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
//use Illuminate\Support\Facades\Log;
//use Symfony\Component\ErrorHandler\Exception\FlattenException;
//use Throwable;
//use Illuminate\Validation\ValidationException;
//
//class Handler extends ExceptionHandler
//{
//    /**
//     * A list of the exception types that are not reported.
//     *
//     * @var array
//     */
//    protected $dontReport = [
//        //
//    ];
//
//    /**
//     * A list of the inputs that are never flashed for validation exceptions.
//     *
//     * @var array
//     */
//    protected $dontFlash = [
//        'password',
//        'password_confirmation',
//    ];
//
//    /**
//     * Report or log an exception.
//     *
//     * @param \Exception $exception
//     * @return void
//     */
//    public function report(Throwable $exception)
//    {
//        if (env('LOG_SLACK_WEBHOOK_URL')) {
//            $e = FlattenException::createFromThrowable($exception);
//            $msg = $e->getMessage();
//            $file = $e->getFile();
//            $line = $e->getLine();
//            $name = env('APP_NAME');
//
//            Log::channel('slack')->error("[$name] $msg $file:$line");
//        }
//
//        parent::report($exception);
//    }
//
//    /**
//     * Render an exception into an HTTP response.
//     *
//     * @param \Illuminate\Http\Request $request
//     * @param \Exception $exception
//     * @return \Illuminate\Http\Response
//     */
//    public function render($request, Throwable $exception)
//    {
//        if ($exception instanceof AuthenticationException) {
//            return response((new ApiError('Authentication Exception', [
//                'message' => 'Unauthenticated',
//            ])));
//        } elseif ($exception instanceof ValidationException) {
//            return response((new ApiError('Validation exception', [
//                'file' => $exception->getFile(),
//                'line' => $exception->getLine(),
//                'message' => $exception->getMessage(),
//                'errors' => $exception->errors(),
//                'class' => get_class($exception),
//                'trace' => $exception->getTraceAsString(),
//            ])));
//        } elseif ($exception instanceof APIException) {
//            return response((new ApiError('APIException', [
//                'file' => $exception->getFile(),
//                'line' => $exception->getLine(),
//                'message' => $exception->getMessage(),
//                'class' => get_class($exception),
//                'text' => $exception->responseData,
//                'trace' => $exception->getTraceAsString(),
//            ])));
//        } elseif ($request->wantsJson()) {
//            return response((new ApiError('Exception caught in handler', [
//                'file' => $exception->getFile(),
//                'line' => $exception->getLine(),
//                'message' => $exception->getMessage(),
//                'class' => get_class($exception),
//                'trace' => $exception->getTraceAsString(),
//            ])));
//        } else {
//            return parent::render($request, $exception);
//        }
//    }
//}
