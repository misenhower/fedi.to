<?php

namespace App\Actions\Fediverse;

use App\Models\Fediverse\Account;
use Throwable;

class FetchAccountData
{
    public function fetch(Account $account): Account
    {
        app(FillAccountFromWebfinger::class)->fill($account);

        try {
            app(FillAccountFromMastodonApi::class)->fill($account);
        } catch (Throwable $e) {
            report($e);
        }

        $account->touch('fetched_at');

        return tap($account)->save();
    }
}
