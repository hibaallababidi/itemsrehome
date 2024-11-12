<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Save;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SavedController extends Controller
{
    public function add_saved_product(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [

            'product_id' => 'required|integer|exists:products,id',

        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $add_saved_product = Save::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.add_saved_product'),
            'data' => [],
        ], 201);


    }

    public function delete_my_saved_product(Request $request)
    {

        $user = Auth::user();
        $validator = Validator::make($request->query(), [

            'product_id' => 'required|integer|exists:products,id',
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $delete_my_saved_product = DB::table('saves')
            ->where('product_id', $request->query('product_id'))
            ->where('user_id', $user->id)
            ->get()->first();
        if ($delete_my_saved_product) {
            $delete_my_saved_product->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.deleted_saved'),
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('messages.error_delete'),
                'data' => [],
            ], 200);
        }


    }

    public function show_saved_product(Request $request)
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

        $user = Auth::user();
        $show_my_saved_product = Product::join('saves', 'products.id', '=', 'saves.product_id')
            ->where('saves.user_id', $user->id)
            ->paginate($request->per_page, [
                'products.id As product_id',
                'products.product_name',
                'products.is_deliverable',
                'products.is_free'
            ]);

        $controller = new ProductController();
        $show_my_saved_product = $controller
            ->getPhotosAndPrices($show_my_saved_product);
        return response()->json([
            'status' => true,
            'message' => trans('messages.show_saved_product'),
            'data' => $show_my_saved_product,
        ]);
    }


}
