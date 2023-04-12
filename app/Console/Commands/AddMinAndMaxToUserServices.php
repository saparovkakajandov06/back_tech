<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use US;

class AddMinAndMaxToUserServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:add_min_and_max_to_user_sevices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add min and max to all user services';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data = [
            //     name: "Репосты",
            US::TIKTOK_REPOSTS_MAIN  => [
                'min_order' => 10, 
                'max_order' => 1000
            ],
            //     name: "Лайки",
            US::VK_LIKES_MAIN  => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Автолайки",
            US::TIKTOK_AUTO_LIKES_LK  => [
                'min_order' => 100, 
                'max_order' => 40000, 
                'order_speed' => '120-200', 
                'order_frequency' => 'hour'
            ],
           
            //     name: "Лайки",
            US::TIKTOK_LIKES_LK  => [
                'min_order' => 100, 
                'max_order' => 40000, 
                'order_speed' => '120-200', 
                'order_frequency' => 'hour'
            ],
          
            //     name: "Автопросмотры",
            US::TIKTOK_AUTO_VIEWS_LK => [
                'min_order' => 100, 
                'max_order' => 200000, 
                'order_speed' => '500', 
                'order_frequency' => 'hour'
            ],
           
            //     name: "Друзья",
            US::VK_FRIENDS_LK => [
                'min_order' => 100, 
                'max_order' => 100000, 
                'order_speed' => '500', 
                'order_frequency' => 'day'
            ],
            
            //     name: "Лайки",
            US::TIKTOK_LIKES_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
           
            //     name: "Позитивные комментарии",
            US::TIKTOK_COMMENTS_POSITIVE_LK => [
                'min_order' => 6, 
                'max_order' => 10000, 
                'order_speed' => '50', 
                'order_frequency' => 'hour'
            ],
            
            //     name: "Лайки на последние посты",
            US::INSTAGRAM_MULTI_LIKES_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
           
            //     name: "Подписчики",
            US::TIKTOK_SUBS_LK => [
                'min_order' => 100, 
                'max_order' => 200000, 
                'order_speed' => '2000', 
                'order_frequency' => 'day'
            ],
         
            //     name: "Подписчики",
            US::INSTAGRAM_SUBS_LK => [
                'min_order' => 100, 
                'max_order' => 200000, 
                'order_speed' => '2000', 
                'order_frequency' => 'day'
            ],
                    
            //     name: "Подписчики",
            US::INSTAGRAM_SUBS_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Зрители в прямой эфир",
            US::INSTAGRAM_LIVE_VIEWERS_LK => [
                'min_order' => 100, 
                'max_order' => 1000000
            ],
           
            //     name: "Лайки",
            US::VK_LIKES_LK  => [
                'min_order' => 100, 
                'max_order' => 100000, 
                'order_speed' => '10-50', 
                'order_frequency' => 'hour'
            ],
            
            //     name: "Комментарии",
            US::VK_COMMENTS_MAIN => [
                'min_order' => 6, 
                'max_order' => 1000
            ],
            
            //     name: "Подписчики",
            US::VK_SUBS_LK  => [
                'min_order' => 100, 
                'max_order' => 100000, 
                'order_speed' => '500', 
                'order_frequency' => 'day'
            ],
          
            //     name: "Комментарии",
            US::VK_COMMENTS_LK => [
                'min_order' => 6, 
                'max_order' => 1000, 
                'order_speed' => '10', 
                'order_frequency' => 'hour'
            ],
            
            //     name: "Подписчики",
            US::TIKTOK_SUBS_MAIN  => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Просмотры видео",
            US::TIKTOK_VIEWS_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Репосты",
            US::VK_REPOSTS_MAIN => [
                'min_order' => 10, 
                'max_order' => 1000
            ],
            
            //     name: "Лайки",
            US::INSTAGRAM_LIKES_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
           
            //     name: "Автолайки",
            US::VK_AUTO_LIKES_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Лайки",
            US::YOUTUBE_LIKES_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Автолайки",
            US::VK_AUTO_LIKES_LK => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Просмотры",
            US::YOUTUBE_VIEWS_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
           
            //     name: "Подписчики",
            US::YOUTUBE_SUBS_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Автолайки",
            US::INSTAGRAM_AUTO_LIKES_LK => [
                'min_order' => 100, 
                'max_order' => 50000, 
                'order_speed' => '120-200', 
                'order_frequency' => 'hour'
            ],
            
            //     name: "Подписчики",
            US::VK_SUBS_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
            
            //     name: "Позитивные комментарии",
            US::INSTAGRAM_COMMENTS_POSITIVE_LK => [
                'min_order' => 6, 
                'max_order' => 100000, 
                'order_speed' => '50', 
                'order_frequency' => 'hour'
            ],
           
            //     name: "Лайки",
            US::INSTAGRAM_LIKES_LK => [
                'min_order' => 100, 
                'max_order' => 200000, 
                'order_speed' => '120-200', 
                'order_frequency' => 'hour'
            ],
            
            //     name: "Автопросмотры",
            US::INSTAGRAM_AUTO_VIEWS_LK => [
                'min_order' => 100, 
                'max_order' => 50000, 
                'order_speed' => '200000', 
                'order_frequency' => 'day'
            ],
            
            //     name: "Просмотры IGTV",
            US::INSTAGRAM_VIEWS_IGTV_LK => [
                'min_order' => 100, 
                'max_order' => 1000000, 
                'order_speed' => '20000', 
                'order_frequency' => 'day'
            ],
           
            //     name: "Свои комментарии на последние посты",
            US::INSTAGRAM_MULTI_COMMENTS_CUSTOM_LK => [
                'min_order' => 6, 
                'max_order' => 10000, 
                'order_speed' => '50', 
                'order_frequency' => 'hour'
            ],
           
            //     name: "Просмотры историй",
            US::INSTAGRAM_VIEWS_STORY_LK => [
                'min_order' => 100, 
                'max_order' => 1000000, 
                'order_speed' => '15000', 
                'order_frequency' => 'day'
            ],
    
            //      name: "Просмотры видео",
            US::INSTAGRAM_VIEWS_VIDEO_LK => [
                'min_order' => 100, 
                'max_order' => 1000000, 
                'order_speed' => '20000', 
                'order_frequency' => 'day'
            ],
  
            //      name: "Просмотры",
            US::YOUTUBE_VIEWS_LK => [
                'min_order' => 300, 
                'max_order' => 1000000, 
                'order_speed' => '10000', 
                'order_frequency' => 'day'
            ],
  
            //      name: "Позитивные комментарии на последние посты",
            US::INSTAGRAM_MULTI_COMMENTS_POSITIVE_LK => [
                'min_order' => 6, 
                'max_order' => 100000, 
                'order_speed' => '50', 
                'order_frequency' => 'hour'
            ],
    
            //      name: "Автопросмотры",
            US::INSTAGRAM_AUTO_VIEWS_MAIN => [
                'min_order' => 100, 
                'max_order' => 1000000
            ],
    
            //      name: "Просмотры видео",
            US::INSTAGRAM_VIEWS_VIDEO_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
   
            //      name: "Просмотры историй",
            US::INSTAGRAM_VIEWS_STORY_MAIN  => [
                'min_order' => 100, 
                'max_order' => 15000
            ],
    
            //      name: "Репосты",
            US::TIKTOK_REPOSTS_LK  => [
                'min_order' => 100, 
                'max_order' => 40000
            ],
    
            //      name: "Показы + Охват",
            US::INSTAGRAM_VIEWS_SHOWS_IMPRESSIONS_LK => [
                'min_order' => 100, 
                'max_order' => 1000000, 
                'order_speed' => '20000', 
                'order_frequency' => 'day'
            ],
   
            //      name: "Автопросмотры",
            US::TIKTOK_AUTO_VIEWS_MAIN  => [
                'min_order' => 100, 
                'max_order' => 1000000
            ],
    
            //      name: "Лайки в прямой эфир",
            US::INSTAGRAM_LIVE_LIKES_LK => [
                'min_order' => 100, 
                'max_order' => 10000, 
                'order_speed' => '2000-3000', 
                'order_frequency' => 'hour'
            ],
   
            //      name: "Свои комментарии",
            US::TIKTOK_COMMENTS_CUSTOM_LK => [
                'min_order' => 6, 
                'max_order' => 10000, 
                'order_speed' => '50', 
                'order_frequency' => 'hour'
            ],
    
            //      name: "Репосты",
            US::VK_REPOSTS_LK => [
                'min_order' => 100, 
                'max_order' => 100000, 
                'order_speed' => '10-20', 
                'order_frequency' => 'hour'
            ],
    
            //      name: "Лайки на последние посты",
            US::INSTAGRAM_MULTI_LIKES_LK => [
                'min_order' => 100, 
                'max_order' => 200000, 
                'order_speed' => '120-200', 
                'order_frequency' => 'hour'
            ],
    
            //      name: "Автолайки",
            US::TIKTOK_AUTO_LIKES_MAIN  => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
    
            //      name: "Друзья",
            US::VK_FRIENDS_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
    
            //      name: "Лайки",
            US::YOUTUBE_LIKES_LK => [
                'min_order' => 100, 
                'max_order' => 100000, 
                'order_speed' => '10000', 
                'order_frequency' => 'day'
            ],
    
            //      name: "Подписчики",
            US::YOUTUBE_SUBS_LK => [
                'min_order' => 100, 
                'max_order' => 100000, 
                'order_speed' => '10000', 
                'order_frequency' => 'day'
            ],

            //      name: "Дизлайки",
            US::YOUTUBE_DISLIKES_LK => [
                'min_order' => 100, 
                'max_order' => 1000000, 
                'order_speed' => '10000', 
                'order_frequency' => 'day'
            ],
   
            //      name: "Просмотры видео",
            US::TIKTOK_VIEWS_LK => [
                'min_order' => 100, 
                'max_order' => 1000000, 
                'order_speed' => '500000', 
                'order_frequency' => 'day'
            ],
    
            //      name: "Автолайки",
            US::INSTAGRAM_AUTO_LIKES_MAIN => [
                'min_order' => 100, 
                'max_order' => 100000
            ],
    
            //      name: "Свои комментарии",
            US::INSTAGRAM_COMMENTS_CUSTOM_LK => [
                'min_order' => 6, 
                'max_order' => 10000, 
                'order_speed' => '50', 
                'order_frequency' => 'hour'
            ]
        ];
        
        echo "Make min and max in US's great again!";

        foreach($data as $tag => $columns){
            $s = US::where('tag', $tag)->firstOrFail();
            foreach($columns as $column => $value){
                $s->update([
                    $column => $value,
                ]);
            }
            echo "$tag\n";
        }
        echo "\ndone\n";
    }
}
