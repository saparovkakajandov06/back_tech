<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Services\ServicesService;

class ServicesController extends Controller
{
    #[Endpoint('extern_services')]
    #[Verbs(D::GET)]
    #[Group('other')]
    #[Text('Список внешних сервисов')]
    public function index(ServicesService $ss)
    {
        return [
            'services' => $ss->getServices(),
        ];
    }
}
