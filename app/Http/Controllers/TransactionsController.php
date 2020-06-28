<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreTransactionsPost;
use Illuminate\Cache\RateLimiter;
use App\Rules\CardNotActive;
use App\Rules\InsufficientLimit;
use App\Rules\DoubledTransaction;

class TransactionsController extends Controller
{


    public function store(Request $request){

        $account = Cache::get('account')->account;
        $transactionHistory = is_array(Cache::get('transaction')) ? Cache::get('transaction') : [];
        $transaction = json_decode($request->getContent())->transaction;

        $validator = Validator::make(
            ['activeCard' => $account->activeCard,
             'availableLimit' =>  $transaction->amount ,
             'doubledTransaction' =>  $transaction
            ],

            ['activeCard' => [new CardNotActive],
             'availableLimit' =>  [new InsufficientLimit($account->availableLimit)],
             'doubledTransaction' =>  [ new DoubledTransaction($transactionHistory)]
        ]);


        if($validator->fails()){

            $error = array_values($validator->errors()->messages());
            $response = $this->violations($account, [ $error[0][0]  ]);
        }else{


            // save data cache
            array_push($transactionHistory, $transaction);
            Cache::put('transaction', $transactionHistory, now()->addMinutes(10));

            //update account
            $account->availableLimit = $account->availableLimit - $transaction->amount ;
            Cache::put('account', (object) ["account" => $account]);

            $response = $this->violations($account, []);
        }

        return response()->json($response);

    }


    function violations($account, $violations){
        return (object) ["account" => array_merge(  (array)$account, array("violations" =>  $violations) )];

    }
}
