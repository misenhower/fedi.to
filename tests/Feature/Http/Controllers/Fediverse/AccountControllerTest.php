<?php

namespace Tests\Feature\Http\Controllers\Fediverse;

use App\Actions\Fediverse\FetchAccountData;
use App\Models\Fediverse\Account;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_new_account()
    {
        $this->mock(FetchAccountData::class)
            ->shouldReceive('fetch')
            ->once();

        // Sanity check
        $this->assertSame(0, Account::count());

        $response = $this->get('/@FooBar@Example.net');

        $response->assertSuccessful();

        // There should now be one account in the DB
        $this->assertSame(1, Account::count());
        $account = Account::first();
        $this->assertSame('foobar', $account->username);
        $this->assertSame('example.net', $account->domain);

        $response->assertInertia(fn (AssertableInertia $page) => $page->where('account.id', $account->id));
    }

    /** @test */
    public function it_returns_an_existing_account()
    {
        Carbon::setTestNow();
        $this->mock(FetchAccountData::class)
            ->shouldNotReceive('fetch');
        Account::factory()->create([
            'username' => 'wrongaccount',
            'domain' => 'example.org',
            'fetched_at' => now(),
        ]);
        $account = Account::factory()->create([
            'username' => 'foobar',
            'domain' => 'example.net',
            'fetched_at' => now(),
        ]);

        $response = $this->get('/@FooBar@Example.net');

        $response->assertSuccessful();
        $response->assertInertia(fn (AssertableInertia $page) => $page->where('account.id', $account->id));
    }
}
