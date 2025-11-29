<?php
use Webman\Route;
use plugin\whatsappOtp\middleware\AuthMiddleware;

Route::post('/otp/send', [plugin\whatsappOtp\controller\AuthController::class, 'sendOtp']);
Route::post('/otp/login', [plugin\whatsappOtp\controller\AuthController::class, 'login']);
Route::post('/otp/refresh', [plugin\whatsappOtp\controller\AuthController::class, 'refresh']);

Route::group('/user', function () {
    Route::get('/profile', function ($request) {
        return json(['code'=>0,'user_id'=>$request->userId]);
    });
})->middleware([AuthMiddleware::class]);
