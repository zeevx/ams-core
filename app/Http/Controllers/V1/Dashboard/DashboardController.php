<?php

namespace App\Http\Controllers\V1\Dashboard;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $data = [
          'email_verified' => !($user->email_verified_at == null),
          'sent_messages' => $user->sent_messages->count(),
          'received_messages' => $user->received_messages->count(),
          'unread_received_messages' => $user->received_messages()->where('read_at',null)->count(),
          'notices' => $user->notices->count(),
          'unread_notices' => $user->notices()->where('read_at',null)->count(),
        ];

        return ResponseHelper::successWithMessageAndData('Stats fetched successfully', $data);
    }
}
