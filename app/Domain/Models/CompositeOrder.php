<?php

namespace App\Domain\Models;

use App\Domain\Models\Money\MoneyBack;
use App\Domain\Models\Money\RefBonus;
use App\Domain\OrderSM\IOrderState;
use App\Domain\OrderSM\StateMethods;
use App\Domain\OrderSM\States\CanceledState;
use App\Domain\OrderSM\States\CompletedState;
use App\Domain\OrderSM\States\CreatedState;
use App\Domain\OrderSM\States\ErrorState;
use App\Domain\OrderSM\States\PaidState;
use App\Domain\OrderSM\States\PartialCompletedState;
use App\Domain\OrderSM\States\PausedState;
use App\Domain\OrderSM\States\RunningState;
use App\Domain\OrderSM\States\SplitState;
use App\Domain\OrderSM\States\UpdatingState;
use App\Exceptions\Reportable\ReportableException;
use App\OLog;
use App\Order;
use App\Services\CurrencyService;
use App\Services\GoogleAnalytics;
use App\Services\MetaPixel;
use App\User;
use App\UserService;
use Database\Factories\CompositeOrderFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CompositeOrder extends Model implements IOrderState
{
    use HasFactory;
    use StateMethods;

    protected $guarded = [];

    protected $casts = [
        'params' => 'array',
    ];

    protected $hidden = [];

//    protected $appends = ['completed'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return CompositeOrderFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userService()
    {
        return $this->belongsTo(UserService::class);
    }

    public function chunks()
    {
        return $this->hasMany(Chunk::class);
    }

    public function ologs()
    {
        return $this->hasMany(OLog::class)->orderBy('created_at');
    }

    public function state(): IOrderState
    {
        return match($this->status) {
            Order::STATUS_CREATED => new CreatedState($this),
            Order::STATUS_SPLIT => new SplitState($this),
            Order::STATUS_PAID => new PaidState($this),
            Order::STATUS_RUNNING => new RunningState($this),
            Order::STATUS_PAUSED => new PausedState($this),
            Order::STATUS_COMPLETED => new CompletedState($this),
            Order::STATUS_PARTIAL_COMPLETED => new PartialCompletedState($this),
            Order::STATUS_CANCELED => new CanceledState($this),
            Order::STATUS_UPDATING => new UpdatingState($this),
            Order::STATUS_ERROR => new ErrorState($this),
            default => throw new ReportableException(
                "Cannot select state for status <{$this->status}>")
        };
    }

    public function chunksCompletedSum()
    {
        return $this->hasOne(Chunk::class)
            ->selectRaw('composite_order_id, sum(completed) as aggregate')
            ->groupBy('composite_order_id');
    }

    public function getChunksCompletedSumAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (!array_key_exists('chunksCompletedSum', $this->relations))
            $this->load('chunksCompletedSum');

        $related = $this->getRelation('chunksCompletedSum');

        // then return the count directly
        return ($related) ? (int)$related->aggregate : 0;
    }

//// lazy loading
//$post = Post::first();
//$post->commentsCount; // 4
//
//// eager loading
//$posts = Post::with('commentsCount')->get();
//$posts->first()->commentsCount;

    // -------------------------------------------------------------

    public function xUpdateChunks($loadedChunks)
    {
        $chunks = $loadedChunks
            ->where('composite_order_id', $this->id)
            ->whereNotIn('status', [
//                Order::STATUS_ERROR,
//                ^ that is off, because we allow manual update on errored chunks
//            todo: analyze and add other status
                Order::STATUS_COMPLETED,
            ]);

        foreach ($chunks as $chunk) {
            $chunk->updateStatus();
        }
    }

    public function writeLog(string $event, string $text='')
    {
        Log::info("[OL] order id {$this->id} $event $text");

        OLog::create([
            'composite_order_id' => $this->id,
            'event' => $event,
            'text' => $text,
        ]);
    }

    private function countChunks(Collection $chunks, string $status): int
    {
        return $chunks->where('status', $status)->count();
    }

    public function nextState(Collection $loadedChunks)
    {
        $chunks = $loadedChunks->where('composite_order_id', $this->id);
        $total = $chunks->count();

        $running = $chunks->whereIn('status', [
            Order::STATUS_RUNNING,
            Order::STATUS_CREATED, // empty add_request?
        ])->count();
        $completed = $this->countChunks($chunks, Order::STATUS_COMPLETED);
        $error = $this->countChunks($chunks, Order::STATUS_ERROR);
        $partial = $this->countChunks($chunks, Order::STATUS_PARTIAL_COMPLETED);
        $canceled = $this->countChunks($chunks, Order::STATUS_CANCELED);
        $paused = $this->countChunks($chunks, Order::STATUS_PAUSED);

        $text = "=== Update order {$this->id} Chunks: total = $total ";
        $text .= "running = $running completed = $completed partial = $partial ";
        $text .= "errored = $error\n";

        $oldStatus = $this->status;

        if ($total == $completed) {
            $this->complete();
        }
        elseif ($error > 0) {
            $this->error();
        }
        elseif ($running > 0) {
            $this->run();
        }
        elseif ($total == $completed + $partial + $canceled + $error + $paused) {
            if ($completed + $partial > 0) {
                $this->partial();
            }
            elseif ($canceled > 0) {
                $this->cancel();
            }
            elseif ($paused > 0) {
                $this->pause();
            }
            else {
                $this->error();
            }
        }
        else {
            $this->error();
        }

        $text .= "$oldStatus -> {$this->status}\n";
        echo $text;
    }

    public function getCompletedAttribute()
    {
        return (int)($this->chunks->sum('completed') + 0.5);
    }

    public function ga(): self
    {
        try {
            resolve(GoogleAnalytics::class)->generateData($this);
        }
        catch (Exception $e) {
            Log::error('[GA] Error on order ' . $this->id . ': ' . describe_exception($e, true));
        }
        return $this;
    }

    public function metaPixel(): self
    {
        try {
            resolve(metaPixel::class)->sendData($this);
        }
        catch (Exception $e) {
            Log::error('[MP] Error on order ' . $this->id . ': ' . describe_exception($e, true));
        }
        return $this;
    }

//    public function trackerStart(): self
//    {
//        if ($trackerClass = $this->userService->tracker) {
//            $orderParams = $this->params;
//            $tracker = new $trackerClass;
//            $orderParams['tracker_start'] = $tracker->getValue($orderParams);
//            $orderParams['tracker_now'] = $tracker->getValue($orderParams);
//            $this->params = $orderParams;
//            $this->save();
//        }
//        return $this;
//    }

    public function charge(): float
    {
        $charge = 0;
        foreach ($this->chunks as $chunk) {
            $charge += $chunk->details['charge'];
        }

        Log::info('[ORDER] order '. $this->id . " has charge $charge RUB");
        if ($charge < 0.01) {
            Log::info('[ORDER] WARNING: Charge is less then 0.01 on order' . $this->id);
        }

        return $charge;
    }

    public function profit($toCur = 'USD'): float
    {
        $cur    = $this->params['cur'];
        $cost   = $this->params['cost'];
        $charge = $this->charge();
        $currencyService = app(CurrencyService::class);

        // $this->charge always in RUB
        $charge = $currencyService->convert(from: 'RUB', to: $cur, amount: $charge);

        // 10% psp fixed commission
        $profit = 0.9 * $cost - $charge;

        if ($profit < 0) {
            Log::info('[ORDER] Negative profit on order' . $this->id);
        }


        return $currencyService->convert(from: $cur, to: $toCur, amount: $profit);
    }

    public function totalCountInAllChunks(): int
    {
        $total = 0;
        foreach ($this->chunks as $chunk) {
            $total += $chunk->details['count'];
        }
        return $total;
    }

    public function giveRefBonus(float $part): self
    {
        Log::info("ref bonus for id = {$this->id} completed part = $part");

        if (empty($this->user->parent)) return $this;

        $bonus = $part * $this->params['cost'] * 0.1;

        (new RefBonus(
          $bonus, // positive or negative
          $this->params['cur'],
          "Бонус за заказ {$this->id} user {$this->user->id}",
          $this->user->id,
          [$this->id]
        ))
          ->applyTransaction($this->user->parent);

        return $this;
    }

    public function getCompletedPart(): float
    {
        if (empty($this->params['count'])) {
            // getting count from min max posts
            try {
                $avg = ($this->params['min'] + $this->params['max']) / 2;
                $count = $avg * $this->params['posts'];
                Log::info('success');
            }
            catch (Exception $e) {
                Log::info($e->getMessage());
                Log::info("getCompletedPart(): could not calculate count");
            }
        }
        else {
            $count = $this->params['count'];
        }

        $val = 1.0 * $this->completed / $count;

        if ($val < 0.0) $val = 0.0;
        if ($val > 1.0) $val = 1.0;

        return $val;
    }

    public function scopePaid($q)
    {
        return $q->whereNotIn('status', [
            Order::STATUS_CREATED, Order::STATUS_SPLIT
        ]);
    }

    public function scopeNotPaid($q)
    {
        return $q->whereIn('status', [
            Order::STATUS_CREATED, Order::STATUS_SPLIT
        ]);
    }

    public function scopeShouldBeUpdated($q)
    {
        return $q->whereIn('status', [
           Order::STATUS_RUNNING, Order::STATUS_PAUSED,
        ]);
    }

    public function fromMain(): bool
    {
        $tag = $this->userService->tag;
        return str_contains($tag, '_MAIN') || str_contains($tag, '_LIGHT4');
    }

    public function giveMoneyBack(float $incompletePart): self
    {
        $value = $incompletePart * $this->params['cost'];

        try {
            (new MoneyBack(
              amount: $value,
              currency: $this->params['cur'],
              comment: "Заказ $this->id [incomplete $incompletePart]",
              orderIds: [$this->id]
            ))
            ->applyTransaction($this->user);

            $this->writeLog('Moneyback', "part = $incompletePart value = $value");

        }
        catch (Exception $e) {
            $this->writeLog('Moneyback error', $e->getMessage());
            Log::error($e->getMessage());
            Log::error($e->getFile() . ':' . $e->getLine());
        }

        return $this;
    }
}
