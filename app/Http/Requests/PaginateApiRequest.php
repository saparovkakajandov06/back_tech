<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class PaginateApiRequest extends FormRequest
{
    public function order()
    {
        return $this->get('order_by') ?? 'id';
    }

    public function sort()
    {
        return $this->get('sort') ?? 'desc';
    }

    public function perPage()
    {
        return $this->get('per_page') ?? 32;
    }

    public function rules()
    {
        return [];
    }
}