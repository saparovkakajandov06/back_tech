<?php

namespace App\Rules;

use App\UserService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Tag implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
// Log::info("[newprices]|{$tag}|{$value}|Cache::get($key)");

        return ! empty(UserService::findByTagCached($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Tag :attribute not found';
    }
}
