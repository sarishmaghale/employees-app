<?php

namespace App\Helpers;

class JsonReponse
{
    public static function success($message = '', $data = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }
    public static function error($message = '', $data = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
