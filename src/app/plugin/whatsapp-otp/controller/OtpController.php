<?php
namespace plugin\whatsapp_otp\controller;

use support\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class OtpController {

    public function send(Request $request) {
        $phone = $request->post('phone');
        $code = rand(100000, 999999);
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
        curl_close($ch);

        return json(['msg'=>'sent', 'result'=>json_decode($res,true)]);
    }

    public function verify(Request $request) {
        $phone = $request->post('phone');
        $code = $request->post('code');
        $cached = cache()->get("otp_{$phone}");

        if ($cached != $code) {
            return json(['error'=>'Invalid or expired OTP'], 400);
        }

        $cfg = config('plugin.whatsapp-otp.config');
        $payload = [
            'phone' => $phone,
            'exp' => time() + $cfg['jwt_expire']
        ];
        $jwt = JWT::encode($payload, $cfg['jwt_key'], 'HS256');

        return json(['token'=>$jwt]);
    }
}
