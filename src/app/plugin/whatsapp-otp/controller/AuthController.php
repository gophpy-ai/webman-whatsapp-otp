<?php
namespace plugin\whatsappOtp\controller;

use support\Request;
use support\Db;
use plugin\whatsappOtp\service\TokenService;
use plugin\whatsappOtp\service\WhatsappService;

class AuthController {
    public function sendOtp(Request $request){
        $phone=$request->post('phone');
        if(!$phone) return json(['code'=>400,'msg'=>'Phone required']);
        $otp=rand(100000,999999);
        cache()->set("otp:$phone",$otp,300);
        WhatsappService::sendOtp($phone,$otp);
        return json(['code'=>0,'msg'=>'OTP sent']);
    }

    public function login(Request $request){
        $phone=$request->post('phone');
        $otp=$request->post('otp');
        if(cache()->get("otp:$phone")!=$otp){
            return json(['code'=>400,'msg'=>'OTP invalid']);
        }

        $user=Db::table('users')->where('phone',$phone)->first();
        $id=$user?$user->id:Db::table('users')->insertGetId(['phone'=>$phone]);

        $tokens=TokenService::issueTokens($id);
        return json(['code'=>0,'msg'=>'Login success','data'=>$tokens]);
    }

    public function refresh(Request $request){
        $r=$request->post('refresh_token');
        $payload=TokenService::verifyRefreshToken($r);
        if(!$payload) return json(['code'=>400,'msg'=>'Invalid refresh token']);
        return json(['code'=>0,'data'=>TokenService::issueTokens($payload->sub)]);
    }
}
