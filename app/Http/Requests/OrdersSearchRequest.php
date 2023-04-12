<?php

namespace App\Http\Requests;

use App\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrdersSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'link'       => 'string',
            'cur'        => ['string', Rule::in(Transaction::CUR)],
            'tag'        => 'string',
            'date_from'  => 'date',
            'date_to'    => 'date',
            'cost'       => 'numeric',
            'cost_from'  => 'numeric',
            'cost_to'    => 'numeric',
            'username'   => 'string',
            'email'      => 'string',
            'platform'   => 'string',
            'id'         => 'integer',
            'payment_id' => 'integer',
            'offset'     => 'integer',
            'limit'      => 'integer',
        ];
    }
}
