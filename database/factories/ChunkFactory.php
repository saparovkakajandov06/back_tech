<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Database\Factories;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChunkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Chunk::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $medias = collect([
            'https://www.instagram.com/p/BxxXcwaA5EF/',
            'https://www.instagram.com/p/ByFwgy6A-6w/',
            'https://www.instagram.com/p/ByIoyNvgJEH/',
            'https://www.instagram.com/p/BySmlrJAds-/',
            'https://www.instagram.com/p/BykwFIggdsz/',
            'https://www.instagram.com/p/BztKDwzg5DP/',
            'https://www.instagram.com/p/B1bl0pYB17w/',
            'https://www.instagram.com/p/B2kIuzAAIqk/',
            'https://www.instagram.com/p/B58XDpnANfT/',
            'https://www.instagram.com/p/B7UPN-dnrnN/',
        ]);

        $order = CompositeOrder::factory()->create();

        return [
            'service_class' => ANakrutka::class,
            'composite_order_id' => $order->id,
            'details' => [
                'link' => $medias->random(),
                'count' => rand(1, 3),
            ],
            'completed' => 0,
            'extern_id' => null,
            'status' => Order::STATUS_RUNNING,
        ];
    }
}
