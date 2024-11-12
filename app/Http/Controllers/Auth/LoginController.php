<?php /** @noinspection ALL */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DeviceToken;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'device_token' => ['required', 'string']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $info = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::guard('users')->attempt($info)) {
            $user = User::where('email', $request->email)->first();
            if ($user->email_verified_at) {
                $block = DB::table('blocks')
                    ->where('user_id', $user->id)
                    ->get()->last();
                if ($block != null && $block->status)
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.login_blocked'),
                        'data' => [],
                    ], 401);
                $token = JWTAuth::fromUser($user);
                $data = $user;
                $data['token'] = $token;
                DeviceToken::where('user_id', $user->id)
                    ->update([
                        'device_token' => $request->device_token
                    ]);
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.login'),
                    'data' => $data,
                ], 200);
            } else {
                $user->delete();
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.login_fail'),
                    'data' => [],

                ], 401);

            }
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('messages.password_error'),
                'data' => []
            ], 401);
        }
    }

    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:admins,email'],
            'password' => ['required', 'string', 'min:8']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $info = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::guard('admins')->attempt($info)) {
            $user = Admin::where('email', $request->email)->first();
            $token = JWTAuth::fromUser($user);
            $data = $user;
            $data['token'] = $token;
            return response()->json([
                'status' => true,
                'message' => trans('messages.login'),
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('messages.password_error'),
                'data' => []
            ], 401);
        }
    }
}
