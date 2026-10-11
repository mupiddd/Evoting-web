<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;

Route::get('/', [VoteController::class, 'showTokenForm'])->name('vote.token');
Route::post('/token/verify', [VoteController::class, 'checkToken'])->name('vote.token.verify');

Route::get('/booth', [VoteController::class, 'showBooth'])->name('vote.booth');
Route::post('/vote/submit', [VoteController::class, 'submitVote'])->name('vote.submit');

Route::get('/success', [VoteController::class, 'success'])->name('vote.success');

Route::get('/results', [VoteController::class, 'results'])->name('vote.results');

Route::get('/admin/tokens', [VoteController::class, 'adminTokens'])->name('tokens.index');
Route::post('/admin/tokens/generate', [VoteController::class, 'generateTokens'])->name('tokens.generate');