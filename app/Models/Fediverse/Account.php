<?php

namespace App\Models\Fediverse;

use App\Actions\Fediverse\FetchAccountData;
use App\Actions\Fediverse\FillAccountFromMastodonApi;
use App\Actions\Fediverse\FillAccountFromWebfinger;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class Account extends Model
{
    use HasUlids;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bot' => 'boolean',
        'fetched_at' => 'datetime',
    ];

    protected $appends = [
        'handle',
        'display_handle',
    ];

    // Attributes

    public function getHandleAttribute(): string
    {
        return "$this->username@$this->domain";
    }

    public function getDisplayHandleAttribute(): string
    {
        $username = $this->preferred_username ?? $this->username;

        return "$username@$this->domain";
    }

    // Helpers

    public static function locate(string $username, string $domain): static
    {
        $username = mb_strtolower($username);
        $domain = mb_strtolower($domain);

        return static::firstOrCreate([
            'username' => mb_strtolower($username),
            'domain' => mb_strtolower($domain),
        ]);
    }

    public function fetch(): static
    {
        return app(FetchAccountData::class)->fetch($this);
    }

    public function fetchIfEmpty(): static
    {
        if (!$this->fetched_at) {
            $this->fetch();
        }

        return $this;
    }

    public function fetchIfOld(): static
    {
        $cutoff = now()->subMinutes(config('fediverse.account_refetch_after_minutes'));
        if ($this->fetched_at <= $cutoff) {
            $this->fetch();
        }

        return $this;
    }
}
