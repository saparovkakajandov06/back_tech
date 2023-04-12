<?php
//
//namespace App\Exceptions;
//
//use Exception;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;
//use Symfony\Component\HttpFoundation\Response;
//use Throwable;
//
//abstract class APIException extends Exception
//{
//    protected $messageKey = "exceptions.default";
//    public $responseData = [];
//    protected $responseCode = Response::HTTP_OK;
//
//    protected function __construct(string $message = "", int $code = 0, Throwable $previous = null)
//    {
//        parent::__construct($message, $code, $previous);
//    }
//
//    public static function create($args = [])
//    {
//        $e = new static;
//
//        foreach ($args as $k => $v) {
//            $e->responseData[$k] = $v;
//        }
//
//        return $e;
//    }
//
//    public function report(Request $request)
//    {
//        $clazz = get_class($this);
//        $text = json_encode($this->responseData);
//        $params = json_encode($request->all());
//        Log::info("$clazz: $text request params $params");
//    }
//
//    /**
//     * Render the exception into an HTTP response.
//     *
//     * @param \Illuminate\Http\Request
//     * @return \Illuminate\Http\Response
//     */
//    public function render($request)
//    {
//        return response()->json([
//            'key' => 'value',
//        ]);
//    }
//}
