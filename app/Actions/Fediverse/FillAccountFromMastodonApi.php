<?php

namespace App\Actions\Fediverse;

use App\Models\Fediverse\Account;
use Illuminate\Support\Facades\Http;

class FillAccountFromMastodonApi
{
    public function fill(Account $account): Account
    {
        $data = $this->getData($account);

        $account->avatar_url = data_get($data, 'avatar');
        $account->header_url = data_get($data, 'header');
        $account->display_name = data_get($data, 'display_name');
        $account->summary = data_get($data, 'note');

        $account->bot = (bool)data_get($data, 'bot');
        $account->followers_count = data_get($data, 'followers_count');
        $account->following_count = data_get($data, 'following_count');
        $account->statuses_count = data_get($data, 'statuses_count');

        return $account;
    }

    private function url(Account $account): string
    {
        $host = parse_url($account->data_url, PHP_URL_HOST);

        return "https://$host/api/v1/accounts/lookup";
    }

    private function getData(Account $account): array
    {
        $response = Http::acceptJson()
            ->get($this->url($account), ['acct' => $account->username])
            ->throw();

        return $response->json();
    }
}
