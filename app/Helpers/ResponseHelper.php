<?php
namespace App\Helpers;

class ResponseHelper {

    public static function success(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
           'status' => 'success'
        ], 201);
    }

    public static function successWithMessage($message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public static function successWithMessageAndData($message, $data): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($status_code): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error'
        ], $status_code);
    }

    public static function errorWithMessage($message, $status_code): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $status_code);
    }

    public static function errorWithMessageAndData($message, $data, $status_code): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
}
