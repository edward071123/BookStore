<?php
namespace App\Services;

use App\Repositories\MemberRepository;
use App\Resources\MemberResource;
use App\Resources\MemberAuthResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class MemberService extends Service
{
    protected $memberRepository;

    public function __construct(
        MemberRepository $memberRepository,
    ) {
        $this->memberRepository           = $memberRepository;
    }

    public function memberLogin($params)
    {
        $code           = config('code.apiSuccess');
        $message        = '';
        $httpStatusCode = 200;
        $token          = '';
        $tokenType      = 'member';
        $presentGuard   = Auth::getDefaultDriver();
        if (!$token = Auth::claims(['guard' => $presentGuard])->attempt([
            'email'         => $params['email'],
            'password'      => $params['password'],
            'status'        => config('setting.on'),
        ])) {
            $code           = config('code.tokenUnauthorized');
            $message        = '密碼或是帳號有誤';
            $httpStatusCode = 401;
            return [
                'code'              => $code,
                'message'           => $message,
                'token'             => $token,
                'httpStatusCode'    => $httpStatusCode,
                'expiresIn'         => 0,
                'user'              => '',
                'tokenType'         => $tokenType
            ];
        }

        return [
            'code'              => $code,
            'message'           => $message,
            'token'             => $token,
            'httpStatusCode'    => $httpStatusCode,
            'expiresIn'         => auth('members')->factory()->getTTL() * 60,
            'user'              => Auth::user()->email,
            'tokenType'         => $tokenType
        ];
    }

    public function memberLogOut()
    {
        $code           = config('code.apiSuccess');
        $message        = '';
        $httpStatusCode = 200;
        $member         = Auth::user();
        $member         =  $this->memberRepository->findOne($member->id);
        DB::connection()->beginTransaction();
        try {
            $member->mobile_notify_token = '';
            $member->save();
            Auth::logout();
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('Member logout Error:' . $e->getMessage());
            $message = 'Member logout Error.';
            $code    = config('code.apiExceptionError');
        }

        return [
            'code'              => $code,
            'message'           => $message,
            'httpStatusCode'    => $httpStatusCode
        ];
    }

    public function memberProfile()
    {
        $code      = config('code.apiSuccess');
        $message   = '';
        $getMember = Auth::user();
        if (!$getMember) {
            return [
                'code'              => $code,
                'message'           => $message,
                'data'              => []
            ];
        }

        $getMember =  $this->memberRepository->findOne($getMember->id);
        return [
            'code'              => $code,
            'message'           => $message,
            'data'              => new memberAuthResource($getMember)
        ];
    }

    public function registerMember($params)
    {
        $code     = config('code.apiSuccess');
        $message  = '';
        $nowYear  = date('Y');
        $nowMonth = date('m');
        DB::connection()->beginTransaction();
        try {
            $this->memberRepository->create(
                '會員',
                [
                    'name'                => $params['name'],
                    'password'            => Hash::make($params['password']),
                    'email'               => $params['email'],
                    'created_year'        => $nowYear,
                    'created_month'       => $nowMonth
                ]
            );
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('Register member Error:' . $e->getMessage());
            $message = '註冊會員失敗.';
            $code    = config('code.apiExceptionError');
        }

        return [
            'code'    => $code,
            'message' => $message
        ];
    }

    public function getMemberOne($id)
    {
        $member =  $this->memberRepository->findOne($id);

        if (!$member) {
            return [];
        }

        return new MemberResource($member);
    }
}
