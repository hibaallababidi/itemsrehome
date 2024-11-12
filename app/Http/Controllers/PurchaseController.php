<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\SuggestedPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function add_order_purchase(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [

            'product_id' => 'required|integer|exists:products,id',
            'items_count' => 'required|integer',
            'is_suggested' => 'required|bool',
            'price' => ['required_if:is_suggested,==,1', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);


        $add_order_purchase = Order::create([
            'product_id' => $request->product_id,
            'items_count' => $request->items_count,
            'is_suggested' => $request->is_suggested,
            'user_id' => $user->id,
            'order_status' => 0,
        ]);

        if ($request->is_suggested == 1) {
            $add_suggested_prices = SuggestedPrice::create([
                'price' => $request->price,
                'order_id' => $add_order_purchase->id,

            ]);
        }

        $title = $user->first_name . ' sent you a purchase request';
        $body = [
            'product_id' => $request->product_id,
            'user_id' => $user->id,
        ];
        $notification = [
            "title" => $title,
            "body" => $body
        ];
        $device_token = DeviceToken::where('user_id', $user->id)->first();
        $device_token = $device_token->device_token;
        Notification::create([
            'user_id' => $user->id,
            'body' => json_encode($notification)
        ]);
        (new NotificationController)
            ->sendNotification($device_token, $body, $title);

        return response()->json([
            'status' => true,
            'message' => trans('messages.add_order'),
            'data' => [],

        ], 201);


    }

    public function display_my_order_purchase(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $user = Auth::user();
        $orders = Product::
        join('orders', 'orders.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.user_id', $user->id)
            ->paginate($request->per_page, [
                'orders.id As order_id',
                'products.id As product_id',
                'products.product_name',
                'orders.user_id As user_id',
                'users.first_name',
                'users.last_name',
                'products.is_free',
                'orders.is_suggested',
                'orders.items_count As order_items_count',
                'orders.created_at',
                'orders.order_status'
            ]);
        $controller = new DisplayOrdersController();
        $orders = $controller
            ->getALlPhotoAndPrices($orders);
        if (!Auth::guest())
            $orders = (new ProductController())->allIsSaved($orders, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.displayMyOrderPurchase'),
            'data' => $orders,
        ], 200);


    }

    public function delete_my_order_purchase(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->query(), [

            'order_id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $delete_my_order_purchase = Order::
        where('id', $request->query('order_id'))
            ->where('user_id', $user->id)
            ->where('order_status', 0)
            ->get()->first();
        if ($delete_my_order_purchase) {
            $delete_my_order_purchase->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.deleted'),
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('messages.deleted.error'),
                'data' => [],
            ], 200);
        }

    }

    public function display_order_purchase(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $orders = Product::join('orders', 'orders.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->paginate($request->per_page, [
                'orders.id As order_id',
                'products.id As product_id',
                'products.product_name',
                'orders.user_id As buyer_id',
                'products.seller_id As seller_id',
                'users.first_name As buyer_first_name',
                'users.last_name As buyer_last_name',
                'products.is_free',
                'orders.is_suggested',
            ]);
        // $orders = json_decode(json_encode($orders), true);
        foreach ($orders as $order) {
            $users = DB::table('users')
                ->join('products', 'products.seller_id', '=', 'users.id')
                ->where('products.seller_id', $order->seller_id)
                ->get(['users.first_name As seller_first_name',
                    'users.last_name As seller_last_name'])->first();
            $users = json_decode(json_encode($users), true);
            $order->seller_first_name = $users['seller_first_name'];
            $order->seller_last_name = $users['seller_last_name'];
        }
        $controller = new DisplayOrdersController();
        $orders = $this
            ->getPhotoAndPrices($orders);
        return response()->json([
            'status' => true,
            'message' => trans('messages.displayOrderPurchase'),
            'data' => $orders,
        ], 200);

    }

    public function getPhotoAndPrices($orders)
    {
        for ($i = 0; $i < sizeof($orders); $i++) {
            $photo = ProductPhoto::where('product_id', $orders[$i]['product_id'])->first();
            $orders[$i]['photo'] = $photo->photo;
            if (!$orders[$i]['is_free'] && !$orders[$i]['is_suggested']) {
                $price = Price::where('product_id', $orders[$i]['product_id'])
                    ->orderBy('created_at', 'DESC')->get('price')->first();
                $orders[$i]['price'] = $price->price;
            } else if (!$orders[$i]['is_free'] && $orders[$i]['is_suggested']) {
                $suggestedPrice = SuggestedPrice::where('order_id', $orders[$i]['order_id'])->get('price')->first();
                $orders[$i]['price'] = $suggestedPrice->price;
            } else if ($orders[$i]['is_free']) {
                $orders[$i]['price'] = 0;
            }
        }
        return $orders;
    }

    public function display_operation_Pending(Request $request)
    {
        $display_operation_Pending = DB::table('users')
            ->join('products', 'products.seller_id', '=', 'users.id')
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->where('orders.order_status', '=', 0)
            ->where('products.is_sold', '=', 0)
            ->get([
                'products.product_name as Product Name',
                'products.seller_id as Seller Id ',
                'orders.user_id as Buyer Id',
                'orders.created_at as date of rejection',
                'orders.order_status as Order Status',

            ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.display_operation_Pending'),
            'data' => $display_operation_Pending,
        ], 200);
    }

    public function display_operation_accepted(Request $request)
    {


        $display_operation_accepted = DB::table('users')
            ->join('products', 'products.seller_id', '=', 'users.id')
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->where('orders.order_status', '=', 1)
            ->where('products.is_sold', '=', 0)
            ->get([
                'products.product_name as Product Name',
                'products.seller_id as Seller Id ',
                'orders.user_id as Buyer Id',
                'orders.created_at as date of rejection',
                'orders.order_status as Order Status',

            ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.display_operation_accepted'),
            'data' => $display_operation_accepted,
        ], 200);


    }

    public function display_operation_completed(Request $request)
    {


        $display_operation_completed = DB::table('users')
            ->join('products', 'products.seller_id', '=', 'users.id')
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->where('orders.order_status', '=', 2)
            ->where('products.is_sold', '=', 1)
            ->get([
                'products.product_name as Product Name',
                'products.seller_id as Seller Id ',
                'orders.user_id as Buyer Id',
                'orders.created_at as date of rejection',
                'orders.order_status as Order Status',

            ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.display_operation_completed'),
            'data' => $display_operation_completed,
        ], 200);


    }

}

