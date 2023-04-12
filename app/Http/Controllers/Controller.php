<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
    * @OA\Info(
    *      version="1.0.0",
    *      title="L5 OpenApi",
    *      description="Smm Api Documentation",
    *      @OA\Contact(
    *          email="takahiroasiro@gmail.com"
    *      ),
    *     @OA\License(
    *         name="Apache 2.0",
    *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
    *     )
    * )
    *
    * @OA\Tag(
    *     name="Notifications"
    * )
    */
}
