<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use plugin\whatsappOtp\app\service\TokenService;

class AuthMiddleware
{
    public function process(Request $request, callable $next): Response
    {
        $auth = $request->header('authorization', '');
        if ($auth === '' || stripos($auth, 'Bearer ') !== 0) {
            return json(['code' => 401, 'msg' => 'Unauthorized']);
        }
        $token = trim(substr($auth, 7));
        $payload = TokenService::verifyAccessToken($token);
        if (!$payload) {
            return json(['code' => 401, 'msg' => 'Invalid token']);
        }
        $request->userId = $payload->sub ?? null;
        return $next($request);
    }
}
