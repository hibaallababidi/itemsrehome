<?php /** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RespondToOrdersController extends Controller
{
    public function acceptOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $order->update([
            'order_status' => 1
        ]);

        $title = 'Your Order has been Accepted';
        $body = [
            'order_id' => $order_id,
        ];
        $notification = [
            "title" => $title,
            "body" => $body
        ];
        $device_token = DeviceToken::where('user_id', $order->user_id)->first();
        $device_token = $device_token->device_token;
        Notification::create([
            'user_id' => $order->user_id,
            'body' => json_encode($notification)
        ]);
        (new NotificationController)->sendNotification($device_token, $body, $title);

        return response()->json([
            'status' => true,
            'message' => trans('messages.accept_order'),
            'data' => []
        ]);
    }

//    public function rejectOrder(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'order_id' => ['required', 'exists:orders,id'],
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'message' => trans('messages.validation'),
//                'data' => $validator->errors()
//            ], 400);
//        $order_id = $request->order_id;
//        $order = Order::find($order_id);
//        $order->update([
//            'order_status' => 3
//        ]);
//
//        return response()->json([
//            'status' => true,
//            'message' => trans('messages.reject_order'),
//            'data' => $validator->errors()
//        ], 400);
//    }

    public function rejectOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $order->update([
            'order_status' => 3
        ]);

        $title = 'Your Order has been Rejected';
        $body = [
            'order_id' => $order_id,
        ];
        $notification = [
            "title" => $title,
            "body" => $body
        ];
        $device_token = DeviceToken::where('user_id', $order->user_id)->first();
        $device_token = $device_token->device_token;
        Notification::create([
            'user_id' => $order->user_id,
            'body' => json_encode($notification)
        ]);
        (new NotificationController)->sendNotification($device_token, $body, $title);

        return response()->json([
            'status' => true,
            'message' => trans('messages.reject_order'),
            'data' => $validator->errors()
        ], 400);
    }

    public function completeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $order->update([
            'order_status' => 2
        ]);
        $itemsCount = $order->items_count;
        $product = Product::find($order->product_id);
        $product->decrement('items_count', $itemsCount);
        if ($product->items_count < 1)
            $product->update(['is_sold' => true]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.complete_order'),
            'data' => []
        ]);
    }
}
