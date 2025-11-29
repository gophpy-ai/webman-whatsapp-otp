<?php
namespace plugin\whatsappOtp\service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService {
    public static function issueTokens($userId) {
        $c = config('plugin.whatsapp-otp.jwt');
        $now = time();

        $access = [
            'sub'=>$userId,'type'=>'access','iat'=>$now,'exp'=>$now+$c['access_exp']
        ];
        $refresh = [
            'sub'=>$userId,'type'=>'refresh','iat'=>$now,'exp'=>$now+$c['refresh_exp']
        ];

        return [
            'access_token'=>JWT::encode($access,$c['access_secret'],'HS256'),
            'refresh_token'=>JWT::encode($refresh,$c['refresh_secret'],'HS256')
        ];
    }

    public static function verifyAccessToken($t){
        try { return JWT::decode($t,new Key(config('plugin.whatsapp-otp.jwt.access_secret'),'HS256')); }
        catch(\Throwable $e){ return false; }
    }

    public static function verifyRefreshToken($t){
        try { return JWT::decode($t,new Key(config('plugin.whatsapp-otp.jwt.refresh_secret'),'HS256')); }
        catch(\Throwable $e){ return false; }
    }
}
