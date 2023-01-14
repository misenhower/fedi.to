<?php

namespace Tests\Feature\Actions\Fediverse;

use App\Actions\Fediverse\FillAccountFromWebfinger;
use App\Models\Fediverse\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FillAccountViaWebfingerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fills_account_details_from_user_data()
    {
        Http::fake([
            'example.com/.well-known/webfinger?resource=acct:jdoe@example.com' => [
                'links' => [
                    [
                        'rel' => 'http://webfinger.net/rel/profile-page',
                        'href' => 'https://example.com/users/jdoe/profile',
                    ],
                    [
                        'rel' => 'self',
                        'href' => 'https://example.com/users/jdoe/self',
                    ],
                ],
            ],
            'example.com/users/jdoe/self' => [
                'preferredUsername' => 'JDoe',
                'icon' => ['url' => 'https://example.net/icon.jpg'],
                'image' => ['url' => 'https://example.net/image.jpg'],
                'name' => 'Jane Doe',
                'summary' => 'Some user bio',
            ],
        ]);
        Http::preventStrayRequests();

        $account = new Account([
            'username' => 'jdoe',
            'domain' => 'example.com',
        ]);

        (new FillAccountFromWebfinger())->fill($account);

        // Make sure all fields are valid
        $account->save();
        $account->refresh();

        // Make sure the first request was sent
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://example.com/.well-known/webfinger?resource=acct:jdoe@example.com'
                && $request->header('Accept') === ['application/json'];
        });
        $this->assertSame('https://example.com/users/jdoe/profile', $account->profile_url);
        $this->assertSame('https://example.com/users/jdoe/self', $account->data_url);

        // Make sure the second request was sent
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://example.com/users/jdoe/self'
                && $request->header('Accept') === ['application/activity+json'];
        });
        $this->assertSame('JDoe', $account->preferred_username);
        $this->assertSame('https://example.net/icon.jpg', $account->avatar_url);
        $this->assertSame('https://example.net/image.jpg', $account->header_url);
        $this->assertSame('Jane Doe', $account->display_name);
        $this->assertSame('Some user bio', $account->summary);
    }
}
