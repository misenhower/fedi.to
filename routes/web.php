<?php

use App\Http\Controllers\Fediverse\AccountController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::inertia('/', 'Welcome');

Route::get('@{username}@{domain}', [AccountController::class, 'show']);

Route::inertia('oauth/mastodon/return', 'Fediverse/Auth/Return');
