<?php

Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\AuthController::class, 'redirectToProvider'])
    ->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\AuthController::class, 'handleProviderCallback'])
    ->name('oauth.callback');
