<?php

namespace App\Actions\Fediverse;

use App\Models\Fediverse\Account;
use Illuminate\Support\Facades\Http;

class FillAccountFromWebfinger
{
    public function fill(Account $account): Account
    {
        [$profileUrl, $dataUrl] = $this->getResource($account);

        $account->profile_url = $profileUrl;
        $account->data_url = $dataUrl;

        $data = $this->getData($dataUrl);

        $account->preferred_username = data_get($data, 'preferredUsername');
        $account->avatar_url = data_get($data, 'icon.url');
        $account->header_url = data_get($data, 'image.url');
        $account->display_name = data_get($data, 'name');
        $account->summary = data_get($data, 'summary');

        return $account;
    }

    private function getResource(Account $account): array
    {
        $url = "https://$account->domain/.well-known/webfinger?resource=acct:$account->handle";

        $response = Http::acceptJson()
            ->get($url)
            ->throw();

        $links = collect($response->json('links'));

        return [
            $links->where('rel', 'http://webfinger.net/rel/profile-page')->value('href'),
            $links->where('rel', 'self')->value('href'),
        ];
    }

    private function getData(string $url): array
    {
        $response = Http::acceptJson()
            ->get($url)
            ->throw();

        return $response->json();
    }
}
