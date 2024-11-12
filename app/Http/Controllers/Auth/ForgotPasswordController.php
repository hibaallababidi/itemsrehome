<?php /** @noinspection PhpUndefinedFunctionInspection */
/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpPureAttributeCanBeAddedInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ForgotPasswordController extends Controller
{
    private Otp $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }

    public function userForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user = User::where('email', $request->email)->first();
        $user->notify(new ForgotPasswordNotification());
        return response()->json([
            'status' => true,
            'message' => trans('messages.verify_email'),
            'data' => []
        ]);
    }

    public function adminForgotPassword()
    {
        $user = Admin::find(1);
        $user->notify(new ForgotPasswordNotification());
        return response()->json([
            'status' => true,
            'message' => trans('messages.verify_email'),
            'data' => []
        ]);
    }

    public function verifyUserPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'min:6', 'max:6']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $otp = $this->otp->validate($request->email, $request->code);
        if (!$otp->status)
            return response()->json([
                'status' => $otp->status,
                'message' => trans('messages.code_error'),
                'data' => []
            ], 401);
        return response()->json([
            'status' => true,
            'message' => trans('messages.email_verified'),
            'data' => []
        ]);
    }

    public function verifyAdminPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'min:6', 'max:6']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $admin = Admin::find(1);
        $otp = $this->otp->validate($admin->email, $request->code);
        if (!$otp->status)
            return response()->json([
                'status' => $otp->status,
                'message' => trans('messages.code_error'),
                'data' => []
            ], 401);
        return response()->json([
            'status' => true,
            'message' => trans('messages.email_verified'),
            'data' => []
        ]);
    }

    public function adminUpdatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'confirmed', 'min:8']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user = Admin::find(1);
        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete();
        $token = JWTAuth::fromUser($user);
        $data = $user;
        $data['token'] = $token;
        return response()->json([
            'status' => true,
            'message' => trans('messages.password_updated'),
            'data' => $data,
        ]);
    }

    public function userUpdatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete();
        $token = JWTAuth::fromUser($user);
        $data = $user;
        $data['token'] = $token;
        return response()->json([
            'status' => true,
            'message' => trans('messages.password_updated'),
            'data' => $data,
        ]);
    }
}
