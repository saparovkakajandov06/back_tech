<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Tests\Unit;

use App\Exceptions\Reportable\DistributorException;
use App\Services\Distributor;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class DistributorTest extends TestCase
{
    private function getService($name, $order, $min, $max)
    {
        $n = 0;
        return compact('name', 'order', 'min', 'max', 'n');
    }

    public function generator(): array
    {
        return [
            // ------------ case 1 -------------------
            [
                [
                    $this->getService('a', 1, 100, 200),
                    $this->getService('b', 2, 100, 200),
                    $this->getService('c', 3, 100, 200),
                ],
                300,
                [
                    'a' => 200,
                    'b' => 100,
                    'c' => 0,
                ]
            ],
            // ----------- case 2 ---------
            [
                [
                    $this->getService('a', 1, 100, 200),
                    $this->getService('b', 2, 100, 200),
                    $this->getService('c', 3, 100, 1000),
                ],
                800,
                [
                    'a' => 200,
                    'b' => 200,
                    'c' => 400,
                ],
            ],
            // ------------ case 3 ---------------------------
            [
                [
                    $this->getService('a', 1, 100, 200),
                    $this->getService('b', 2, 100, 200),
                ],
                201,
                [
                    'a' => 101,
                    'b' => 100,
                ]
            ],
            // ------------ case 4 ----------------
            [
                [
                    $this->getService('a', 1, 100, 200),
                    $this->getService('b', 2, 100, 200),
                    $this->getService('c', 3, 700, 1000),
                    $this->getService('d', 4, 100, 1000),
                ],
                950,
                [
                    'a' => 200,
                    'b' => 0,
                    'c' => 750,
                    'd' => 0,
                ]
            ],
            // --------------- case 5 - order --------
            [
                [
                    $this->getService('d', 4, 100, 1000),
                    $this->getService('c', 3, 700, 1000),
                    $this->getService('b', 2, 100, 200),
                    $this->getService('a', 1, 100, 200),
                ],
                950,
                [
                    'a' => 200,
                    'b' => 0,
                    'c' => 750,
                    'd' => 0,
                ]
            ],
            // ---- case 6 - 3 little likes -----
            [
                [
                    $this->getService('a', 1, 1, 1),
                    $this->getService('b', 2, 1, 1),
                    $this->getService('c', 3, 1, 1),
                ],
                3,
                [
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                ]
            ],
            // ------ case 7 - skip first service ------
            [
                [
                    $this->getService('a', 1, 100, 150),
                    $this->getService('b', 2, 100, 180),
                ],
                180,
                [
                    'a' => 0,
                    'b' => 180,
                ]
            ],
            // --------- case 8 ------------
            [
                [
                    $this->getService('a', 1, 5, 500),
                    $this->getService('b', 2, 50, 100_000),
                ],
                520,
                [
                    'a' => 470,
                    'b' => 50,
                ]
            ]
        ];
    }

    /** @dataProvider generator */
    public function testDistribution($config, $amount, $ref)
    {
        $d = new Distributor($config);
        $res = $d->getDistribution($amount);
        $this->assertEquals($res, $ref);
    }

    public function testExceptionTooSmall()
    {
        $this->expectException(DistributorException::class);
        $this->expectExceptionMessage("too small (99)");

        (new Distributor([
            $this->getService('a', 1, 100, 200),
            $this->getService('b', 2, 100, 200),
        ]))->getDistribution(99);
    }

    public function testExceptionTooBig()
    {
        $this->expectException(DistributorException::class);
        $this->expectExceptionMessage("too large (500)");

        (new Distributor([
            $this->getService('a', 1, 100, 200),
            $this->getService('b', 2, 100, 200),
        ]))->getDistribution(500);
    }
}
