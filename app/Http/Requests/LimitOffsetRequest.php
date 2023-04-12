<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LimitOffsetRequest extends FormRequest
{
    public function order()
    {
        return $this->get('order_by') ?? 'id';
    }

    public function limit()
    {
        return $this->get('limit') ?? 10;
    }

    public function offset()
    {
        return $this->get('offset') ?? 0;
    }

    public function rules()
    {
        return [];
    }
}