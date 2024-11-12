<?php /** @noinspection ALL */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
    public function logout()
    {
        JWTAuth::parseToken()->invalidate();
        return response()->json([
            'status' => true,
            'message' => trans('messages.logout'),
            'data' => [],
        ], 200);
    }
}
