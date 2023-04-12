<?php

namespace App\Events\Money;

use App\Transaction;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BaseTransaction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public $amount;
    public string $type; // set by child
    public string $comment;
    public string $id;
    public ?string $related_user_id;

    public function __construct(User $user, $amount, $comment='')
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->type = Transaction::UNKNOWN;
        $this->comment = $comment;
        $this->related_user_id = null;

        $this->id = uniqid();
    }
}
