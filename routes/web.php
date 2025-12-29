<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use Laravel\Socialite\Facades\Socialite;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(url('/admin/login'));
});

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');

Route::match(['get', 'post'], '/logout', function (Request $request) {

    // filament()->getLogoutUrl()
    Filament::auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    // return redirect(url(request()->input('panel') . '/login'));

    if (request()->input('team')) {
        // return redirect()->intended(url("/guest/login?team=" . request()->input('team')));
        return redirect()->intended(url("/admin/login?team=" . request()->input('team')));
    }

    return redirect(filament()->getLoginUrl());
})->name('logout');


Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'password' => bcrypt(Str::random(24)), // Dummy password
        ]
    );

    Auth::login($user);

    return redirect()->intended(Filament::getUrl());
})->name('auth.google.callback');



Route::get('/auth/github/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('auth.github.redirect');

Route::get('/auth/github/callback', function () {
    // $githubUser = Socialite::driver('github')->user();
    try {
        $githubUser = Socialite::driver('github')->user();
    } catch (\Exception $e) {
        return redirect()->intended(Filament::getUrl());
    }

    $user = User::firstOrCreate(
        ['email' => $githubUser->getEmail()],
        [
            'name' => $githubUser->getName() ?? $githubUser->getNickname(),
            'password' => bcrypt(Str::random(24)),
        ]
    );

    Auth::login($user);

    return redirect()->intended(Filament::getUrl());
})->name('auth.github.callback');


Route::get('/print-invoice/{id}', [PdfController::class, 'invoice'])
    ->name('print.invoice');

Route::middleware(['auth'])->group(function () {
    Route::get('/print-invoice/{id}', [PdfController::class, 'invoice'])
        ->name('print.invoice');
});
