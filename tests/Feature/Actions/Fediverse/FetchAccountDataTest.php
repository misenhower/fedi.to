<?php

namespace Tests\Feature\Actions\Fediverse;

use App\Actions\Fediverse\FetchAccountData;
use App\Actions\Fediverse\FillAccountFromMastodonApi;
use App\Actions\Fediverse\FillAccountFromWebfinger;
use App\Models\Fediverse\Account;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use Tests\TestCase;

class FetchAccountDataTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_executes_the_appropriate_actions()
    {
        $account = Account::factory()->create();
        $this->mock(FillAccountFromWebfinger::class)
            ->shouldReceive('fill')
            ->with($account)
            ->once();
        $this->mock(FillAccountFromMastodonApi::class)
            ->shouldReceive('fill')
            ->with($account)
            ->once();

        $account->fetch();
    }

    /** @test */
    public function it_fetches_when_empty()
    {
        // fetchIfEmpty should only fetch the account if it hasn't been fetched before
        $account = Account::factory()->create(['fetched_at' => null]);
        $this->mock(FetchAccountData::class)->shouldReceive('fetch')->with($account)->once();
        $account->fetchIfEmpty();

        $account = Account::factory()->create(['fetched_at' => Carbon::parse('2022-01-01')]);
        $this->mock(FetchAccountData::class)->shouldNotReceive('fetch');
        $account->fetchIfEmpty();
    }

    /** @test */
    public function it_fetches_when_old()
    {
        Carbon::setTestNow('2022-01-01 12:00:00');
        Config::set('fediverse.account_refetch_after_minutes', 30);

        // fetchIfOld should only fetch if the current data is past the cutoff time,
        // or if the account has never been fetched.

        $account = Account::factory()->create(['fetched_at' => null]);
        $this->mock(FetchAccountData::class)->shouldReceive('fetch')->with($account)->once();
        $account->fetchIfOld();

        $account = Account::factory()->create(['fetched_at' => now()]);
        $this->mock(FetchAccountData::class)->shouldNotReceive('fetch');
        $account->fetchIfOld();

        $account = Account::factory()->create(['fetched_at' => now()->subMinutes(29)]);
        $this->mock(FetchAccountData::class)->shouldNotReceive('fetch');
        $account->fetchIfOld();

        $account = Account::factory()->create(['fetched_at' => now()->subMinutes(31)]);
        $this->mock(FetchAccountData::class)->shouldReceive('fetch')->with($account)->once();
        $account->fetchIfOld();
    }
}
