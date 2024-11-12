<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\EmailVerificationController;

use App\Models\User;
use App\Models\UserSocialLink;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use function PHPUnit\Framework\isEmpty;

class ProfileController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }

    function getUserSocialLinks($user)
    {
        $social_links = UserSocialLink::where('user_id', $user['user_id'])
            ->get(['type', 'link']);
        if (!isset($social_links))
            $user['social_links'] = null;
        else {
            $user['social_links']['telegram'] = null;
            $user['social_links']['facebook'] = null;
            foreach ($social_links as $link) {
                if ($link['type'] == 0) {
                    $user['social_links']['telegram'] = $link['link'];
                } else {
                    $user['social_links']['facebook'] = $link['link'];
                }
            }
        }
        return $user;
    }

    public function DisplayMyProfile()
    {
        $user = Auth::user();
        $displayMyProfile = DB::table('users')
            ->where('users.id', $user->id)
            ->get([
                'users.id As user_id',
                'users.first_name',
                'users.last_name',
                'users.photo',
                'users.phone_number',
                'users.email',
                'users.location_id'
            ])->first();
        $displayMyProfile = json_decode(json_encode($displayMyProfile), true);
        if ($displayMyProfile['location_id'] != null) {
            $displayMylocation = DB::table('locations')
                ->join('cities', 'cities.id', '=', 'locations.city_id')
                ->where('locations.id', $displayMyProfile['location_id'])
                ->get(['cities.city_name',
                    'locations.location_name',])->first();
            $displayMylocation = json_decode(json_encode($displayMylocation), true);
            $displayMyProfile['location_name'] = $displayMylocation['location_name'];
            $displayMyProfile['city_name'] = $displayMylocation['city_name'];
        } else {
            $displayMyProfile['location_name'] = Null;
            $displayMyProfile['city_name'] = Null;
        }

        $displayMyProfile = $this->getUserSocialLinks($displayMyProfile);
        return response()->json([
            'status' => true,
            'message' => trans('messages.displayMyProfile'),
            'data' => $displayMyProfile,
        ], 200);
    }

    public function DisplayProfile(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'user_id' => 'integer|exists:users,id'
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $displayProfile = DB::table('users')
            ->where('users.id', $request->query('user_id'))
            ->get([
                'users.id As user_id',
                'users.first_name',
                'users.last_name',
                'users.photo',
                'users.phone_number',
                'users.email',
                'users.location_id'
            ])->first();
        $displayProfile = json_decode(json_encode($displayProfile), true);
        if ($displayProfile['location_id'] != Null) {
            $displayMylocation = DB::table('locations')
                ->join('cities', 'cities.id', '=', 'locations.city_id')
                ->where('locations.id', $displayProfile['location_id'])
                ->get(['cities.city_name',
                    'locations.location_name',])->first();
            $displayMylocation = json_decode(json_encode($displayMylocation), true);
            $displayProfile['location_name'] = $displayMylocation['location_name'];
            $displayProfile['city_name'] = $displayMylocation['city_name'];
        } else {
            $displayProfile['location_name'] = Null;
            $displayProfile['city_name'] = Null;
        }
        $displayProfile = $this->getUserSocialLinks($displayProfile);
        return response()->json([
            'status' => true,
            'message' => trans('messages.success'),
            'data' => $displayProfile,
        ], 200);
    }

    public function updated_profile(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'first_name' => ['string'],
            'last_name' => ['string'],
            'phone_number' => ['string'],
            'location_id' => 'integer|exists:locations,id',
            'photo' => 'image',

            'social_links' => ['array'],
            'social_links.*' => ['array'],


        ]);

        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $profile = User::where('id', $user->id)->first();
        if ($request->has('first_name')) {
            $profile->update([
                'first_name' => $request->first_name,
            ]);
        }

        if ($request->has('last_name')) {
            $profile->update([
                'last_name' => $request->last_name,
            ]);
        }


        if ($request->has('phone_number')) {
            $profile->update([
                'phone_number' => $request->phone_number,
            ]);
        }

        if ($request->has('location_id')) {
            $profile->update([
                'location_id' => $request->location_id,
            ]);
        }


        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');
            $extension = $photo->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $photo->move("photos/", $filename);
            $profile->update([
//                'photo' => URL::to("/photos/$filename"),
                'photo' => '/photos/' . $filename
            ]);
        }

        if ($request->has('social_links')) {
            $social_links = $request->social_links;
            for ($i = 0; $i < sizeof($social_links); $i++) {
                $user_social_link = UserSocialLink::where('user_id', $user->id)
                    ->where('type', $social_links[$i]['type'])
                    ->first();
                if ($user_social_link) {
                    if ($user_social_link['type'] == 0) {
                        if ($social_links[$i]['link'][0] == '@')
                            $social_links[$i]['link'] =
                                Str::replaceFirst('@', 'https://t.me/', $social_links[$i]['link']);
                    }
                    $user_social_link->update([
                        'link' => $social_links[$i]['link']
                    ]);
                } else {
                    if ($social_links[$i]['type'] == 0) {
                        if ($social_links[$i]['link'][0] == '@')
                            $social_links[$i]['link'] =
                                Str::replaceFirst('@', 'https://t.me/', $social_links[$i]['link']);
                    }

                    $add_UserSocialLink = UserSocialLink::create([
                        'user_id' => $user->id,
                        'type' => $social_links[$i]['type'],
                        'link' => $social_links[$i]['link'],
                    ]);

                }


            }
        }
        return response()->json([
            'status' => true,
            'message' => trans('messages.update_profile'),
            'data' => [],
        ], 201);
    }

    public function updated_email(Request $request)
    {
        $user = Auth::user();
        $userEmail = $user->email;
        $validator = Validator::make($request->all(), [
            'new_email' => ['email'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user->update([
            'email' => $request->email,
        ]);

        $user->notify(new EmailVerificationNotification());
        $user->update([
            'email' => $userEmail,
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.message'),
            'data' => [],
        ], 200);

    }

    public function verifyEmailUpdate(Request $request)
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
        $user = Auth::user();

        $user->update([
            'email' => $request->email,
            'email_verified_at' => now()
        ]);
        $token = JWTAuth::fromUser($user);
        $data = $user;
        $data['token'] = $token;
        return response()->json([
            'status' => true,
            'message' => trans('messages.email_verified'),
            'data' => $data,
        ], 200);


    }

}


