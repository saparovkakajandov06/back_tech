<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use Illuminate\Http\Request;
use App\Responses\ApiSuccess;

class ImageUploadController extends Controller
{
     /**
     * @OA\Post(
     *      path="/api/img/upload",
     *      operationId="imageUploadPost",
     *      tags={"ImageUpload"},
     *      summary="Loading an image",
     *      description="Загрузка изображения",
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          in="header",
     *          name="bearerAuth",
     *          scheme="bearer",
     *      ),
     *      @OA\Parameter(
     *          name="image",
     *          in="query",
     *          description="The image parameter file in query",
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password changed"
     *      ),
     *    )
     * Loading an image
     */
    #[Endpoint('img/upload')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('other')]
    #[Text('Загрузка изображений')]
    #[Param('image', true, D::IMAGE)]
    public function imageUploadPost(Request $request)
    {        
        return $request->file('image')->store('uploads', 'public');
    }
}
