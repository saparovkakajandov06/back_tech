<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Transaction;

class TransactionTotalsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */



    public function toArray($request)
    {
        $amountFormatted = number_format($this->amount, 2, '.', ' ');
        $symbol = '';

        switch ($this->transaction_group) {
            case Transaction::GROUP_EARNED;
              $amountFormatted = $amountFormatted;
              $symbol = '+';
            break;
            case Transaction::GROUP_WITHDRAWN;
              $amountFormatted = number_format(abs($this->amount), 2, '.', ' ');
              $symbol = '-';
            break;
        }

        return [
            'title' => $this->title,
            'group' => $this->transaction_group,
            'symbol' => $symbol,
            'amount_formatted' => $amountFormatted,
            'amount' => $this->amount,
        ];
    }
}
