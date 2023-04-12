<?php

namespace App\Http\Requests\StatisticsRequests;

use Illuminate\Foundation\Http\FormRequest;

class ErrorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'api_alias' => 'required',
            'data' => 'nullable',
            'status' => 'required'
        ];
    }
}
