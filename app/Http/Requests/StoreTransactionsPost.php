<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use DateTime;
use Illuminate\Cache\RateLimiter;
use App\Rules\CardNotActive;


class StoreTransactionsPost extends FormRequest
{


    private $account;



    public function __construct(){
        $this->account = Cache::get('account')->account;
    }

    public function boot()
    {
        Validator::extend('foo', function ($attribute, $value, $parameters, $validator) {
            return $value == 'foo';
        });
    }


    protected function validationData()
    {

        return $this->json()->all();
    }

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
            'field2' => [new CardNotActive],
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make('Illuminate\Validation\Factory');

        if (method_exists($this, 'validator')) {
            return $this->container->call([$this, 'validator'], compact('factory'));
        }
        return $factory->make(
            $this->json()->all(), $this->container->call([$this, 'rules']), $this->messages(), ['field2']
        );
    }

    /**
     * Do foo with Validator
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */

/*
    public function withValidator($validator)
    {




        $validator->after(function ($validator) {


            $transaction = json_decode($this->getContent())->transaction;

            //Cache::put('transaction', [ $transaction ]);
            if ($this->cardNotActive()) {
                $validator->errors()->add('violations',  "card-not-active" );
            }


            if($this->insufficientLimit($transaction->amount)){
                $validator->errors()->add('violations',  "insufficient-limit" );
            }

            if($this->doubledTransaction($transaction)) {
                $validator->errors()->add('violations',  "doubled-transaction" );
            }


        });
    }
    */

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    private function cardNotActive(){
        return !$this->account->activeCard;
    }

    private function insufficientLimit($amount){
        return $this->account->availableLimit < $amount? true : false;
    }

    private function doubledTransaction($transaction) {

        $transactionHistory = Cache::get('transaction');
        $filter = array_filter($transactionHistory, function($history) use ($transaction){
            return ($history->merchant === $transaction->merchant and $history->amount === $transaction->amount);
        });

        if($filter){
           $lastTransaction =  array_pop($filter);
            $t1 = new DateTime($lastTransaction->time);
            $t2 = new DateTime($transaction->time);
            $intervalo = $t1->diff($t2);
            if($intervalo->i < 2){
                return false;
            }
        }

        return false;
    }




}
