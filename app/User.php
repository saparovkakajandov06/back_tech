<?php

namespace App;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Role\UserRole;
use App\Services\Money\Services\TransactionsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Пользователь системы
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    const notFound = 'Пользователь не найден.';

    public const LANG_RU = 'ru';
    public const LANG_EN = 'en';
    public const LANG_ES = 'es';
    public const LANG_PT = 'pt';
    public const LANG_TR = 'tr';
    public const LANG_DE = 'de';
    public const LANG_IT = 'it';
    public const LANG_UK = 'uk';

    const LANG = [
        self::LANG_RU,
        self::LANG_EN,
        self::LANG_ES,
        self::LANG_PT,
        self::LANG_TR,
        self::LANG_DE,
        self::LANG_IT,
        self::LANG_UK
    ];

    protected $guarded = [
//        'api_token',
    ];

    protected $hidden = [
        'api_token',
        'token_updated_at',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'token_updated_at' => 'datetime',
        'roles' => 'array',
        'params' => 'array',
    ];

    // ------- relationships -----------

    public function payments()
    {
        return $this->hasMany('\App\Payment');
    }

    public function articles()
    {
        return $this->hasMany('\App\Article')->orderBy('created_at', 'DESC');
    }

    public function compositeOrders()
    {
        return $this->hasMany(CompositeOrder::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function refs()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function premiumStatus()
    {
        return $this->belongsTo(PremiumStatus::class);
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction')->orderBy('created_at');
    }

    public function cashBackHistory()
    {
        return $this->hasMany(Cashback::class)->orderBy('created_at');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function chunks()
    {
        return $this->belongsToMany(Chunk::class, 'actions');
    }

    public static function getFreeToken()
    {
    start:
        $token = Str::random(60);
        if (User::where('api_token', $token)->exists()) {
            Log::info("Duplicate token $token");
            goto start;
        }
        return $token;
    }

    // ------- tokens -----------
    public function updateToken(): string
    {
        if (
            empty($this->token_updated_at) ||
            empty($this->api_token) ||
            Carbon::now()->diffInDays($this->token_updated_at) > 90
        ) {
            $this->update([
                'api_token' => User::getFreeToken(),
                'token_updated_at' => Carbon::now(),
            ]);
            $this->refresh();
        }

        return $this->api_token;
    }

    public function setPassword($password)
    {
        $this->update([
            'password'         => bcrypt($password),
            'api_token'        => User::getFreeToken(),
            'token_updated_at' => Carbon::now(),
        ]);
        $this->refresh();
    }

    public function deleteToken()
    {
        $this->api_token = null;
        $this->save();
    }

    // ----------- roles ----------------

    public function addRole(string $role): User
    {
        $roles = $this->getRolesFlat();
        $roles[] = $role;

        $roles = array_unique($roles);
        $this->setRolesFlat($roles);

        return $this;
    }

    public function setRolesFlat(array $roles): User
    {
        $this->setAttribute('roles', $roles);
        return $this;
    }

    public function hasRole(string $checkedRole): bool
    {
        //return in_array($role, $this->getRolesFlat());
        $rolesFlat = $this->getRolesFlat();
        foreach ($rolesFlat as $userRole) {
            $rolesNested = UserRole::getRolesNested($userRole);
            if (in_array($checkedRole, $rolesNested)) {
                return true;
            }
        }
        return false;
    }

    public function getRolesFlat(): array
    {
        $roles = $this->getAttribute('roles');
        return $roles ?? [];
    }

// ------------------------------------------
    public static function findOrCreate(
        int    $passwordLength=8,
        string $referralCode=null,
        string $token=null,
        string $language='ru',
        string $currency=Transaction::CUR_RUB,
    ): User {
        if ($token and $user = User::where('api_token', $token)->first()) {
            return $user;
        }
        $r = Str::random($passwordLength);
        $user = User::create([
            'api_token'        => User::getFreeToken(),
            'cur'              => $currency,
            'email'            => $r.'@smm.example.com',
            'lang'             => $language,
            'name'             => 'user_'.$r,
            'password'         => bcrypt($r),
            'roles'            => [ UserRole::ROLE_AUTO ],
            'token_updated_at' => Carbon::now(),
        ]);
        $user->searchForParent($referralCode);
        $user->setBasicPremiumStatus();
        return $user;
    }

    public function canSendMail(): bool
    {
        return $this->hasEmail() and !$this->isAutoUser();
    }

    public function isAutoUser(): bool
    {
        if(!$this->hasEmail()){
            return false;
        }

        $email = $this->email;

        return Str::contains($email, '@smm.example.com');

    }

    public function hasEmail(): bool
    {
        return $this->email !== null;
    }

    // ------------ wallet --------------

//    public function getBalanceAttribute($cur): float
//    {
//        return resolve(MoneyService::class)->getUserBalance($this->id, $cur);
//    }

//    private function getSum($type)
//    {
//        $amount = $this->transactions()
//            ->where('type', $type)
//            ->get()
//            ->sum('amount');
//
//        return $amount;
//    }

//    public function getPaymentsSum($cur)
//    {
//        return resolve(MoneyService::class)->getPaymentsSum($this->id, $cur);
//    }

    // ---------------- transactions -------------------
    public function giveMoney(float $amount, string $cur): self
    {
        resolve(TransactionsService::class)->create($this, Transaction::INFLOW_CREATE, $amount, $cur, "Money created");

        return $this;
    }

    public function getBalance(): string
    {
        $m = resolve(TransactionsService::class);
        $rub = $m->sum($this, Transaction::CUR_RUB);
        $usd = $m->sum($this, Transaction::CUR_USD);

        return "$rub RUB $usd USD";
    }

    public function save(array $options = array())
    {
        // реферальный код
        if(empty($this->ref_code)) {
        start:
            $ref_code = Str::random(8);
            if (User::where('ref_code', $ref_code)->exists()) {
                Log::info("Duplicate refcode $ref_code");
                goto start;
            }
            $this->ref_code = $ref_code;
        }

        return parent::save($options);
    }

    public function setBasicPremiumStatus(): User
    {
        $this->update([
            'premium_status_id' => PremiumStatus::where('name', $this->parent_id ? 'LEVEL_2' : 'LEVEL_1')
                ->where('cur', $this->cur)
                ->firstOrFail()
                ->id,
        ]);

        return $this;
    }

    public function searchForParent($refCode=null): User
    {
        if (! empty($refCode) && $parent = User::where('ref_code', $refCode)->first()) {
            $this->update([
                'parent_id' => $parent->id,
            ]);
        }
        return $this;
    }

    public function hasParam($param): bool
    {
        return collect($this->params)->contains($param);
    }

    public function addParam($param): array
    {
        if (! $this->hasParam($param)) {
            $this->update([
                'params' => $this->params ?
                    [ ...$this->params, $param ] :
                    [ $param ]
            ]);
        }

        return $this->params;
    }

    public function removeParam($param): array
    {
        $this->update([
            'params' => collect($this->params)
                ->filter(fn($par) => $par !== $param)
                ->all()
        ]);

        return $this->params;
    }

    public function scopeWithParam($query, $param)
    {
        return $query->where('params', '?', $param);
    }
}
