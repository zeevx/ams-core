<?php

namespace App\Http\Controllers\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validate = Validator::make($request->all(),[
           'email' => 'required',
           'password' => 'required'
        ]);

        if ($validate->fails()){
            return ResponseHelper::errorWithMessage($validate->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $token = $this->getPersonalAccessToken($user);
                $role = $user->getRoleNames()[0];

                $data = [
                    'user' =>  $user,
                    'role' =>  $role,
                    'token_type' =>  'Bearer',
                    'token' =>  $token->accessToken,
                ];
                return ResponseHelper::successWithMessageAndData('Authentication successful',$data);

            } else {
                return ResponseHelper::errorWithMessage('Invalid Password',UNAUTHORIZED);
            }
        } else {
            return ResponseHelper::errorWithMessage('This user does not exists',UNAUTHORIZED);
        }
    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|unique:users',
            'address' => 'required',
            'password' => 'required',
            "confirm_password" => 'required|string|same:password',
        ]);

        if ($validate->fails()){
            return ResponseHelper::errorWithMessage($validate->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);
        $user->profile()->create([
            'address' => $request->address
        ]);
        $user->wallet()->create();
        $user->assignRole('client');

        $token = $this->getPersonalAccessToken($user);
        $role = $user->getRoleNames()[0];

        $data = [
            'user' =>  $user,
            'role' =>  $role,
            'token_type' =>  'Bearer',
            'token' =>  $token->accessToken,
        ];

        return ResponseHelper::successWithMessageAndData('Account created successful',$data);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $user->token()->revoke();
        return ResponseHelper::successWithMessage('Logout successful');
    }

    public function getPersonalAccessToken($user)
    {
        if (request()->remember_me === 'true')
            Passport::personalAccessTokensExpireIn(now()->addDays(15));

        return $user->createToken('Personal Access Token');
    }
}
