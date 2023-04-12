<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Action;
use App\Domain\Models\Chunk;
use App\Domain\Services\Local\ALocal;
use App\Events\Money\InflowUserJob;
use App\Order;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group userjobs
 */
class UserJobsController extends Controller
{
    const indexMessage = 'Доступные задания';

    /**
     * @OA\Get(
     *      path="/api/userjobs",
     *      operationId="index",
     *      tags={"UserJobs"},
     *      summary="List available jobs for user",
     *      description="Доступные задания для пользователя",
     *      @OA\Response(
     *          response=200,
     *          description="Доступные задания"
     *       ),
     *     )
     * List available jobs for user
     */
    #[Endpoint('userjobs')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('user')]
    #[Text('Список доступных заданий для пользователя')]
    public function index(): ApiResponse
    {
        $createdActions = Auth::user()->chunks->pluck('id')->all();
        $available = Chunk::whereServiceClass(ALocal::class)
                          ->whereStatus(Order::STATUS_RUNNING)
                          ->whereNotIn('id', $createdActions)
                          ->get();

        return new ApiSuccess(self::indexMessage, $available);
    }

    /**
     * @OA\Post(
     *      path="/api/userjobs/{id}",
     *      operationId="create",
     *      tags={"UserJobs"},
     *      summary="Create new action",
     *      description="Создает action для локального задания",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="The id parametr in query",
     *          required=true,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="ok, action created"
     *       ),
     *     )
     * Create new action
     */
    #[Endpoint('userjobs/{id}')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('user')]
    #[Text('Взять задание')]
    #[Param('id', true, D::STRING)]
    public function create(string $id): ApiResponse
    {
        $user = Auth::user();
        $chunk = Chunk::findOrFail($id);

        $order = $chunk->compositeOrder;
        $userService = $order->userService;
        $userService->processLocalValidation($order->params);

        if (Action::where('user_id', $user->id)
                   ->where('chunk_id', $chunk->id)
                   ->exists()) {
            return new ApiError('action exists');
        }

        $action = Action::create([
            'user_id' => $user->id,
            'chunk_id' => $chunk->id,
        ]);

        $user->refresh();

        return new ApiSuccess('ok, action created', $action);
    }

    /**
     * @OA\Post(
     *      path="/api/userjobs/check/{actionId}",
     *      operationId="check",
     *      tags={"UserJobs"},
     *      summary="Check user job status",
     *      description=" Запрос на проверку статуса задания",
     *      @OA\Parameter(
     *          name="actionId",
     *          in="query",
     *          description="The actionId parametr in query",
     *          required=true,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Результат поиска"
     *       ),
     *     )
     * Check user job status
     */
    #[Endpoint('userjobs/check/{actionId}')]
    #[Verbs(D::POST)]
    #[Group('user')]
    #[Text('Проверить задание')]
    #[Param('id', true, D::STRING)]
    public function check(string $actionId, MoneyService $m): ApiResponse
    {
        $action = Action::findOrFail($actionId);
        
        $service = $action->chunk->compositeOrder->userService;
        
        if ($service->check($action)) {
            $action->update(['completed' => 1]);

            // оплатить
            $amount = $service->getPrice(1); // todo add cur
//            event(new InflowUserJob($action->user,
//                                    $amount,
//                                    'Оплата action ' . $actionId));

            $m->inflow($action->user, $amount, Transaction::INFLOW_USER_JOB,
                'Оплата action ' . $actionId);

            $action->update(['paid' => 1]);
            return new ApiSuccess('Задание выполнено', [
                'action' => $actionId,
                'chunk' => $action->chunk_id,
                'user' => $action->user_id,
            ]);
        } else {
            return new ApiError('Задание не выполнено');
        }
    }
}
