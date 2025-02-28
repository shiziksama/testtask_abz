<?php
use App\Http\Controllers\ApiController;
use App\Http\Middleware\ApiTokensMiddleware;

Route::get('/token', [ApiController::class, 'getToken']);

Route::get('/users', [ApiController::class, 'getUsers']);
Route::get('/users/{id}', [ApiController::class, 'getUserById']);
Route::get('/positions', [ApiController::class, 'getPositions']);


Route::post('/users', [ApiController::class, 'registerUser'])->middleware(ApiTokensMiddleware::class);

