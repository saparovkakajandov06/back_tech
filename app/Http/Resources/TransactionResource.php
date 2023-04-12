<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Transaction;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $amountFormatted = ($this->amount > 0)
            ? '+ ' . number_format($this->amount, 2, '.', ' ')
            : '- ' . number_format(abs($this->amount), 2, '.', ' ');

        switch ($this->transaction_group) {
            case Transaction::GROUP_DEPOSITED;
              $comment = 'Пополнение баланса';
            break;
            case Transaction::GROUP_EARNED;
              $comment = 'Партнерская программа';
            break;
            case Transaction::GROUP_WITHDRAWN;
              $comment = 'Вывод средств';
            break;
            default:
              $comment = $this->comment;
            break;
        }

        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'amount_formatted' => $amountFormatted,
            'comment' => $comment,
            'created_at' => $this->created_at,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y в H:i:s'),
            'commission' => number_format($this->commission, 2, '.', ''),
            'group' => $this->transaction_group,
      ];
    }
}
