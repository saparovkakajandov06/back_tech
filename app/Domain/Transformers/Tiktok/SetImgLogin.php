<?php
namespace App\Domain\Transformers\Tiktok;

use App\Domain\Traits;
use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\TTUser;
use App\UserService;
use Exception;

class SetImgLogin implements ITransformer
{
    use Traits\TransformTiktokLink;

    // buzova86
    // @buzova86
    // https://tiktok.com/@buzova86
    // https://www.tiktok.com/@buzova86

    // -> buzova86

    public function transform(array $params, UserService $us): array
    {
        $url = $params[0]['link'] ?? $params[0]['login'];

        if (stripos($url, '@') === false) {
            if (stripos($url, 'tiktok') === false) {
                $userId = $url;
            } else {
                $userId = $this->transfromLinkToLogin($url);
            }
        } else {
                $userId = $this->transformProfileUrl($url);
        }

       try{
//           $img = $this->service->getAvatar($userId);
           $img = TTUser::fromLogin($userId)->avatarMedium;

       }catch (Exception $e){
           $img = null;
        }

        $params[0]['img'] = $img;

        //вынести в отдельный модуль https://www.tiktok.com/@

        if($img !== null){
            $params[0]['login'] = 'https://www.tiktok.com/@'.$userId.'';
        }

        $params[0]['alias'] = $userId;
        $params[0]['title'] = $userId;
        $params[0]['link'] = $this->transformLoginToLink($userId);

        return $params;
    }
}
