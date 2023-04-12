<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Action
 *
 * @property int $id
 * @property int $user_id
 * @property int $chunk_id
 * @property bool $completed
 * @property bool $paid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Models\Chunk $chunk
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Action newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Action newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Action query()
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereChunkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUserId($value)
 */
	class Action extends \Eloquent {}
}

namespace App{
/**
 * App\Article
 *
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string $cover
 * @property string $heading
 * @property string $description
 * @property string $tags
 * @property string $headtitle
 * @property string $headdescription
 * @property string $article
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ArticleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereHeaddescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereHeadtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereViews($value)
 */
	class Article extends \Eloquent {}
}

namespace App{
/**
 * App\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BaseModel query()
 * @mixin \Eloquent
 */
	class BaseModel extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * Часть заказа
 *
 * @property int $id
 * @property int|null $composite_order_id
 * @property string $service_class
 * @property int $completed
 * @property string|null $extern_id
 * @property string $status
 * @property array|null $add_request
 * @property array|null $remote_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $details
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Action[] $actions
 * @property-read int|null $actions_count
 * @property-read \App\Domain\Models\CompositeOrder|null $compositeOrder
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ChunkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereAddRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereCompositeOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereExternId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereRemoteResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereServiceClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chunk whereUpdatedAt($value)
 */
	class Chunk extends \Eloquent {}
}

namespace App\Domain\Models{
/**
 * App\Domain\Models\CompositeOrder
 *
 * @property int $id
 * @property int $user_id
 * @property int $user_service_id
 * @property bool $paid
 * @property string $status
 * @property bool $done
 * @property string|null $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $uuid
 * @property array|null $params
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Models\Chunk[] $chunks
 * @property-read int|null $chunks_count
 * @property-read \App\Domain\Models\Chunk|null $chunksCompletedSum
 * @property-read mixed $chunks_completed_sum
 * @property-read mixed $completed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OLog[] $ologs
 * @property-read int|null $ologs_count
 * @property-read \App\User $user
 * @property-read \App\UserService $userService
 * @method static \Database\Factories\CompositeOrderFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder notPaid()
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder paid()
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder shouldBeUpdated()
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereUserServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompositeOrder whereUuid($value)
 */
	class CompositeOrder extends \Eloquent implements \App\Domain\OrderSM\IOrderState {}
}

namespace App\Notification{
/**
 * App\Notification\Notification
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $content
 * @method static \Illuminate\Database\Eloquent\Builder|Notification moreRecent(\App\Notification\Notification $notification)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Notification{
/**
 * Class Read
 *
 * @package App\Notification
 * @property Notification $notification
 * @property User $user
 * @property boolean $is_read
 * @property integer $user_id
 * @property integer $notification_id
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status readByUser(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status whereNotificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification\Status whereUserId($value)
 * @mixin \Eloquent
 */
	class Status extends \Eloquent {}
}

namespace App{
/**
 * App\OLog
 *
 * @property int $id
 * @property int $composite_order_id
 * @property string|null $event
 * @property string|null $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|OLog whereCompositeOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OLog whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OLog whereUpdatedAt($value)
 */
	class OLog extends \Eloquent {}
}

namespace App{
/**
 * Order statuses
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $service_id
 * @property string|null $type
 * @property string $cost
 * @property string $price
 * @property string $status
 * @property string|null $details
 * @property bool $paid
 * @property string|null $img
 * @property string|null $instagram_login
 * @property int|null $foreign_id
 * @property string|null $foreign_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereForeignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereForeignStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereInstagramLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUuid($value)
 */
	class Order extends \Eloquent {}
}

namespace App{
/**
 * Платежи через кассы
 * Только для пополнений, не для оплаты с главной
 *
 * @property int $id
 * @property string $foreign_id
 * @property string $type
 * @property string $status
 * @property string $amount
 * @property string $currency
 * @property int $user_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Database\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereForeignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 */
	class Payment extends \Eloquent {}
}

namespace App{
/**
 * App\Post
 *
 * @property int $id
 * @property string $heading
 * @property string|null $description
 * @property int $views
 * @property string|null $slug
 * @property string|null $cover
 * @property string|null $date
 * @property string|null $image
 * @property string|null $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereViews($value)
 */
	class Post extends \Eloquent {}
}

namespace App{
/**
 * Class PremiumStatus
 *
 * @package App
 * 
 * Статус пользователя для программы лояльности
 * @property int $id
 * @property string $name
 * @property bool $online_support
 * @property bool $personal_manager
 * @property array $discount
 * @property int $cash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $cur
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereCash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereCur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereOnlineSupport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus wherePersonalManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumStatus whereUpdatedAt($value)
 */
	class PremiumStatus extends \Eloquent {}
}

namespace App\Price{
/**
 * Class PriceCategory
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Category extends \Eloquent {}
}

namespace App\Price{
/**
 * Class PriceFeature
 *
 * @package App
 * @property string $name
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price\Feature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Feature extends \Eloquent {}
}

namespace App\Price{
/**
 * App\Price\Price
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $category_id
 * @property int $cost
 * @property int $count
 * @property int $economy
 * @property bool $is_featured
 * @property-read \App\Price\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price\Feature[] $features
 * @property-read int|null $features_count
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereEconomy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUpdatedAt($value)
 */
	class Price extends \Eloquent {}
}

namespace App{
/**
 * App\Proxy
 *
 * @property int $id
 * @property string|null $comment
 * @property string $url
 * @property string|null $instagram
 * @property string|null $cookie
 * @property string|null $user_agent
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $proxy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProxyRequest[] $requests
 * @property-read int|null $requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProxyResponse[] $responses
 * @property-read int|null $responses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereCookie($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proxy whereUserAgent($value)
 */
	class Proxy extends \Eloquent {}
}

namespace App{
/**
 * App\ProxyRequest
 *
 * @property int $id
 * @property int $proxy_id
 * @property array $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Proxy $proxy
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest whereProxyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyRequest whereUpdatedAt($value)
 */
	class ProxyRequest extends \Eloquent {}
}

namespace App{
/**
 * App\ProxyResponse
 *
 * @property int $id
 * @property int $proxy_id
 * @property array $params
 * @property bool $success
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Proxy $proxy
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse whereProxyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyResponse whereUpdatedAt($value)
 */
	class ProxyResponse extends \Eloquent {}
}

namespace App{
/**
 * App\Status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status query()
 * @mixin \Eloquent
 */
	class Status extends \Eloquent {}
}

namespace App{
/**
 * App\Transaction
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $event_id
 * @property string $type
 * @property string $amount
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $related_user_id
 * @property string $commission
 * @property string $cur
 * @property-read \App\User $user
 * @method static \Database\Factories\TransactionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereRelatedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App{
/**
 * App\TransactionGroups
 *
 * @property int $id
 * @property string $transaction_group
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups whereTransactionGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionGroups whereUpdatedAt($value)
 */
	class TransactionGroups extends \Eloquent {}
}

namespace App{
/**
 * App\TransactionTypes
 *
 * @property int $id
 * @property string $transaction_type
 * @property string $title
 * @property int $transaction_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes whereTransactionGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionTypes whereUpdatedAt($value)
 */
	class TransactionTypes extends \Eloquent {}
}

namespace App{
/**
 * App\USPrice
 *
 * @property int $id
 * @property string $tag
 * @property array|null $EUR
 * @property array|null $USD
 * @property array|null $RUB
 * @property array|null $TRY
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\USPriceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereEUR($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereRUB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereTRY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereUSD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|USPrice whereUpdatedAt($value)
 */
	class USPrice extends \Eloquent {}
}

namespace App{
/**
 * Пользователь системы
 *
 * @property int $id
 * @property string|null $api_token
 * @property \Illuminate\Support\Carbon|null $token_updated_at
 * @property string|null $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $confirmation_code
 * @property string|null $reset_code
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $roles
 * @property string|null $instagram_login
 * @property string|null $social_id
 * @property string|null $network
 * @property string|null $avatar
 * @property string|null $ref_code
 * @property int|null $parent_id
 * @property int $premium_status_id
 * @property string|null $telegram_id
 * @property string $lang
 * @property string $cur
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Action[] $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Article[] $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Models\Chunk[] $chunks
 * @property-read int|null $chunks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Models\CompositeOrder[] $compositeOrders
 * @property-read int|null $composite_orders_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \App\PremiumStatus $premiumStatus
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $refs
 * @property-read int|null $refs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInstagramLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePremiumStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereResetCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTokenUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App{
/**
 * App\UserService
 *
 * @property int $id
 * @property string $title
 * @property string $tag
 * @property string $splitter
 * @property array|null $config
 * @property string|null $img
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $description
 * @property array|null $card
 * @property array|null $local_validation
 * @property string|null $local_checker
 * @property string|null $tracker
 * @property string|null $platform
 * @property string|null $name
 * @property array|null $pipeline
 * @property array|null $labels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Models\CompositeOrder[] $compositeOrders
 * @property-read int|null $composite_orders_count
 * @property-read \App\USPrice|null $price
 * @method static \Database\Factories\UserServiceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserService query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereLocalChecker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereLocalValidation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService wherePipeline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereSplitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereTracker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserService withLabel($label)
 */
	class UserService extends \Eloquent {}
}

namespace App{
/**
 * App\Withdraw
 *
 * @property int $id
 * @property int $transaction_id
 * @property string $event_id
 * @property string $type
 * @property string $wallet_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereWalletNumber($value)
 */
	class Withdraw extends \Eloquent {}
}

