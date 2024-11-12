<?php /** @noinspection PhpUnused */
/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FavouriteCategory;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class CategoryController extends Controller
{
    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => ['required', 'string', 'unique:categories,category_name'],
            'photo' => ['required', 'image'],
            'sub_category' => ['required', 'string', 'unique:sub_categories,sub_category_name']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $photo = $request->file('photo');
        $extension = $photo->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $photo->move("photos/", $filename);
        $category = Category::create([
            'category_name' => $request->category,
//            'photo' => URL::to("/photos/$filename"),
            'photo' => '/photos/' . $filename
        ]);
        SubCategory::create([
            'category_id' => $category->id,
            'sub_category_name' => $request->sub_category
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.add_category'),
            'data' => [$category]
        ], 201);
    }

    public function addSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'int', 'exists:categories,id'],
            'sub_category' => ['required', 'string', 'unique:sub_categories,sub_category_name']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        SubCategory::create([
            'category_id' => $request->category_id,
            'sub_category_name' => $request->sub_category
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.add_sub_category'),
            'data' => []
        ], 201);
    }

    public function editCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'category' => ['string'],
            'photo' => ['image'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $category = Category::where('id', $request->category_id)->first();
        if ($request->has('category')) {
            $category->update([
                'category_name' => $request->category
            ]);
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $extension = $photo->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $photo->move("photos/", $filename);
            $category->update([
//                'photo' => URL::to("/photos/$filename"),
                'photo' => '/photos/' . $filename
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => trans('messages.update_category'),
            'data' => []
        ]);
    }

    public function editSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sub_category_id' => ['required', 'integer', 'exists:sub_categories,id'],
            'category_id' => ['integer', 'exists:categories,id'],
            'sub_category' => ['string'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $sub_category = SubCategory::where('id', $request->sub_category_id)->first();
        if ($request->has('sub_category')) {
            $sub_category->update([
                'sub_category_name' => $request->sub_category
            ]);
        }

        if ($request->has('category_id')) {
            $sub_category->update([
                'category_id' => $request->category_id
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => trans('messages.update_sub_category'),
            'data' => []
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'category_id' => ['required', 'exists:categories,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $sub_categories = SubCategory::where('category_id', $request->query('category_id'))->pluck('id');
        $product = Product::whereIn('sub_category_id', $sub_categories)->first();
        if ($product == null) {
            Category::where('id', $request->query('category_id'))->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.delete_category'),
                'data' => []
            ]);
        } else
            return response()->json([
                'status' => false,
                'message' => trans('messages.delete_category_fail'),
                'data' => []
            ]);
    }

    public function deleteSubCategory(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $product = Product::where('sub_category_id', $request->query('sub_category_id'))->first();
        if ($product == null) {
            SubCategory::where('id', $request->query('sub_category_id'))->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.delete_sub_category'),
                'data' => []
            ]);
        } else
            return response()->json([
                'status' => false,
                'message' => trans('messages.delete_sub_category_fail'),
                'data' => []
            ]);
    }

    public function displayCategoriesUser(Request $request)
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
        $categories = Category::paginate($request->per_page, [
            'id', 'category_name', 'photo'
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.category'),
            'data' => $categories
        ]);
    }

    public function displayCategoriesAdmin(Request $request)
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
        $categories = Category::paginate($request->per_page, [
            'id',
            'category_name',
            'photo',
            'created_at'
        ]);
        for ($i = 0; $i < sizeof($categories); $i++) {
            $count = SubCategory::where('category_id', $categories[$i]['id'])->get()->count();
            $categories[$i]['sub_categories_count'] = $count;
        }
        return response()->json([
            'status' => true,
            'message' => trans('messages.category'),
            'data' => $categories
        ]);
    }

    public function displaySubCategories(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'category_id' => ['required', 'exists:categories,id'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $subCategories = SubCategory::where('category_id', $request->query('category_id'))
            ->paginate($request->per_page, [
                'id', 'sub_category_name'
            ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.sub_category'),
            'data' => $subCategories
        ]);
    }

    public function displaySubCategoriesAdmin(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'category_id' => ['required', 'exists:categories,id'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $subCategories = SubCategory::where('category_id', $request->query('category_id'))
            ->paginate($request->per_page, [
                'id', 'sub_category_name', 'created_at'
            ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.sub_category'),
            'data' => $subCategories
        ]);
    }

    public function setFavouriteCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'exists:categories,id'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $id = Auth::user()->id;
        FavouriteCategory::create([
            'user_id' => $id,
            'category_id' => $request->category_id
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.set_favourite_category'),
            'data' => []
        ], 201);
    }
}
