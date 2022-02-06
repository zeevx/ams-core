<?php

namespace App\Http\Controllers\V1\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function user(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $data = [
            'user' => $user,
            'role' => $user->getRoleNames()[0]
        ];
        return ResponseHelper::successWithMessageAndData('User data returned successfully', $data);
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $validate = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:users,email,'.$user->id.',id',
                'phone' => 'required|unique:users,phone,'.$user->id.',id'
        ]);

        if ($validate->fails()){
            return ResponseHelper::errorWithMessage($validate->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $user->update($validate->validated());

        return ResponseHelper::successWithMessageAndData('User account updated successfully', $user);

    }

    public function updateProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $validate = Validator::make($request->all(), [
            'address' => 'required'
        ]);

        if ($validate->fails()){
            return ResponseHelper::errorWithMessage($validate->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $user->profile()->update($validate->validated());

        return ResponseHelper::successWithMessageAndData('User profile updated successfully', $user);

    }

    public function fetch(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::when($request->has('email'), function ($query) use ($request){
                return $query->where('email', $request->get('email'));
        })->when($request->has('phone'), function ($query) use ($request) {
            return $query->where('phone', $request->get('phone'));
        })->first();

        if (!$user){
            return ResponseHelper::errorWithMessage('User does not exist', UNPROCESSABLE_ENTITY);
        }

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone
        ];
        return ResponseHelper::successWithMessageAndData('User information fetched successfully', $data);
    }
}
