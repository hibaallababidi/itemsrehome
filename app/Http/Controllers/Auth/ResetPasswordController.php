<?php /** @noinspection ALL */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'confirmed', 'string', 'min:8']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user = Auth::user();
        if (Hash::check($request->current_password, $user->getAuthPassword())) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            $token = JWTAuth::fromUser($user);
            $data = $user;
            $data['token'] = $token;
            return response()->json([
                'status' => true,
                'message' => trans('messages.password_updated'),
                'data' => $data,
            ], 200);
        } else
            return response()->json([
                'status' => false,
                'message' => trans('messages.password_error'),
                'data' => []
            ], 400);

    }


}
