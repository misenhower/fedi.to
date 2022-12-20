<?php

namespace App\Models\Fediverse;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
