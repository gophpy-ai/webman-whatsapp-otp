<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\controller;

use support\Request;
use support\Db;
use plugin\whatsappOtp\app\dto\OtpRequestDto;
use plugin\whatsappOtp\app\service\OtpService;
use plugin\whatsappOtp\app\service\TokenService;
use plugin\whatsappOtp\app\service\WhatsappClient;

class AuthController
{
    private OtpService $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService(new WhatsappClient());
    }

    public function sendOtp(Request $request)
    {
        $data = $request->input();
        $phone = $data['phone'] ?? '';
        if ($phone === '') {
            return json(['code' => 400, 'msg' => 'phone required']);
        }
        $dto = new OtpRequestDto($phone);
        $res = $this->otpService->sendOtp($dto);
        if (!($res['success'] ?? false)) {
            return json(['code' => 500, 'msg' => 'failed to send', 'detail' => $res['error'] ?? null]);
        }
        return json(['code' => 0, 'msg' => 'OTP sent']);
    }

    public function login(Request $request)
    {
        $data = $request->input();
        $phone = $data['phone'] ?? '';
        $code = $data['otp'] ?? '';
        if ($phone === '' || $code === '') {
            return json(['code' => 400, 'msg' => 'phone and otp required']);
        }
        $cached = cache()->get('otp:' . $phone);
        if (!$cached || $cached !== $code) {
            return json(['code' => 400, 'msg' => 'Invalid or expired OTP']);
        }

        $user = Db::table('users')->where('phone', $phone)->first();
        if (!$user) {
            $id = Db::table('users')->insertGetId(['phone' => $phone]);
        } else {
            $id = $user->id;
        }

        $tokens = TokenService::issueTokens((int)$id);
        cache()->delete('otp:' . $phone);
        return json(['code' => 0, 'msg' => 'OK', 'data' => $tokens]);
    }

    public function refreshToken(Request $request)
    {
        $data = $request->input();
        $refresh = $data['refresh_token'] ?? '';
        if ($refresh === '') {
            return json(['code' => 400, 'msg' => 'refresh_token required']);
        }
        $payload = TokenService::verifyRefreshToken($refresh);
        if (!$payload) {
            return json(['code' => 400, 'msg' => 'Invalid refresh token']);
        }
        $tokens = TokenService::issueTokens((int)$payload->sub);
        return json(['code' => 0, 'data' => $tokens]);
    }
}
