<?php

namespace App\Domain\Traits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

trait TransformTiktokLink
{
    public function baseCurl($url): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.62 Version/11.01');

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function transformLoginToLink($url): string
    {
        $link = null;

        if(strripos($url, 'tiktok.com') === false){
            $link = 'https://www.tiktok.com/@'.$url.'/';
        }else{
            $link = $url;
        }

        return $link;
    }
    /*
     * Получаем карту embed tiktok (только для видео Тикток)
     */

    public function transformLongLink($url): object
    {
        $out = $this->baseCurl('https://www.tiktok.com/oembed?url='.$url.'');

        return json_decode($out);
    }

    /*
     *Получаем логин из ссылки на видео
     */
    public function getEmbet($url): object
    {
        if(stripos($url, '@')){
            $longLink = $url;
        }else{
            $longLink = $this->transformBaseLink($url);
        }


        return $this->transformLongLink($longLink);
    }

    /*
     *Получаем логин из ссылки на видео
     */
    public function getLoginFromLinkVideo($obj): string
    {
        return $this->transformEmbedToLogin($obj);
    }

    /*
     *Получаем id видео из ссылки на видео
     */
    public function getIdFromLinkVideo($obj): string
    {
        return $this->transformVideoUrl($obj);
    }

    /*
     * Преобразуем короткую ссылк в длинную (только для получения инфы по аккаунту)
     */

    public function transformBaseLink($url): string
    {
        $result = $this->baseCurl($url);

        $re = '/ href="([\s\S]+?)"/m';

        preg_match_all($re, $result, $matches, PREG_SET_ORDER, 0);

        return $this->getValue($matches);
    }

    /*
     * Преобразуем короткую ссылк в длинную (только для получения инфы видео)
     */

    public function transformBaseLinkVideo($url): string
    {
        $result = $this->baseCurl($url);

        return $result;
    }

    /*
     * Получаем страницу для парсинга по login id
     */

    public function scrapLogin($uid): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://m.tiktok.com/h5/share/usr/'.$uid.'.html');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.62 Version/11.01');


        $result = curl_exec($ch);
        $result = serialize($result);

        curl_close($ch);

        if(strripos($result, '@') === false){
            $re = '/"uniqueId":"([\s\S]+?)",/m'; //"nickName":"rumyantcevm","covers"
        }else{
            $re = '/@([\s\S]+?)\?/m'; //"nickName":"rumyantcevm","covers" @sh_botashev?
        }

        preg_match_all($re, $result, $matches, PREG_SET_ORDER, 0);

        return $this->getValue($matches);

    }

    /*
     *  Получаем id видео
     *
     */

    public function transformVideoUrl($data): string
    {
        //$data = $this->transformLongLink($url);
        try {
            $body = $data->html;
        }catch (\Exception $e){
            $body = "";
        }

        $regExpVideo = '/video\/([\s\S]+?)"/m';
        preg_match_all($regExpVideo, $body, $matches, PREG_SET_ORDER, 0);

        return $this->getValue($matches);
    }

    /*
     *  Получаем login из embed карты
     * */

    public function transformEmbedToLogin($data): string
    {
        try {
            $body = $data->author_url;
        }catch (\Exception $e){
            $body = "";
        }

        $regExpVideo = '/com\/@([\s\S]+?)$/m'; //com/@([\s\S]+?)/video
        preg_match_all($regExpVideo, $body, $matches, PREG_SET_ORDER, 0);

        return $this->getValue($matches);
    }

    /*
     *  Получаем фото поста (только для видео)
     * */

    public function getPhotoPost($data): string
    {
        //$data = $this->transformLongLink($url);



        try {
            $img = $data->thumbnail_url;
        }catch (\Exception $e){
            $img = "";
        }

        return $img;
    }


    /*
     * Вытаскивает из ссылки логин аккаунта в тикток
     */

    public function transformProfileUrl($url): string
    {

        if(stripos($url, 'tiktok') !== false && stripos($url, '?') !== false){
            $regExpLogin = '/@([\s\S]+?)\?/m';
        }else{
            $regExpLogin = '/@([\s\S]+?)$/m';
        }

        preg_match_all($regExpLogin, $url, $matches, PREG_SET_ORDER, 0);

        return $this->getValue($matches);
    }

    /*
     * Трансформирует url в id юзера, а id юзера в login
     */
    public function transfromLinkToLogin($url): string
    {
        $data = $this->transformBaseLink($url);
        Log::info('Base link');
        Log::info($data);
        Log::info('Base link');
        $regExpLogin = '/usr\/([\s\S]+?).html/m';
        preg_match_all($regExpLogin, $data, $matches, PREG_SET_ORDER, 0);

        Log::info('$matches');
        Log::info($matches[0][1]);
        Log::info('$matches');

        if($this->getValue($matches) !== false){
            $res = $this->scrapLogin($matches[0][1]);
        }else{
            $res = false;
        }

        return $res;
    }

    /*
     * Получаем id видео из длинной ссылки
     * */
    public function transfromLinkToIdVideo($url): string
    {
        if(stripos($url, '?') === false){
            $regExpVideo = '/video\/([\s\S]+?)$/m';
        }else{
            $regExpVideo = '/video\/([\s\S]+?)\?/m';
        }

        preg_match_all($regExpVideo, $url, $data, PREG_SET_ORDER, 0);

        return $this->getValue($data);
    }

    /*
     * Получаем логин из ссылки(Только для ссылок формата https://www.tiktok.com/@artur_dvl/video/6852703879889554693?_d=...)
     */
    public function transformLinkToLoginVideo($url): string
    {
        $regExpVideo = '/@([\s\S]+?)\//m';
        preg_match_all($regExpVideo, $url, $data, PREG_SET_ORDER, 0);

        return $this->getValue($data);
    }

    public function getValue($data){
        try {
            return $data[0][1];
        }catch (\Exception $e){
            return false;
        }
    }
}
