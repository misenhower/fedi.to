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
        ])->withViewData([
            'og' => [
                'title' => "{$account->display_name} ({$account->display_handle}) / Fedi.to",
                'description' => $account->summary,
                'image' => $account->avatar_url,
            ],
        ]);
    }
}
