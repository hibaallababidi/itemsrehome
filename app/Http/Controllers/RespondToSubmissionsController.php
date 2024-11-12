<?php /** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RespondToSubmissionsController extends Controller
{
    public function acceptSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $product_id = $request->product_id;
        $product = Product::find($product_id);
        $product->update([
            'product_status' => true
        ]);

        $product_name = $product->product_name;
        $title = "The Admin Accepted your Product $product_name";
        $body = [
            'product_id' => $product_id,
        ];
        $notification = [
            "title" => $title,
            "body" => $body
        ];
        $device_token = DeviceToken::where('user_id', $product->seller_id)->first();
        $device_token = $device_token->device_token;
        Notification::create([
            'user_id' => $product->seller_id,
            'body' => json_encode($notification)
        ]);
        (new NotificationController)->sendNotification($device_token, $body, $title);

        return response()->json([
            'status' => true,
            'message' => trans('messages.accept_submission'),
            'data' => []
        ]);
    }

    public function rejectSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $product_id = $request->product_id;
        $product = Product::find($product_id);
        $product_name = $product->product_name;

        $title = "The Admin Rejected your Product $product_name";
        $body = [
            'product_id' => $product_id,
        ];
        $notification = [
            "title" => $title,
            "body" => $body
        ];
        $device_token = DeviceToken::where('user_id', $product->seller_id)->first();
        $device_token = $device_token->device_token;
        Notification::create([
            'user_id' => $product->seller_id,
            'body' => json_encode($notification)
        ]);
        (new NotificationController)->sendNotification($device_token, $body, $title);

        return response()->json([
            'status' => true,
            'message' => trans('messages.reject_submission'),
            'data' => []
        ]);
    }
}
