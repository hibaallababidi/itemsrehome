<?php /** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Price;
use App\Models\ProductPhoto;
use App\Models\SuggestedPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisplayOrdersController extends Controller
{
    public function displayPendingOrders(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $orders = Order::join('products', 'orders.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('products.seller_id', Auth::user()->id)
            ->where('products.is_sold', false)
            ->where('order_status', 0)
            ->paginate($request->per_page, $this->ordersDataToGet());
        $orders = $this->getALlPhotoAndPrices($orders);
        return response()->json([
            'status' => true,
            'message' => trans('messages.pending_orders'),
            'data' => $orders
        ]);
    }

    public function displayAcceptedOrders(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $orders = Order::join('products', 'orders.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('products.seller_id', Auth::user()->id)
            ->where('products.is_sold', false)
            ->where('order_status', 1)
            ->paginate($request->per_page, $this->ordersDataToGet());
        $orders = $this->getALlPhotoAndPrices($orders);
        return response()->json([
            'status' => true,
            'message' => trans('messages.accepted_orders'),
            'data' => $orders
        ]);
    }

    public function displayCompletedOrders(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $orders = Order::join('products', 'orders.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('products.seller_id', Auth::user()->id)
            ->where('products.is_sold', true)
            ->where('order_status', 1)
            ->paginate($request->per_page, $this->ordersDataToGet());
        $orders = $this->getALlPhotoAndPrices($orders);
        return response()->json([
            'status' => true,
            'message' => trans('messages.completed_orders'),
            'data' => $orders
        ]);
    }

    public function displayOrderDetails(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $order_id = $request->query('order_id');
        $order = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.id', $order_id)
            ->get($this->orderDataToGet())->first();
        $order = $this->getPhotoAndPrices(json_decode(json_encode($order), true));
        return response()->json([
            'status' => true,
            'message' => trans('messages.order_details'),
            'data' => $order
        ]);
    }

    /*
     * Functions to help
     */

    public function getALlPhotoAndPrices($orders)
    {
        for ($i = 0; $i < sizeof($orders); $i++) {
            $photo = ProductPhoto::where('product_id', $orders[$i]['product_id'])->first();
            $orders[$i]['photo'] = $photo->photo;
            if (!$orders[$i]['is_free']) {
                $price = Price::where('product_id', $orders[$i]['product_id'])
                    ->orderBy('created_at', 'DESC')->get('price')->first();
                $orders[$i]['original_price'] = $price->price;
            } else
                $orders[$i]['original_price'] = 0;
            if ($orders[$i]['is_suggested']) {
                $suggestedPrice = SuggestedPrice::where('order_id', $orders[$i]['order_id'])->get('price')->first();
                $orders[$i]['suggested_price'] = $suggestedPrice->price;
            } else if (!$orders[$i]['is_free']) {
                $suggestedPrice = Price::where('product_id', $orders[$i]['product_id'])
                    ->orderBy('created_at', 'DESC')->get('price')->first();
                $orders[$i]['suggested_price'] = $suggestedPrice->price;
            } else
                $orders[$i]['suggested_price'] = 0;

        }
        return $orders;
    }

    public function getPhotoAndPrices($order)
    {
        $photo = ProductPhoto::where('product_id', $order['product_id'])->first();
        $order['photo'] = $photo->photo;
        if (!$order['is_free']) {
            $price = Price::where('product_id', $order['product_id'])
                ->orderBy('created_at', 'DESC')->get('price')->first();
            $order['original_price'] = $price->price;
        } else
            $order['original_price'] = 0;
        if ($order['is_suggested']) {
            $suggestedPrice = SuggestedPrice::where('order_id', $order['order_id'])->get('price')->first();
            $order['suggested_price'] = $suggestedPrice->price;
        } else if (!$order['is_free']) {
            $suggestedPrice = Price::where('product_id', $order['product_id'])
                ->orderBy('created_at', 'DESC')->get('price')->first();
            $order['suggested_price'] = $suggestedPrice->price;
        } else
            $order['suggested_price'] = 0;
        return $order;
    }

    private function ordersDataToGet()
    {
        return [
            'orders.id As order_id',
            'products.id As product_id',
            'orders.user_id As user_id',
            'users.first_name',
            'users.last_name',
            'products.is_free',
            'orders.is_suggested',
        ];
    }

    private function orderDataToGet()
    {
        return [
            'orders.id As order_id',
            'products.id As product_id',
            'orders.user_id As user_id',
            'users.first_name',
            'users.last_name',
            'products.is_free',
            'orders.is_suggested',
            'orders.items_count',
            'orders.created_at'
        ];
    }
}
