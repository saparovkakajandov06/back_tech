<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class FirstRequest extends FormRequest
{

    function passedValidation()
    {
        $this->merge([
            'abc' => 'abc'
        ]);
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => 'the first request',
        ]);

        throw ValidationException::withMessages([
            'login' => 'bad login',
            'name' => 'good name',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
