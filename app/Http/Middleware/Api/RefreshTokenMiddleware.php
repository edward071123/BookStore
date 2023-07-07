<?php
namespace App\Http\Middleware\Api;

use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Log;

// use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
// use Illuminate\Support\Facades\Log;

class RefreshTokenMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     * @throws TokenInvalidException
     */
    public function handle($request, Closure $next)
    {
        // 檢查 token
        $this->checkForToken($request);

        // 1. 格式通過, 驗證是否為專屬(Guard)的token
        $presentGuard = Auth::getDefaultDriver();
        $token        = Auth::getToken();
        if (empty($token)) {
            // throw new \App\Exceptions\TokenFalseException('Unexpected token illegal.');
            $message = 'Unexpected token illegal.';
            return response()->json([
                'code'    => config('code.tokenExceptionError'),
                'message' => $message
            ], 200);
        }

        try {
            // 取得 token 內容
            $payload = Auth::manager()->getJWTProvider()->decode($token->get());
        } catch (JWTException $exception) {
            // return response()->json(['status' => $exception->getMessage()], 500);
            // throw new \App\Exceptions\TokenFalseException($exception->getMessage());
            $message = $exception->getMessage();
            return response()->json([
                'code'    => config('code.tokenExceptionError'),
                'message' => $message
            ], 200);
        }

        // 檢查token內帶的 guard 是否為目前 Auth 的 default guard
        if (empty($payload['guard']) || ($payload['guard'] !== $presentGuard)) {
            // throw new TokenInvalidException();
            // return response()->json(['status' => trans('Token guard is error.')], 500);
            // throw new \App\Exceptions\TokenFalseException('Token guard is error.');
            $message = 'Token guard is error.';
            return response()->json([
                'code'    => config('code.tokenExceptionError'),
                'message' => $message
            ], 200);
        }

        // 2. 開始檢查 token 是否過期
        try {
            // 檢查 user 登入狀態
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }
            // throw new UnauthorizedHttpException('jwt-auth', '未登入');
            // return response()->json(['status' => trans('Not logged')], 401);
            // throw new \App\Exceptions\TokenFalseException('Not logged.');
            $message = 'Not logged.';
            return response()->json([
                'code'    => config('code.tokenExceptionError'),
                'message' => $message
            ], 200);
        } catch (TokenExpiredException $exception) {
            // 刷新 token 並且加入 header
            try {
                // 刷新用户的 token
                $token = $this->auth->refresh();
                // 使用一次性登入保證此次 request 成功
                Auth::onceUsingId($this->auth->manager()->getPayloadFactory()
                    ->buildClaimsCollection()->toPlainArray()['sub']);
            } catch (JWTException $exception) {
                // 這裡代表 refresh 也過期 則無法刷新 必須重新登入
                // throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
                // throw new \App\Exceptions\TokenFalseException($exception->getMessage());
                $message = $exception->getMessage();
                return response()->json([
                    'code'    => config('code.tokenExceptionError'),
                    'message' => $message
                ], 200);
            }
        } catch (JWTException $exception) {
            // return response()->json(['status' => $exception->getMessage()], 500);
            // throw new \App\Exceptions\TokenFalseException($exception->getMessage());
            $message = $exception->getMessage();
            return response()->json([
                'code'    => config('code.tokenExceptionError'),
                'message' => $message
            ], 200);
        }

        // 夾在 header 內帶回
        return $this->setAuthenticationHeader($next($request), $token);
    }
}
