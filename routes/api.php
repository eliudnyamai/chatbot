<?php

use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/getmessages', [ChatbotController::class, 'replyToMessages']);
Route::get('/storeresponses', [ChatbotController::class, 'storeResponses']);
Route::get('/chatbotTest', [ChatbotController::class, 'ask']);
Route::get('/chatbot', [ChatbotController::class, 'index']);
Route::post('/chatbot', [ChatbotController::class, 'store']);
Route::post('/chatbotCreate', [ChatbotController::class, 'store']);
Route::get('/chatbot/{id}', [ChatbotController::class, 'show']);
Route::put('/chatbot/{id}', [ChatbotController::class, 'update']);
Route::delete('/chatbot/{id}', [ChatbotController::class, 'destroy']);

