<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('username');
            $table->string('domain');
            $table->index(['domain', 'username']);

            $table->string('preferred_username')->nullable();
            $table->string('display_name')->nullable();
            $table->text('summary')->nullable();

            $table->string('profile_url')->nullable();
            $table->string('data_url')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('header_url')->nullable();

            $table->boolean('bot')->nullable();

            $table->unsignedInteger('followers_count')->nullable();
            $table->unsignedInteger('following_count')->nullable();
            $table->unsignedInteger('statuses_count')->nullable();

            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
