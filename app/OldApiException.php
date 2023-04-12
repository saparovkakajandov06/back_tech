<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiException extends \Exception
{
    public $data;

    public function __construct($message, $data=null)
    {
        parent::__construct($message);

        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function report(Request $request)
    {
        $message = $this->getMessage();
        $file = $this->getFile();
        $line = $this->getLine();
        $data = json_encode($this->getData());
        $params = json_encode($request->all());

        Log::error("ApiException: $message $data on $file, $line request params $params");
    }

    public function render($request)
    {
        $this->responseData['error'] = get_class($this);
        $this->responseData['message'] = __($this->messageKey);
//        $this->responseData['locale'] = App::getLocale();
//        $this->responseData['translated'] = __($this->msg);

        return response()->json($this->responseData, $this->responseCode);
    }
}
