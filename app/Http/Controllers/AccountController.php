<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        //Cache::flush();
        $account = json_decode($request->getContent());

        if (!Cache::has('account')) {
            Cache::put('account', $account,  now()->addMinutes(10));

        }else {

            $account = (object)  array_merge( (array) $account, array("violations" => [ "account-already-initialized" ]) );

        }

        return response()->json($account);
    }
}
