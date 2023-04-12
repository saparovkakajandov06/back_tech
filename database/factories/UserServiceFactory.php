<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Database\Factories;

use App\Domain\Models\Labels;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Instagram\SetImgFromLinkAsMediaUrl;
use App\Domain\Transformers\Instagram\SetLoginFromLinkAsMediaUrl;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckLinkAsMediaUrl;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\UserService;
use App\USPrice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserService::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tag = 'TEST_SVC_' . Str::random(6);

        USPrice::factory()->create([
            'id' => 100 + rand(100, 100000000),
            'tag' => $tag,
        ]);

        return [
            'title' => 'Тестовый сервис',
            'tag' => $tag,
            'labels' => [
                Labels::TYPE_LIKES,
                Labels::DISCOUNT_LIKES,
                Labels::VISIBLE,
                Labels::ENABLED,
                Labels::CLIENT_LK,
            ],
            'pipeline' => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                CheckLinkAsMediaUrl::class,
                SetImgFromLinkAsMediaUrl::class,
                SetLoginFromLinkAsMediaUrl::class,

                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            'splitter' => DefaultSplitter::class,
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => 100,
                    'remote_params' => [
                        'service' => 88,
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 4.187,
                        'mode' => AbstractService::NET_COST_DISABLED,
                        'auto' => 123,
                        'auto_timestamp' => null,
                    ],
                ]
            ]
        ];
    }
}
