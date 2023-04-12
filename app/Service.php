<?php
//
//namespace App;
//
//use App\Exceptions\CostException;
//
//class Service extends BaseModel {
//
//    protected $casts = [
//        'info' => 'array',
//        'price_list' => 'array',
//    ];
//
//    public static function getByType(string $type) {
//        return self::where('type', $type)->first();
//    }
//
//    public function orders() {
//        return $this->hasMany('App\Order');
//    }
//
//    public function getPrice($n) {
//        $price = $this->price_list[1];
//        $list = $this->price_list;
//        ksort($list);
//        foreach ($list as $k => $v) {
//            if ($n < $k) {
//                break;
//            } else {
//                $price = $v;
//            }
//        }
//        return $price;
//    }
//
//    /**
//     * @param $n
//     * @return array
//     * @throws CostException
//     */
//    public function getCost($n): float {
//        return $this->computeCost($n)['cost'];
//    }
//
//    /**
//     * @param $service
//     * @param $n
//     * @throws CostException
//     */
//    public function computeCost($n): array {
//        if ($n < $this->min || $n > $this->max) {
//            throw CostException::create([
//                'text' => "n must be in [$this->min, $this->max], but was $n"
//            ]);
//        }
//
//        $price = $this->getPrice($n);
//        if (!$price) {
//            throw CostException::create(['text' => "bad price: $price, n: $n"]);
//        }
//
//        $cost = $price * $n;
//        $full_cost = $this->getPrice(1) * $n;
//        $economy = $full_cost - $cost;
//        $economy_percent = $economy * 100.0 / $full_cost;
//        return [
//            'service_id' => $this->id,
//            'service_type' => $this->type,
//            'n' => $n,
//            '_full_cost' => $full_cost,
//            'cost' => round($cost, 2, PHP_ROUND_HALF_DOWN),
//            'economy' => round($economy, 2, PHP_ROUND_HALF_DOWN),
//            '_stairway_percent' => round($economy_percent, 2, PHP_ROUND_HALF_DOWN),
//        ];
//    }
//
//    public function computeCostPremium($n, $user): array {
//        $costData = $this->computeCost($n);
//
//        $premiumStatus = $user->getPremiumStatus();
//        $premiumDiscount = $premiumStatus->discount[$this->group];
//
//        if ($premiumDiscount > $costData['_stairway_percent']) {
//            $costData['cost'] = $costData['_full_cost'] * $premiumDiscount / 100.0;
//            $costData['economy'] = $costData['_full_cost'] - $costData['cost'];
//            $costData['_premium'] = $premiumStatus->name;
//            $costData['_premium_percent'] = $premiumDiscount;
//        }
//        return $costData;
//    }
//}
