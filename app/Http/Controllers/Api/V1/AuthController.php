<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Helpers;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email'    => ['required', Rule::exists(User::class)],
            'password' => 'required',
        ]);

        $user = User::where('email', Helpers::normalize($request->input('email'), 'email'))->first();

        $password = Helpers::normalize($request->input('password'), 'password');
        if(!Hash::check($password, $user->password)){
            return response()->json([
                'password' => [trans('validation.password')]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->respondWithToken(Auth::login($user));
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function me(): UserResource
    {
        return new UserResource(Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => [
                'code' => trans('auth.logout')
            ]
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => Auth::factory()->getTTL() * 60
            ]
        ]);
    }
}
