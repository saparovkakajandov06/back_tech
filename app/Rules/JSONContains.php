<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class JSONContains implements Rule
{

    protected $fieldName;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $data = json_decode($value);
        $f = $this->fieldName;
        return isset($data->$f);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.contains_field');
    }
}
