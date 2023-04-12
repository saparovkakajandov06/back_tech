<?php
//
//namespace Tests\Feature;
//
//use App\Domain\Transformers\Scrapers\ScrapeVkMedia;
//use App\Domain\Transformers\Scrapers\ScrapeVkProfile;
//use App\Domain\Transformers\Scrapers\ScrapeVkUserProfile;
//use App\Parsers\VkLinkParser;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Tests\TestCase;
//
//
//class VkApiScrapeTest extends TestCase
//{
//    use DatabaseMigrations;
//
//    private VkLinkParser $lp;
//
//    public function setUp(): void
//    {
//        parent::setUp();
//
//        $this->lp = new VkLinkParser();
//    }
//
//    public function testProfileScrape()
//    {
//        $login = $this->lp->login("https://vk.com/id1");
//        $array = ['login' => $login];
//        $scraper = (new ScrapeVkProfile())->transform($array);
//        dump($scraper);
//
//    }
//    public function testProfileClubScrape()
//    {
//        $login = $this->lp->login("https://vk.com/rhymes");
//        $array = ['login' => $login];
//        $scraper = (new ScrapeVkProfile())->transform($array);
//        dump($scraper);
//
//    }
//    public function testUserProfileScrapeFalse()
//    {
//        $login = $this->lp->login("https://vk.com/rhymes");
//        $array = ['login' => $login];
//        $scraper = (new ScrapeVkUserProfile())->transform($array);
//        dump($scraper);
//
//    }
//    public function testUserProfileScrapeTrue()
//    {
//        $login = $this->lp->login("https://vk.com/id1");
//        $array = ['login' => $login];
//        $scraper = (new ScrapeVkUserProfile())->transform($array);
//        dump($scraper);
//
//    }
//
//    public function testMediaPhotoFromProfileScrape()
//    {
//        $link = $this->lp->link("https://vk.com/id1?z=photo1_456316241%2Fphotos1");
//        $array = ['link' => $link];
//        $scraper = (new ScrapeVkMedia())->transform($array);
//        dump($scraper);
//
//    }
//
//    public function testMediaPhotoFromWallScrape()
//    {
//        $link = $this->lp->link("https://vk.com/id1?z=photo1_456316241%2Falbum1_00%2Frev");
//        $array = ['link' => $link];
//        $scraper = (new ScrapeVkMedia())->transform($array);
//        dump($scraper);
//    }
//    public function testMediaStoryScrape()
//    {
//        $link = $this->lp->link("https://vk.com/feed?w=story569862312_456239018%2Ffeed");
//        $array = ['link' => $link];
//        $scraper = (new ScrapeVkMedia())->transform($array);
//        dump($scraper);
//    }
//
//    public function testMediaScrape()
//    {
//        $link = $this->lp->link("https://vk.com/rhymes?w=wall-28905875_25161484");
//        $array = ['link' => $link];
//        $scraper = (new ScrapeVkMedia())->transform($array);
//        dump($scraper);
//    }
//
//    public function testMediaVideoScrape()
//    {
//        $link = $this->lp->link("https://vk.com/video?z=video-47_456252863%2Fb65757c15d8118b14c%2Fpl_cat_featured");
//        $array = ['link' => $link];
//        $scraper = (new ScrapeVkMedia())->transform($array);
//        dump($scraper);
//    }
//    public function testPrivatePhotoScrape()
//    {
//        $link = $this->lp->link("https://vk.com/photo569862312_457239376?rev=1");
//        $array = ['link' => $link];
//        $scraper = (new ScrapeVkMedia())->transform($array);
//        dump($scraper);
//    }
//
//
//}
