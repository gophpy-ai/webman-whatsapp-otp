<?php
use Webman\Route;

Route::post('/otp/send', [plugin\whatsapp_otp\controller\OtpController::class, 'send']);
Route::post('/otp/verify', [plugin\whatsapp_otp\controller\OtpController::class, 'verify']);
