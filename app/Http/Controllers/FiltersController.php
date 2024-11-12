<?php /** @noinspection PhpUnnecessaryLocalVariableInspection */
/** @noinspection PhpRedundantOptionalArgumentInspection */
/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FiltersController extends Controller
{
    public function filterProducts(Request $request)
    {
        $validator = Validator::make($request->all(), $this->filterRules());
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => 'fail',
                'data' => $validator->errors()
            ], 400);

        $products = $this->callFilter($request);
        $products = $this->sortProducts($request, $products);
        return response()->json([
            'status' => true,
            'message' => trans('messages.products'),
            'data' => $products
        ]);
    }

    private function filterRules()
    {
        return [
            'per_page' => ['required', 'int'],
            'sort_by' => [Rule::in('submission_date', 'price_ascending', 'price_descending', 'seller_evaluation')],
            'sub_category_id' => ['exists:sub_categories,id'],
            'location_id' => ['exists:locations,id'],
            'price_range_max' => ['required_with:price_range_min', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'price_range_min' => ['required_with:price_range_max', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/']
        ];
    }

    /** FILTER */
    private function callFilter(Request $request)
    {
        $category = $request->sub_category_id != null;
        $location = $request->location_id != null;
        $price_range = $request->price_range_min != null;

        if ($category && !$location && !$price_range)
            return $this->filterWithCategory($request);

        else if (!$category && $location && !$price_range)
            return $this->filterWithLocation($request);

        else if (!$category && !$location && $price_range)
            return $this->filterWithPriceRange($request);

        else if ($category && $location && !$price_range)
            return $this->filterWithCategoryLocation($request);

        else if ($category && !$location && $price_range)
            return $this->filterWithCategoryPriceRange($request);

        else if (!$category && $location && $price_range)
            return $this->filterWithLocationPriceRange($request);

        else if ($category && $location && $price_range)
            return $this->filterWithCategoryLocationPriceRange($request);

        else
            return $this->filterWithNon($request);
    }

    private function filterWithCategoryLocationPriceRange(Request $request)
    {
        /*
         * join on price
         */
        $minPrice = $request->price_range_min;
        $maxPrice = $request->price_range_max;
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('sub_category_id', $request->sub_category_id)
            ->where('location_id', $request->location_id)
            ->get($this->productsDataToGet());
        $products = json_decode(json_encode($products), true);
        $products = $this->getSellerEvaluation($products);
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        $products = collect($products);
        return $products->where('new_price', '>=', $minPrice)
            ->where('new_price', '<=', $maxPrice);
    }

    private function filterWithCategoryLocation(Request $request)
    {
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('sub_category_id', $request->sub_category_id)
            ->where('location_id', $request->location_id)
            ->paginate($request->per_page, $this->productsDataToGet());
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        return $this->getSellerEvaluation($products);
    }

    private function filterWithCategoryPriceRange(Request $request)
    {
        /*
         * join on price
         */
        $minPrice = $request->price_range_min;
        $maxPrice = $request->price_range_max;
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('sub_category_id', $request->sub_category_id)
            ->get($this->productsDataToGet());
        $products = json_decode(json_encode($products), true);
        $products = $this->getSellerEvaluation($products);
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        $products = collect($products);
        return $products->where('new_price', '>=', $minPrice)
            ->where('new_price', '<=', $maxPrice);
    }

    private function filterWithLocationPriceRange(Request $request)
    {
        /*
         * join on price
         */
        $minPrice = $request->price_range_min;
        $maxPrice = $request->price_range_max;
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('location_id', $request->location_id)
            ->get($this->productsDataToGet());
        $products = json_decode(json_encode($products), true);
        $products = $this->getSellerEvaluation($products);
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        $products = collect($products);
        return $products->where('new_price', '>=', $minPrice)
            ->where('new_price', '<=', $maxPrice);
    }

    private function filterWithCategory(Request $request)
    {
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('sub_category_id', $request->sub_category_id)
            ->paginate($request->per_page, $this->productsDataToGet());
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        return $this->getSellerEvaluation($products);
    }

    private function filterWithLocation(Request $request)
    {
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->where('location_id', $request->location_id)
            ->paginate($request->per_page, $this->productsDataToGet());
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        return $this->getSellerEvaluation($products);
    }

    private function filterWithPriceRange(Request $request)
    {
        /*
         * join on price
         */
        $minPrice = $request->price_range_min;
        $maxPrice = $request->price_range_max;
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->get($this->productsDataToGet());
        $products = $this->getSellerEvaluation($products);
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        $products = collect($products);
        return $products->where('new_price', '>=', $minPrice)
            ->where('new_price', '<=', $maxPrice);
    }

    private function filterWithNon(Request $request)
    {
        $products = Product::where('product_status', 1)
            ->where('is_sold', 0)
            ->where('items_count', '>', 0)
            ->paginate($request->per_page, $this->productsDataToGet());
        $products = (new ProductController())->getPhotosAndPrices($products);
        if (!Auth::guest())
            $products = (new ProductController())->allIsSaved($products, Auth::id());
        return $this->getSellerEvaluation($products);
    }

    private function productsDataToGet()
    {
        return [
            'id As product_id',
            'product_name',
            'is_free',
            'is_deliverable',
            'seller_id',
            'created_at'
        ];
    }


    /** SORT */
    private function getSellerEvaluation($products)
    {
        for ($i = 0; $i < sizeof($products); $i++) {
            $evaluations = Evaluation::where('evaluated_id', $products[$i]['seller_id'])->pluck('evaluation_number');
            $evaluations = json_decode(json_encode($evaluations), true);
            if (sizeof($evaluations) == 0)
                $products[$i]['seller_evaluation'] = 0;
            else
                $products[$i]['seller_evaluation'] = array_sum($evaluations) / count($evaluations);
        }
        return $products;
    }

    private function sortProducts(Request $request, $products)
    {
        $sort_by = $request->sort_by;
        if ($sort_by == 'price_ascending')
            return $this->sortProductBYPriceAscending($request, $products);
        else if ($sort_by == 'price_descending')
            return $this->sortProductBYPriceDescending($request, $products);
        else if ($sort_by == 'seller_evaluation')
            return $this->sortProductBYSellerEvaluation($request, $products);
        else
            return $this->sortProductBYSubmissionDate($request, $products);
    }

    private function sortProductBYSubmissionDate(Request $request, $products)
    {
        data_get($products, 'product_id');
        $products = $products->sortByDesc('created_at');
        $totalGroup = count($products);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $products = new LengthAwarePaginator($products->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return $products;
    }

    private function sortProductBYPriceAscending(Request $request, $products)
    {
        data_get($products, 'new_price');
        $products = $products->sortBy('new_price');
        $totalGroup = count($products);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $products = new LengthAwarePaginator($products->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return $products;
    }

    private function sortProductBYPriceDescending(Request $request, $products)
    {
        data_get($products, 'new_price');
        $products = $products->sortByDesc('new_price');
        $totalGroup = count($products);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $products = new LengthAwarePaginator($products->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return $products;
    }

    private function sortProductBYSellerEvaluation(Request $request, $products)
    {
        data_get($products, 'seller_evaluation');
        $products = $products->sortByDesc('seller_evaluation');
        $totalGroup = count($products);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $products = new LengthAwarePaginator($products->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return $products;
    }
}
