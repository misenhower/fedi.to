<?php

namespace Tests\Feature\Models\Fediverse;

use App\Models\Fediverse\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_handle_attributes()
    {
        $account = Account::factory()->create([
            'username' => 'someone',
            'domain' => 'example.net',
        ]);

        $this->assertSame('someone@example.net', $account->handle);
        $this->assertSame('someone@example.net', $account->display_handle);

        $account->update([
            'preferred_username' => 'SomeOne',
        ]);

        $this->assertSame('someone@example.net', $account->handle);
        $this->assertSame('SomeOne@example.net', $account->display_handle);
    }

    /** @test */
    public function it_locates_accounts()
    {
        $account = Account::factory()->create([
            'username' => 'someone',
            'domain' => 'example.net',
        ]);

        $result = Account::locate('SOMEONE', 'Example.net');
        $this->assertTrue($result->is($account));

        $result = Account::locate('SOMEONEelse', 'Example.net');
        $this->assertFalse($result->is($account));
        $this->assertSame('someoneelse', $result->username);
        $this->assertSame('example.net', $result->domain);
        $this->assertTrue($account->exists);
        $this->assertFalse($account->isDirty());
    }
}
