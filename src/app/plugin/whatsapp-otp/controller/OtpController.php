<?php
namespace plugin\whatsapp_otp\controller;

use support\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class OtpController {

    public function send(Request $request) {
        $phone = $request->post('phone');
        if (!$phone) {
            return json(['error' => 'phone is required'], 400);
        }
        $code = rand(100000, 999999);
        // store in cache (webman cache or redis)
        cache()->set("otp_{$phone}", $code, 300);

        $cfg = config('plugin.whatsapp-otp.config');
        $url = "https://graph.facebook.com/v21.0/{$cfg['phone_number_id']}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'otp_login_code',
                'language' => ['code' => 'en_US'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $code]
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$cfg['waba_token'],
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true
        ]);
        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return json(['error' => 'failed to send', 'detail' => $err], 500);
        }

        return json(['msg'=>'sent']);
    }

    public function verify(Request $request) {
        $phone = $request->post('phone');
        $code = $request->post('code');

        if (!$phone || !$code) {
            return json(['error'=>'phone and code required'], 400);
        }

        $cached = cache()->get("otp_{$phone}");
        if (!$cached || $cached != $code) {
            return json(['error'=>'Invalid or expired OTP'], 400);
        }

        // create JWT
        $cfg = config('plugin.whatsapp-otp.config');
        $payload = [
            'phone' => $phone,
            'iat' => time(),
            'exp' => time() + ($cfg['jwt_expire'] ?? 3600)
        ];
        $jwt = JWT::encode($payload, $cfg['jwt_key'], 'HS256');

        // remove OTP
        cache()->delete("otp_{$phone}");

        return json(['token'=>$jwt]);
    }
}
