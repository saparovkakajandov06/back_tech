<?php


namespace App\Http\Controllers\Admin;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\Http\Requests\LimitOffsetRequest;
use App\Http\Requests\PaginateApiRequest;
use App\Repositories\UserRepository;
use App\Responses\ApiError;
use App\Responses\ApiSuccess;
use App\User;
use Illuminate\Http\Request;

class ReferalController extends Controller
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * ReferalController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param PaginateApiRequest $request
     * @param $user_id
     * @return ApiError|ApiSuccess
     */
    #[Endpoint('admin/user/{user_id}/refs')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Group('user')]
    #[Text('Получить список рефералов юзера')]
    #[Param('user_id', true, D::INT)]
    #[Param('limit', true, D::INT)]
    #[Param('offset', true, D::INT)]
    public function index(Request $request, $userId)
    {
        try {
            [$items, $count] = $this->userRepository->getRefs(
                (int) $userId, $request->offset, $request->limit);

            return new ApiSuccess('refs', [
                'items' => $items,
                'count' => $count,
            ]);
        } catch (\Throwable $exception) {
            return new ApiError($exception->getMessage(), $exception);
        }
    }
}
