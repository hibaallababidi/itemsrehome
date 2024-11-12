<?php /** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    /** @noinspection PhpComposerExtensionStubsInspection */
    public function sendNotification($device_key, $body, $title)
    {
        $SERVER_API_KEY = 'AAAAA3KyMDE:APA91bGnxtUTNQXi6zJbMIYn3ItJgB2NQ87JNnTEj8_XIfS0d3dv80srcconO8zFUV4COCVCM0lcR_1cCLCXHpLo111jSTD3Ub8HR-4NrQRV9ohHHAbp2nGZtpGPWAX73w9sPJ8Iw2RH';
        $URL = 'https://fcm.googleapis.com/fcm/send';
        $post_data = [
            "registration_ids" => [$device_key],
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default"
            ]
        ];
        $post_data = json_encode($post_data);

        $crl = curl_init();

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-type: application/json'
        ];

        $h2 = curl_setopt($crl, CURLOPT_URL, $URL);
        $h4 = curl_setopt($crl, CURLOPT_POST, true);
        $h3 = curl_setopt($crl, CURLOPT_HTTPHEADER, $headers);
        $h1 = curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        $h6 = curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $h5 = curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        $ex = curl_exec($crl);
        return $ex;
    }

    public function getNotificationCount()
    {
        $count = Notification::where('user_id', Auth::id())->get()->count();
        return response()->json([
            'status' => true,
            'message' => trans('messages.notification_count'),
            'data' => $count
        ]);
    }

    public function notificationList(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => 'fail',
                'data' => $validator->errors()
            ], 400);
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate($request->per_page, [
                'id',
                'body As notification',
                'created_at'
            ]);

        return response()->json([
            'status' => true,
            'message' => trans('messages.notifications'),
            'data' => $notifications
        ]);
    }

//    public function sendNotification($device_key, $body, $title)
//    {
//        $SERVER_API_KEY = 'AAAAA3KyMDE:APA91bGnxtUTNQXi6zJbMIYn3ItJgB2NQ87JNnTEj8_XIfS0d3dv80srcconO8zFUV4COCVCM0lcR_1cCLCXHpLo111jSTD3Ub8HR-4NrQRV9ohHHAbp2nGZtpGPWAX73w9sPJ8Iw2RH';
//        $URL = 'https://fcm.googleapis.com/fcm/send';
//        $post_data = '{
//            "to" : "' . $device_key . '",
//            "notification" : {
//                 "body" : "' . $body . '",
//                 "title" : "' . $title . '"
//                },
//          }';
//
//        //
//        $post_data=json_encode($post_data);
//        //
//        $crl = curl_init();
//
//        $header = array();
//        $header[] = 'Content-type: application/json';
//        $header[] = 'Authorization: key=' . $SERVER_API_KEY;
//        $h1 = curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
//
//        $h2 = curl_setopt($crl, CURLOPT_URL, $URL);
//        $h3 = curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
//
//        $h4 = curl_setopt($crl, CURLOPT_POST, true);
//        $h5 = curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
//        $h6 = curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
//
//        $ex = curl_exec($crl);
//        return $ex;
////        return [
////            $crl,
////            $h1,
////            $h2,
////            $h3,
////            $h4,
////            $h5,
////            $h6,
////            $ex
////        ];
//
//    }
}
