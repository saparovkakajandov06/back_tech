<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Requests\NotificationPostRequest;
use App\Notification\Notification;
use Illuminate\Http\JsonResponse;

class NotificationsController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/notifications",
     *      operationId="getNotifications",
     *      tags={"Notifications"},
     *      summary="Get list of notifications",
     *      description="Returns list of notifications",
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          in="header",
     *          name="bearerAuth",
     *          scheme="bearer",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *     )
     * Returns list of notifications
     */
    #[Endpoint('notifications')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('other')]
    #[Text('Возвращает список уведомлений')]
    public function index(): iterable
    {
        $notifications = Notification::unread();

        return $notifications;
    }

    /**
     * @OA\Post(
     *      path="/api/notifications",
     *      operationId="postNotifications",
     *      tags={"Notifications"},
     *      summary="Post a new notification",
     *      description="Returns list of notifications",
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          in="header",
     *          name="bearerAuth",
     *          scheme="bearer",
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/NotificationPostRequest")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *     )
     * Returns made notification
     */
    #[Endpoint('notifications')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('other')]
    #[Text('Создание уведомления')]
    #[Param('content', true, D::STRING)]
    public function store(NotificationPostRequest $request): JsonResponse
    {
        Notification::create($request->all());

        return new JsonResponse([
            'success' => 'It is created.'
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/notifications/{id}",
     *      operationId="getNotifications",
     *      tags={"Notifications"},
     *      summary="Update selected notification",
     *      description="Returns updated notification",
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          in="header",
     *          name="bearerAuth",
     *          scheme="bearer",
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/NotificationPostRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *     @OA\Response(
     *      response=400,
     *      description="Bad data was given."
     * )
     *     )
     * Returns updated notification
     */
    #[Endpoint('notifications/{notification}')]
    #[Verbs(D::PUT)]
    #[Role('ROLE_ANY')]
    #[Group('other')]
    #[Text('Редактирование уведомления')]
    #[Param('id', true, D::INT)]
    #[Param('content', true, D::STRING)]
    public function update(NotificationPostRequest $request, Notification $notification): JsonResponse
    {
        $notification->update($request->all());

        return new JsonResponse([
            'success' => 'It is updated.'
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/notifications/{id}",
     *      operationId="destroyNotifications",
     *      tags={"Notifications"},
     *      summary="Delete selected notification",
     *      description="Delete selected notification",
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          in="header",
     *          name="bearerAuth",
     *          scheme="bearer",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *     )
     * Returns json response.
     */
    #[Endpoint('notifications/{notification}')]
    #[Verbs(D::DELETE)]
    #[Role('ROLE_ANY')]
    #[Group('other')]
    #[Text('Удаление уведомления')]
    #[Param('id', true, D::INT)]
    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();

        return new JsonResponse([
            'success' => 'It is deleted.'
        ]);
    }
}
