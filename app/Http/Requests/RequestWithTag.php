<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestWithTag extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => 'the slug',
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
            'tag' => 'required|string',
            'slug' => 'required|string',
        ];
    }


    public function withValidator($validator)
    {
//        $validator->after(function ($validator) {
//            if (true) {
//                $validator->errors()
//                    ->add('field', 'Something is wrong with this field!');
//            }
//        });
        $this->merge([
            'data' => 'the data',
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tag.required' => 'A tag is required',
        ];
    }
}
