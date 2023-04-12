<?php
////php artisan test --filter GoogleAnalyticsTest
//namespace Tests\Feature;
//
//use App\Services\GoogleAnalytics;
//
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Tests\TestCase;
//use Tests\TF\TFHelpers;
//
//class GoogleAnalyticsTest extends TestCase
//{
//    use DatabaseMigrations;
//
//    private GoogleAnalytics $gaService;
//
//    public function setUp(): void
//    {
//        parent::setUp();
//        TFHelpers::runTestSeeders();
//
//        $this->gaService = resolve(GoogleAnalytics::class);
//    }
//
//    public function testReturnUserService()
//    {
//        //this service id return INSTAGRAM
//        ////this service id return TIKTOK
//        dump($this->gaService->sendFromPayments('GA1.1.1556093647', 5, 'c_CloudPayments', 'de'));
//
//    }
//}
