<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DateTime;

class DoubledTransaction implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($transactionHistory)
    {
        $this->transactionHistory = $transactionHistory;
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


        $filter = array_filter($this->transactionHistory, function($history) use ($value){
            return ($history->merchant === $value->merchant and $history->amount === $value->amount);
        });


        if($filter){
            $lastTransaction =  array_pop($filter);
            $t1 = new DateTime($lastTransaction->time);
            $t2 = new DateTime($value->time);
            $intervalo = $t1->diff($t2);
            return !($intervalo->i <= 2);
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'doubled-transaction';
    }
}
