<?php

use App\Http\Controllers\ChatbotController;
use App\Models\TrainingData;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $trainingData = TrainingData::all();
        return view('chatbot.index')
            ->with('data', $trainingData);
});
Route::get('/chatbot', [ChatbotController::class, 'index']);
Route::get('/create', [ChatbotController::class, 'create']);
Route::post('/store', [ChatbotController::class, 'store']);
Route::get('/train', [ChatbotController::class, 'train_bot']);
Route::delete('/delete/{id}', [ChatbotController::class, 'destroy']);
Route::get('/edit/{id}', [ChatbotController::class, 'show']);
Route::put('/update/{id}', [ChatbotController::class, 'update']);
Route::get('/getmessages', [ChatbotController::class, 'replyToMessages']);
Route::get('/sendmessage', [ChatbotController::class, 'sendSMS']);


