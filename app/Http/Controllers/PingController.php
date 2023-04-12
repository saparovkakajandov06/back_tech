<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Role\UserRole;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Exceptions\Reportable\DistributorException;

class PingController extends Controller
{
    #[Endpoint('ping')]
    #[Verbs(D::GET)]
    #[Group('test')]
    #[Text('Тестовый метод')]
    #[Text('В ответе возвращает pong')]
    public function pong(): ApiResponse
    {
        return new ApiSuccess('pong');
    }

    #[Endpoint('long')]
    #[Verbs(D::GET)]
    #[Group('test')]
    #[Text('Тестовый метод, ответ приходит с задержкой')]
    #[Param('sleep', false, D::INT, 'sleep time in seconds')]
    public function long(Request $request): ApiResponse
    {
        $s = $request->sleep ?: 1;
        sleep($s);

        return new ApiSuccess('long time', [
            'sleep' => $s,
        ]);
    }

    #[Endpoint('slow')]
    #[Verbs(D::ANY)]
    #[Group('test')]
    #[Text('Тестовый метод, с запросом к бд, и с рандомным юзером в отевете')]
    public function slow()
    {
        $user = User::find(rand(1000, 100000));

        return [
            'name' => $user->name
        ];
    }

    #[Endpoint('test_auth')]
    #[Verbs(D::ANY)]
    #[Role('ROLE_ANY')]
    #[Group('test')]
    #[Text('Тестовый метод, для авторизованных юзеров')]
    public function testAuth(): ApiResponse
    {
        return new ApiSuccess('test auth');
    }

    #[Endpoint('test_moderator')]
    #[Verbs(D::ANY)]
    #[Role('ROLE_MODERATOR')]
    #[Group('test')]
    #[Text('Тестовый метод, для модераторов')]
    public function testModerator(): ApiResponse
    {
        return new ApiSuccess('test moderator');
    }

    #[Endpoint('test_admin')]
    #[Verbs(D::ANY)]
    #[Role('ROLE_ADMIN')]
    #[Group('test')]
    #[Text('Тестовый метод, для админов')]
    public function testAdmin(): ApiResponse
    {
        return new ApiSuccess('test admin');
    }

    #[Endpoint('test/exception')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MODERATOR')]
    #[Group('test')]
    #[Text('Тестовый метод, для вывода исключений(exceptions)')]
    #[Param('file', true, D::STRING, 'the name of the file which contains the exceptions', 'exceptions')]
    #[Param('key', true, D::STRING, 'key in file', 'no_price_in_cur')]
    #[Param('message_params', false, D::TYPE_ARRAY, 'names of the params and values', 'params: [[\'service\', \'INSTAGRAM_LIKES_MAIN\'],[\'cur\':\'USD\'')]
    public function testException(Request $request)
    {
        $request->validate([
            'file' => 'required|string',
            'key' => 'required|string',
            'message_params' => 'array|min:1',
            'message_params.*' => 'array|min:2',
            'message_params.*' => 'array|min:2',
            'message_params.*.*' => 'string|min:1',
            'message_params.*.*' => 'string|min:1',
        ]);

        $keyValues = make_data_getter($request->message_params);
        $values = make_data_getter($request->message_params);
        
        throw new DistributorException(__("{$request->file}.{$request->key}", [
            $keyValues('0.0') => $values('0.1'),
            $keyValues('1.0') =>  $values('1.1'),
        ]));
    }
}
