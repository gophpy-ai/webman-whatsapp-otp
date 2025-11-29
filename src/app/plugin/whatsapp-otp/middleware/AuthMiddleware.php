<?php
namespace plugin\whatsappOtp\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use plugin\whatsappOtp\service\TokenService;

class AuthMiddleware {
    public function process(Request $request, callable $next): Response {
        $auth = $request->header('authorization');
        if (!$auth || !str_starts_with($auth,'Bearer ')) {
            return json(['code'=>401,'msg'=>'Unauthorized']);
        }
        $payload = TokenService::verifyAccessToken(substr($auth,7));
        if (!$payload) {
            return json(['code'=>401,'msg'=>'Token invalid']);
        }
        $request->userId = $payload->sub;
        return $next($request);
    }
}
