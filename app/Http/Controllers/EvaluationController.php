<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function League\Flysystem\get;

class EvaluationController extends Controller
{
    public function evaluation(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [

            'evaluated_id' => 'required|integer|exists:users,id',
            'evaluation_number' => 'required|integer',

        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $products = DB::table('products')
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->where('products.seller_id', $request->evaluated_id)
            ->where('orders.user_id', $user->id)
            ->where('products.is_sold', 1)
            ->where('orders.order_status', 2)
            ->get()->first();

        if ($products) {
            $add_Evaluation = Evaluation::create([
                'evaluator_id' => $user->id,
                'evaluated_id' => $request->evaluated_id,
                'evaluation_number' => $request->evaluation_number,
            ]);
            return response()->json([
                'status' => true,
                'message' => trans('messages.add_evaluation'),
                'data' => [],
            ], 201);
        } else {
            return response()->json([
                'status' => true,
                'message' => trans('messages.errorAdd'),
                'data' => [],
            ], 201);
        }


    }


}
