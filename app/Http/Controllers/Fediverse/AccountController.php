<?php

namespace App\Http\Controllers\Fediverse;

use App\Http\Controllers\Controller;
use App\Models\Fediverse\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function show(string $username, string $domain)
    {
        $account = Account::locate($username, $domain)->fetchIfEmpty();

        return Inertia::render('Fediverse/Account', [
            'account' => $account,
        ]);
    }
}
