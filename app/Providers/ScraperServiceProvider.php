<?php

namespace App\Providers;

use App;
use App\Scraper\DYoutubeScraper;
use App\Scraper\FakeInstagramScraper;
use App\Scraper\FakeTiktokScraper;
use App\Scraper\FakeYoutubeScraper;
use App\Scraper\InstagramScraper;
use App\Scraper\PInstagramScraper;
use App\Scraper\Simple\BestExperienceTiktokScraper;
use App\Scraper\Simple\Instagram28Scraper;
use App\Scraper\Simple\JoTiktokScraper;
use App\Scraper\Simple\RestylerIgScraper;
use App\Scraper\Simple\SimpleTiktokScraperFake;
use App\Scraper\Simple\KirtanTiktokScraper;
use App\Scraper\TiktokScraper;
use App\Scraper\TTScraper;
use App\Scraper\YoutubeScraper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class ScraperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PInstagramScraper::class, function() {
            return new PInstagramScraper();
        });

        $this->app->bind(InstagramScraper::class, function () {
            return new FakeInstagramScraper();

            if (App::environment('testing')) {
                return new FakeInstagramScraper();
            }

            $scraper = new PInstagramScraper();

            $key = __CLASS__ . __FUNCTION__;
            $checkResult = Cache::remember(
                $key,
                600,
                fn() => $scraper->healthCheck()
            );

            if ($checkResult) {
                return $scraper;
            } else {
                return new FakeInstagramScraper();
            }
        });


        $this->app->bind(TiktokScraper::class, function () {

            $scraper = new TTScraper();

            $key = __CLASS__ . __FUNCTION__;
            $checkResult = Cache::remember(
                $key,
                600,
                fn() => $scraper->healthCheck()
            );

            if ($checkResult) {
                return $scraper;
            } else {
                return new FakeTiktokScraper();
            }
        });

        $this->app->bind(YoutubeScraper::class, function () {

            $scraper = new DYoutubeScraper();

            $key = __CLASS__ . __FUNCTION__;
            $checkResult = Cache::remember(
                $key,
                600,
                fn() => $scraper->healthCheck()
            );

            if ($checkResult) {
                return $scraper;
            } else {
                return new FakeYoutubeScraper();
            }
        });

        // ---------------------
        // resolve concrete scrapers because we set them in pipeline
        $this->app->bind(KirtanTiktokScraper::class, function () {
            return App::environment('testing') ?
                new SimpleTiktokScraperFake() :
                new KirtanTiktokScraper();
        });

        $this->app->bind(RestylerIgScraper::class, function () {
            return App::environment('testing') ?
                null :
                new RestylerIgScraper();
        });

        $this->app->bind(BestExperienceTiktokScraper::class, fn() => new BestExperienceTiktokScraper(config('scrapers.tiktok.bestexperience')));
        $this->app->bind(JoTiktokScraper::class, fn() => new JoTiktokScraper(config('scrapers.tiktok.jo')));

        $this->app->bind(Instagram28Scraper::class, fn() => new Instagram28Scraper(config('scrapers.instagram.28')));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
