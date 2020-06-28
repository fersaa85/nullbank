<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class InsufficientLimit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($availableLimit)
    {
        $this->availableLimit = $availableLimit;
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
        return $value < $this->availableLimit;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'insufficient-limit';
    }
}
