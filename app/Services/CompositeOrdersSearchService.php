<?php

namespace App\Services;

use App\Domain\Models\CompositeOrder;
use App\Services\Search\BasicSearchService;
use Illuminate\Support\Str;
use App\Payment;
use Illuminate\Support\Facades\Log;

class CompositeOrdersSearchService extends BasicSearchService
{
    public function __construct()
    {
        parent::__construct();

        $this->query = CompositeOrder::paid()
            ->select([
                'id',
                'user_id',
                'status',
                'created_at',
                'params',
                'user_service_id',
            ])->with([
                'chunksCompletedSum',
                'userService' => function ($q) {
                    $q->select('id', 'tag', 'name', 'platform');
                },
                'user' => function ($q) {
                    $q->select('id', 'roles', 'name', 'email');
                }
            ])->orderBy('id', 'desc');
    }

    public function setLink($link): static
    {
        if (! empty($link)) {
            $link = parse_url($link)['path'] ?? $link;

            $link = preg_quote($link);

            $this->query->where(function ($query) use ($link) {
                $query->where('params->link', '~*', $link)
                    ->orWhere('params->login', '~*', $link);
            });
        }
        return $this;
    }

    public function setCur($cur): static
    {
        if (!empty($cur)) {
            $this->query->where("params->cur", "$cur");
        }
        return $this;
    }

    public function setTag($tag): static
    {
        if (!empty($tag)) {
            $tag = preg_quote(Str::upper($tag));
            $this->query->whereHas('userService', function ($q) use ($tag) {
                $q->where('tag', '~', "$tag");
            });
        }
        return $this;
    }

    public function setStatuses($statuses): static
    {
        if (!empty($statuses)) {
            $ss = Str::of($statuses)->trim()->upper()->explode(' ');
            $this->query->whereIn('status', $ss);
        }
        return $this;
    }

    public function setCost($cost): static
    {
        if (! empty($cost)) {
            $e = 0.005;
            $this->query
                 ->whereRaw("ABS((params->>'cost')::numeric - $cost) < $e");
        }
        return $this;
    }

    public function setCostFrom($from): static
    {
        if (! empty($from)) {
            $e = 0.005;
            $this->query->whereRaw("(params->>'cost')::numeric >= $from - $e");
        }
        return $this;
    }

    public function setCostTo($to): static
    {
        if (! empty($to)) {
            $e = 0.005;
            $this->query->whereRaw("(params->>'cost')::numeric <= $to + $e");
        }
        return $this;
    }

    public function setUsername($username): static
    {
        if (! empty($username)) {
            $username = preg_quote($username);

            $this->query->whereHas('user', function($q) use ($username) {
                $q->where('name', '~*', "$username");
            });
        }
        return $this;
    }

    public function setEmail($email): static
    {
        if (! empty($email)) {
            $email = preg_quote($email);

            $this->query->whereHas('user', function($q) use ($email) {
                $q->where('email', '~*', "$email");
            });
        }
        return $this;
    }

    public function setPlatform($platform): static
    {
        if (! empty($platform)) {
            $platform = Str::ucfirst(Str::lower($platform));
            $this->query->whereHas('userService', function($q) use ($platform) {
                $q->where('platform', $platform);
            });
        }
        return $this;
    }

    public function firstOrder()
    {
        $first = CompositeOrder::orderBy('id','ASC')
            ->firstOrFail();

        return $first?->created_at;
    }

    public function usersFirstOrder($id)
    {
        $first = CompositeOrder::where('user_id', $id)
            ->orderBy('id','ASC')
            ->first();

        return $first?->created_at;
    }

    public function setOrderId(?int $id): static
    {
        if (!empty($id)) {
            $this->query->where('id', $id);
        }
        return $this;
    }

    public function setPaymentId (?int $paymentId): static
    {
        if (empty($paymentId)) {
            return $this;
        }
        $orderIds = [];
        if ($payment = Payment::where('id', $paymentId)->first()) {
            $orderIds = $payment->order_ids ?? [];
        }
        $this->query->whereIn('id', $orderIds);
        return $this;
    }
}
