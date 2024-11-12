<?php /** @noinspection ALL */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class EmailVerificationController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
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
                'message' => $otp->message,//trans('messages.code_error'),
                'data' => []
            ], 401);
        $user = User::where('email', $request->email)->first();
        $user->update(['email_verified_at' => now()]);
        $token = JWTAuth::fromUser($user);
        $data = $user;
        $data['token'] = $token;
        return response()->json([
            'status' => true,
            'message' => trans('messages.email_verified'),
            'data' => $data,
        ], 200);
    }

    public function resendVerificationCode(Request $request)
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
        User::where('email', $request->email)->first()
            ->notify(new EmailVerificationNotification());
        return response()->json([
            'status' => true,
            'message' => trans('messages.email_sent'),
            'data' => [],
        ], 200);
    }
}
