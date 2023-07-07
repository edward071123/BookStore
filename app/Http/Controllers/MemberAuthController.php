<?php
namespace App\Http\Controllers;

use App\Help\JsonResponse;
use App\Requests\MemberAuthRequest;
use App\Services\MemberService;

class MemberAuthController extends Controller
{
    protected $memberService;

    /**
     * Create a new MemberAuthController instance.
     *
     * @return void
     */
    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Get a JWT via given credentials.
     * @param MemberAuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(MemberAuthRequest $request)
    {
        $request->validated();
        $params   = $request->all();
        $response = $this->memberService->memberLogin($params);
        return response()->json(
            [
                'code'          => $response['code'],
                'message'       => $response['message'],
                'access_token'  => $response['token']
            ],
            $response['httpStatusCode']
        );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $response = $this->memberService->memberLogOut();
        return response()->json(
            new JsonResponse(false, [], $response['code'], $response['message']),
            $response['httpStatusCode']
        );
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $response = $this->memberService->memberProfile();
        return response()->json($response);
    }
}
