<?php
namespace App\Domain\Transformers\Tiktok;

use App\Domain\Traits;
use App\Domain\Transformers\ITransformer;
use App\UserService;

class SetImgVideo implements ITransformer
{
    use Traits\TransformTiktokLink;

    public function transform(array $params, UserService $us): array
    {
        $url = $params[0]['link'] ?? $params[0]['login'];

        $embed = $this->getEmbet($url);


        if (stripos($url, '@') === false) {
            $login = $this->getLoginFromLinkVideo($embed);
        } else {
            $login = $this->transformLinkToLoginVideo($url);
        }

        /*
         * Получаем id video
         */
        if (stripos($url, '@') === false) {
            $videoId = $this->getIdFromLinkVideo($embed);
        } else {
            $videoId = $this->transfromLinkToIdVideo($url);
        }

        try {
            $img = $this->getPhotoPost($embed);
        }catch (\Exception $e){
            $img = null;
        }

        if($img !== null || $img !== ""){
            $params[0]['login'] = 'https://www.tiktok.com/@'.$login.'';
        }

        $params[0]['img'] = $img;
        //вынести в отдельный компонент
        $params[0]['alias'] = $login;
        $params[0]['id_video'] = $videoId;

        return collect($params)->all();
    }
}