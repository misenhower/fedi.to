<?php

namespace Tests\Feature\Actions\Fediverse;

use App\Actions\Fediverse\FillAccountFromMastodonApi;
use App\Models\Fediverse\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FillAccountFromMastodonApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fills_account_details_from_the_mastodon_api()
    {
        Http::fake([
            'example.com/api/v1/accounts/lookup?acct=jdoe' => [
                'avatar' => 'https://example.net/avatar.jpg',
                'header' => 'https://example.net/header.jpg',
                'display_name' => 'Jane Doe',
                'note' => 'Some profile note',
                'bot' => true,
                'followers_count' => 1234,
                'following_count' => 321,
                'statuses_count' => 5432,
            ],
        ]);
        Http::preventStrayRequests();

        $account = Account::factory()->create([
            'username' => 'jdoe',
            'data_url' => 'https://example.com/users/jdoe',
        ]);

        (new FillAccountFromMastodonApi())->fill($account);

        // Make sure all fields are valid
        $account->save();
        $account->refresh();

        // Make sure the request was sent
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://example.com/api/v1/accounts/lookup?acct=jdoe'
                && $request->header('Accept') === ['application/json'];
        });

        // Make sure the fields were updated properly
        $this->assertSame('https://example.net/avatar.jpg', $account->avatar_url);
        $this->assertSame('https://example.net/header.jpg', $account->header_url);
        $this->assertSame('Jane Doe', $account->display_name);
        $this->assertSame('Some profile note', $account->summary);
        $this->assertSame(true, $account->bot);
        $this->assertSame(1234, $account->followers_count);
        $this->assertSame(321, $account->following_count);
        $this->assertSame(5432, $account->statuses_count);
    }
}
