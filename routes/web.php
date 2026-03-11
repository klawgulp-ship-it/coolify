<?php

use App\Http\Controllers\Auth\OAuthController;

Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');
});
