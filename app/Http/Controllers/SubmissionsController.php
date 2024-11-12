<?php /** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\ProductSocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubmissionsController extends Controller
{
    public function submitProduct(Request $request)
    {
        $validator = Validator::make($request->all(), $this->submitProductRules());
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $product = $this->storeProduct($request, Auth::user()->id);
        $this->storeProductPhotos($request, $product);
        $this->storeProductSocialLinks($request, $product);
        $this->storeProductPrice($request, $product);
        return response()->json([
            'status' => true,
            'message' => trans('messages.submit_product'),
            'data' => []
        ], 201);
    }

    public function getSubmissionsCount()
    {
        $count = Product::where('product_status', false)->get()->count();
        return response()->json([
            'status' => true,
            'message' => trans('messages.display_submissions'),
            'data' => $count
        ]);
    }

    public function displaySubmissions(Request $request)
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
        $products = Product::join('users', 'products.seller_id', '=', 'users.id')
            ->orderByDesc('created_at')
            ->paginate($request->per_page, $this->submissionsDataToGet());
        if ($products) {
            $controller = new ProductController();
            $products = $controller->getPhotosAndPrices($products);
        }
        return response()->json([
            'status' => true,
            'message' => trans('messages.display_submissions'),
            'data' => $products
        ]);
    }

    public function displaySubmissionDetails($product_id)
    {

    }

    /*
     * Functions to help
     */

    private function submissionsDataToGet()
    {
        return [
            'products.id As product_id',
            'products.product_name',
            'products.is_free',
            'products.seller_id',
            'users.first_name',
            'users.last_name',
            'products.created_at'
        ];
    }

    private function submitProductRules()
    {
        return [
            'phone_number' => ['required', 'string', 'min:10'],
            'product_name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'duration_of_use' => ['required', 'string'],
            'items_count' => ['required', 'int'],
            'is_free' => ['required', 'bool'],
            'is_deliverable' => ['required', 'bool'],
            'price_suggestion' => ['required', 'bool'],
            'location_id' => ['required', 'int', 'exists:locations,id'],
            'sub_category_id' => ['required', 'int', 'exists:sub_categories,id'],
            'photos' => ['required', 'array'],
            'social_links' => ['required', 'array'],

            'photos.*' => ['image'],
            'social_links.*' => ['array'],

            'price' => ['required_if:is_free,==,0', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/']
        ];
    }

    private function storeProduct(Request $request, $id)
    {
        return Product::create([
            'seller_id' => $id,
            'sub_category_id' => $request->sub_category_id,
            'location_id' => $request->location_id,
            'product_name' => $request->product_name,
            'description' => $request->description,
            'duration_of_use' => $request->duration_of_use,
            'phone_number' => $request->phone_number,
            'product_status' => false,
            'items_count' => $request->items_count,
            'is_free' => $request->is_free,
            'is_deliverable' => $request->is_deliverable,
            'price_suggestion' => $request->price_suggestion,
        ]);
    }

    public function storeProductPhotos(Request $request, $product)
    {
        if ($request->photos != null) {
            $photos = $request->photos;
            for ($i = 0; $i < sizeof($photos); $i++) {
                $this->storePhoto($photos[$i], $product->id);
            }
        }
    }

    public function storePhoto($photo, $product_id)
    {
        $extension = $photo->getClientOriginalExtension();
//        $filename = time() . '.' . $extension;
        $filename = $this->milliseconds() . '.' . $extension;
        $photo->move("photos/", $filename);
        ProductPhoto::create([
            'product_id' => $product_id,
//            'photo' => URL::to("/photos/$filename"),
            'photo' => '/photos/' . $filename
        ]);
    }

    public function storeProductSocialLinks(Request $request, $product)
    {
        if ($request->social_links != null) {
            $social_links = $request->social_links;
            for ($i = 0; $i < sizeof($social_links); $i++) {
                $this->storeSocialLink($social_links[$i], $product->id);
            }
        }
    }

    public function storeSocialLink($social_link, $product_id)
    {
        if ($social_link['type'] == 0) {
            if ($social_link['link'][0] == '@')
                $social_link['link'] = Str::replaceFirst('@', 'https://t.me/', $social_link['link']);
        }
        ProductSocialLink::create([
            'product_id' => $product_id,
            'type' => $social_link['type'],
            'link' => $social_link['link']
        ]);
    }

    public function storeProductPrice(Request $request, $product)
    {
        if ($request->is_free == 0 && $request->has('price')) {
            Price::create([
                'product_id' => $product->id,
                'price' => $request->price
            ]);
        }
    }

    function milliseconds()
    {
        $mt = explode(' ', microtime());
        return intval($mt[1] * 1E3) + intval(round($mt[0] * 1E3));
    }
}
