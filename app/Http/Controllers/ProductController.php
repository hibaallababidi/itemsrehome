<?php /** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\ProductSocialLink;
use App\Models\Save;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function displayProducts(Request $request)
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
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->orderByDesc('created_at')
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_deliverable'
            ]);
        $products = $this->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = $this->allIsSaved($products, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.products'),
            'data' => $products
        ]);

    }

    public function displayFreeProducts(Request $request)
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
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('is_free', true)
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_deliverable'
            ]);
        $products = $this->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = $this->allIsSaved($products, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.free_products'),
            'data' => $products
        ]);
    }

    public function displayProductsByLocation(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'string'],
            'location_id' => ['required', 'int', 'exists:locations,id']
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
            ->where('location_id', true)
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_deliverable'
            ]);
        $products = $this->getPhotosAndPrices($products);
        if (!Auth::guest())
            $this->allIsSaved($products, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.free_products'),
            'data' => $products
        ]);

    }

    public function displayProductDetails(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'product_id' => ['required', 'integer', 'exists:products,id']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $product = $this->getProduct($request->query('product_id'));
        $photos = ProductPhoto::where('product_id', $request->query('product_id'))->pluck('photo');
        $product['photos'] = $photos;
        $product = $this->getSocialLinks($product);
        $product = $this->getPrices($product);
        if (!Auth::guest())
            $product = $this->isSaved($product, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.product_details'),
            'data' => $product
        ]);
    }

    public function displayUserProducts(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $product = Product::find($request->query('product_id'));
        $products = Product::where('seller_id', $product->seller_id)
            ->whereNotIn('id', [$product->id])
            ->where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_deliverable'
            ]);
        $products = $this->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = $this->allIsSaved($products, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.user_products'),
            'data' => $products
        ]);
    }

    public function displayUserProductsForAdmin(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $products = Product::where('seller_id', $request->user_id)
            ->where('product_status', 1)
            ->where('items_count', '>', 0)
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_sold',
                'is_deliverable'
            ]);
        $products = $this->getPhotosAndPrices($products);
        return response()->json([
            'status' => true,
            'message' => trans('messages.user_products'),
            'data' => $products
        ]);
    }

    public function displayMyProducts(Request $request)
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
        $products = Product::where('seller_id', Auth::user()->id)
            ->where('items_count', '>', 0)
            ->paginate($request->per_page, [
                'id As product_id',
                'product_name',
                'is_free',
                'is_deliverable',
                'is_sold',
                'product_status'
            ]);
        $products = $this->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = $this->allIsSaved($products, Auth::id());
        return response()->json([
            'status' => true,
            'message' => trans('messages.my_products'),
            'data' => $products
        ]);
    }

    /*
     * Functions to help
     */

    public function getPhotosAndPrices($products)
    {
        for ($i = 0; $i < sizeof($products); $i++) {
            $photo = ProductPhoto::where('product_id', $products[$i]['product_id'])->first();
            $products[$i]['photo'] = $photo->photo;
            if (!$products[$i]['is_free']) {
                $prices = Price::where('product_id', $products[$i]['product_id'])->pluck('price');
                if (sizeof($prices) > 1) {
                    $products[$i]['discount'] = true;
                    $old_price = $prices[sizeof($prices) - 2];
                    $new_price = $prices[sizeof($prices) - 1];
                    $products[$i]['old_price'] = $old_price;
                    $products[$i]['new_price'] = $new_price;
                } else {
                    $products[$i]['old_price'] = null;
                    $products[$i]['new_price'] = $prices[0];
                    $products[$i]['discount'] = false;
                }
            } else {
                $products[$i]['old_price'] = null;
                $products[$i]['new_price'] = 0;
                $products[$i]['discount'] = false;
            }
        }
        return $products;
    }

    private function getProduct($product_id)
    {
        $product = DB::table('products')
            ->join('users', 'products.seller_id', '=', 'users.id')
            ->join('locations', 'products.location_id', '=', 'locations.id')
            ->join('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->where('products.id', $product_id)
            ->get($this->productDataToGet())->first();
        return json_decode(json_encode($product), true);
    }

    private function getSocialLinks($product)
    {
        $social_links = ProductSocialLink::where('product_id', $product['product_id'])
            ->get(['type', 'link']);
        $product['social_links']['telegram'] = null;
        $product['social_links']['facebook'] = null;
        foreach ($social_links as $link) {
            if ($link['type'] == 0) {
                $product['social_links']['telegram'] = $link['link'];
            } else {
                $product['social_links']['facebook'] = $link['link'];
            }
        }
        return $product;
    }

    public function getPrices($product)
    {
        if (!$product['is_free']) {
            $prices = Price::where('product_id', $product['product_id'])->pluck('price');
            if (sizeof($prices) > 1) {
                $product['discount'] = true;
                $old_price = $prices[sizeof($prices) - 2];
                $new_price = $prices[sizeof($prices) - 1];
                $product['old_price'] = $old_price;
                $product['new_price'] = $new_price;
            } else {
                $product['old_price'] = null;
                $product['new_price'] = $prices[0];
                $product['discount'] = false;
            }
        } else {
            $product['old_price'] = null;
            $product['new_price'] = 0;
            $product['discount'] = false;
        }

        return $product;
    }

    private function productDataToGet()
    {
        return [
            'products.id As product_id',
            'products.product_name',
            'products.description',
            'products.duration_of_use',
            'products.phone_number As phone_number',
            'products.items_count',
            'products.is_free',
            'products.is_deliverable',
            'products.price_suggestion',
            'users.id As seller_id',
            'users.first_name',
            'users.last_name',
            'locations.location_name As location',
            'categories.category_name As category',
            'sub_categories.sub_category_name As sub_category'
        ];
    }

    public function allIsSaved($products, $user_id)
    {
        for ($i = 0; $i < sizeof($products); $i++) {
            $saved = Save::where('user_id', $user_id)
                ->where('product_id', $products[$i]['product_id'])
                ->exists();
            if ($saved)
                $products[$i]['saved'] = true;
            else
                $products[$i]['saved'] = false;
        }
        return $products;
    }

    public function isSaved($product, $user_id)
    {
        $saved = Save::where('user_id', $user_id)
            ->where('product_id', $product['product_id'])
            ->exists();
        if ($saved)
            $product['saved'] = true;
        else
            $product['saved'] = false;

        return $product;
    }

}
