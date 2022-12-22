<?php

namespace Tests\Feature\Http\Controllers\Api\Fediverse;

use App\Actions\Fediverse\FetchAccountData;
use App\Models\Fediverse\Account;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow();
        Config::set('fediverse.account_refetch_after_minutes', 30);
    }

    /** @test */
    public function it_retrieves_accounts()
    {
        $account = Account::factory()->create([
            'fetched_at' => now(),
        ]);
        $this->mock(FetchAccountData::class)->shouldNotReceive('fetch');

        $response = $this->get("api/accounts/$account->id");

        $response->assertSuccessful();
        $response->assertJson(['data' => ['id' => $account->id]]);
    }

    /** @test */
    public function it_fetches_old_accounts()
    {
        $account = Account::factory()->create([
            'fetched_at' => now()->subMinutes(31),
        ]);
        $this->mock(FetchAccountData::class)
            ->shouldReceive('fetch')
            ->once();

        $response = $this->get("api/accounts/$account->id");

        $response->assertSuccessful();
        $response->assertJson(['data' => ['id' => $account->id]]);
    }
}
