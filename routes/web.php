<?php

use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VoteController::class, 'showTokenForm'])->name('vote.token');
Route::post('/token/verify', [VoteController::class, 'checkToken'])->name('vote.token.verify');

Route::get('/booth', [VoteController::class, 'showBooth'])->name('vote.booth');
Route::post('/vote/submit', [VoteController::class, 'submitVote'])->name('vote.submit');

Route::get('/success', [VoteController::class, 'success'])->name('vote.success');

Route::get('/results', [VoteController::class, 'results'])->name('vote.results');

// Autentikasi Panitia (Admin)
Route::get('/admin/login', [VoteController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [VoteController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/admin/logout', [VoteController::class, 'adminLogout'])->name('admin.logout');

// Panel Panitia - Manajemen Token
Route::get('/admin/tokens', [VoteController::class, 'adminTokens'])->name('tokens.index');
Route::post('/admin/tokens/generate', [VoteController::class, 'generateTokens'])->name('tokens.generate');
Route::delete('/admin/tokens/{token}', [VoteController::class, 'destroyToken'])->name('tokens.destroy');
Route::post('/admin/tokens/reset-unused', [VoteController::class, 'resetUnusedTokens'])->name('tokens.reset-unused');

// Panel Panitia - Manajemen Kandidat (CRUD + Foto)
Route::get('/admin/candidates', [VoteController::class, 'adminCandidates'])->name('candidates.index');
Route::post('/admin/candidates', [VoteController::class, 'storeCandidate'])->name('candidates.store');
Route::put('/admin/candidates/{candidate}', [VoteController::class, 'updateCandidate'])->name('candidates.update');
Route::delete('/admin/candidates/{candidate}', [VoteController::class, 'destroyCandidate'])->name('candidates.destroy');
