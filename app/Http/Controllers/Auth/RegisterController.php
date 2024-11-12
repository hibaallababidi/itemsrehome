<?php /** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
//            'device_token' => ['required', 'string']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user = User::where('email', $request->email)->first();
        if (isset($user) && !is_null($user->email_verified_at)) {
            return response()->json([
                'status' => false,
                'message' => trans('messages.email_taken'),
                'data' => []
            ], 400);
        } elseif (isset($user) && is_null($user->email_verified_at))
            $user->delete();
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
//        DeviceToken::create([
//            'user_id' => $user->id,
//            'device_token' => $request->device_token
//        ]);
        $user->notify(new EmailVerificationNotification());
        return response()->json([
            'status' => true,
            'message' => trans('messages.register'),
            'data' => []
        ], 201);
    }
}
