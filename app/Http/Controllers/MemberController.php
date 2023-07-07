<?php
namespace App\Http\Controllers;

use App\Help\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\MemberService;
use App\Requests\MemberRegisterRequest;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * 註冊會員.
     *
     * @param  MemberRegisterRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register(MemberRegisterRequest $request)
    {
        $request->validated();
        $response = $this->memberService->registerMember($request->all());
        return response()->json(
            new JsonResponse(false, [], $response['code'], $response['message']),
            201
        );
    }
}
