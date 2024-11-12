<?php /** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EditProductController extends Controller
{
    /*
     * 'phone_number','product_name','description','duration_of_use',
       'items_count','is_free','is_deliverable','price_suggestion',
       'location_id','sub_category_id','photos','social_links','price'
     */
    public function editProduct(Request $request)
    {
        $validator = Validator::make($request->all(), $this->editProductRules());
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $product = Product::find($request->product_id);

        $editData = $this->getEditedData($request, $product);
        $product = $this->updateProduct($product, $editData);

        $this->editProductSocialLinks($request, $product->id);

        $controller = new SubmissionsController();
        $controller->storeProductPhotos($request, $product);

        $controller->storeProductPrice($request, $product);

        return response()->json([
            'status' => true,
            'message' => trans('messages.edit_product'),
            'data' => [$product]
        ]);
    }

    public function editProductSocialLinks(Request $request, $product_id)
    {
        if ($request->has('social_links')) {
            $social_links = $request->social_links;
            for ($i = 0; $i < sizeof($social_links); $i++) {
                $this->updateSocialLink($social_links[$i], $product_id);
            }
        }
    }

    /*
     * Functions To Help
     */

    private function editProductRules()
    {
        return [
            'product_id' => ['required', 'int', 'exists:products,id'],
            'phone_number' => ['string', 'min:10'],
            'product_name' => ['string'],
            'description' => ['string'],
            'duration_of_use' => ['string'],
            'items_count' => ['int'],
            'is_free' => ['bool'],
            'is_deliverable' => ['bool'],
            'price_suggestion' => ['bool'],
            'location_id' => ['int', 'exists:locations,id'],
            'sub_category_id' => ['int', 'exists:sub_categories,id'],
            'photos' => ['array'],
            'social_links' => ['array'],
            'price' => ['required_if:is_free,==,0', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],

            'photos.*' => ['image'],
            'social_links.*' => ['array'],
        ];
    }

    private function getEditedData(Request $request, $product)
    {
        $editData = [];
        if ($request->has('phone_number'))
            $editData['phone_number'] = $request->phone_number;
        else
            $editData['phone_number'] = $product->phone_number;

        if ($request->has('product_name'))
            $editData['product_name'] = $request->product_name;
        else
            $editData['product_name'] = $product->product_name;

        if ($request->has('description'))
            $editData['description'] = $request->description;
        else
            $editData['description'] = $product->description;

        if ($request->has('duration_of_use'))
            $editData['duration_of_use'] = $request->duration_of_use;
        else
            $editData['duration_of_use'] = $product->duration_of_use;

        if ($request->has('is_deliverable'))
            $editData['is_deliverable'] = $request->is_deliverable;
        else
            $editData['is_deliverable'] = $product->is_deliverable;

        if ($request->has('is_free'))
            $editData['is_free'] = $request->is_free;
        else
            $editData['is_free'] = $product->is_free;

        if ($request->has('items_count'))
            $editData['items_count'] = $request->items_count;
        else
            $editData['items_count'] = $product->items_count;

        if ($request->has('sub_category_id'))
            $editData['sub_category_id'] = $request->sub_category_id;
        else
            $editData['sub_category_id'] = $product->sub_category_id;

        if ($request->has('location_id'))
            $editData['location_id'] = $request->location_id;
        else
            $editData['location_id'] = $product->location_id;

        if ($request->has('price_suggestion'))
            $editData['price_suggestion'] = $request->price_suggestion;
        else
            $editData['price_suggestion'] = $product->price_suggestion;

        return $editData;
    }

    private function updateProduct($product, $data)
    {
        $product->update([
            'phone_number' => $data['phone_number'],
            'product_name' => $data['product_name'],
            'description' => $data['description'],
            'duration_of_use' => $data['duration_of_use'],
            'items_count' => $data['items_count'],
            'is_free' => $data['is_free'],
            'is_deliverable' => $data['is_deliverable'],
            'price_suggestion' => $data['price_suggestion'],
            'location_id' => $data['location_id'],
            'sub_category_id' => $data['sub_category_id'],
        ]);
        /*
        if($data['is_free']==true){
            Price::where('product_id')->delete();
        }
        */
        return $product;
    }

    private function updateSocialLink($social_link, $product_id)
    {
        $product_social_link = ProductSocialLink::where('product_id', $product_id)
            ->where('type', $social_link['type'])
            ->first();
        if ($product_social_link != null) {
            if ($social_link['type'] == 0) {
                if ($social_link['link'][0] == '@')
                    $social_link['link'] = Str::replaceFirst('@', 'https://t.me/', $social_link['link']);
            }
            $product_social_link->update([
                'link' => $social_link['link']
            ]);
        } else {
            $controller = new SubmissionsController();
            $controller->storeSocialLink($social_link, $product_id);
        }
    }
}
