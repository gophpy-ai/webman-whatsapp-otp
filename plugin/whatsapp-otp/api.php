<?php
declare(strict_types=1);

use plugin\whatsappOtp\app\controller\AuthController;

return [
    'POST /otp/send' => [AuthController::class, 'sendOtp'],
    'POST /otp/login' => [AuthController::class, 'login'],
    'POST /auth/refresh' => [AuthController::class, 'refreshToken'],
    // protected example
    'GET /user/profile' => [function($request){
        return json(['code'=>0,'user_id'=>$request->userId ?? null]);
    }],
];
