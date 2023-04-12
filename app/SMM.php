<?php
//
//namespace App;
//
//use App\Exceptions\ValidationException;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//use Symfony\Component\HttpFoundation\Response;
//
//class SMM
//{
//    public static function validate(Request $request, array $rules) {
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            throw ValidationException::create([
//                'fields' => $validator->errors()
//            ]);
//
////            $function = debug_backtrace()[1]['function'];
////            $class = debug_backtrace()[1]['class'];
////            $line = debug_backtrace()[1]['line'];
////            $msg = "$class@$function:$line";
////
////            throw new ApiException('SMM Validation Exception ' .
////                $validator->errors()->toJson() . $msg, []);
//        }
//    }
//
//    public static function success($data, $code = Response::HTTP_OK) {
//        return response()->json(['success' => $data], $code);
//    }
//}
