<?php

namespace App\Http\Controllers\Api\Fediverse;

use App\Http\Controllers\Controller;
use App\Models\Fediverse\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(Account $account)
    {
        $account->fetchIfOld();

        return [
            'data' => $account,
        ];
    }
}
