<?php /** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function searchProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => ['required', 'string', 'min:1'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('product_name', 'LIKE', '%' . $request->product_name . '%')
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_deliverable'
            ]);
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (sizeof($products) != 0) {
            if (!Auth::guest())
                $products = (new ProductController())->allIsSaved($products, Auth::id());
            return response()->json([
                'status' => true,
                'message' => trans('messages.products'),
                'data' => $products
            ]);
        } else
            return response()->json([
                'status' => false,
                'message' => trans('messages.search_fail'),
                'data' => $products
            ]);

    }

    public function searchUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => ['required', 'string', 'min:1'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $users = User::where('first_name', 'LIKE', '%' . $request->user_name . '%')
            ->orWhere('last_name', 'LIKE', '%' . $request->user_name . '%')
            ->paginate($request->per_page, [
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone_number',
            ]);
        $users = $this->getUserStatus($users);

        if (sizeof($users) != 0)
            return response()->json([
                'status' => true,
                'message' => trans('messages.users'),
                'data' => $users,
            ], 200);
        else
            return response()->json([
                'status' => false,
                'message' => trans('messages.search_fail'),
                'data' => $users
            ]);
    }

    private function getUserStatus($users)
    {
        for ($i = 0; $i < sizeof($users); $i++) {
            $block = DB::table('blocks')
                ->where('user_id', $users[$i]['id'])
                ->get()->last();
            if ($block)
                $users[$i]['status'] = $block->status;
            else
                $users[$i]['status'] = 0;
        }
        return $users;
    }
}
