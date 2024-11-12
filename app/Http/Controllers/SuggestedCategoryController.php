<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SuggestedCategory;
use Illuminate\Http\Request;


class SuggestedCategoryController extends Controller
{
    public function add_suggested_category(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [

            'suggested_category_name' => 'required|string',

        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $suggested_category = SuggestedCategory::create([
            'user_id' => $user->id,
            'suggested_category_name' => $request->suggested_category_name,
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.add_suggested_category'),
            'data' => [],
        ], 201);
    }

    public function show_suggested_category(Request $request)
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
        $suggested_category = SuggestedCategory::
        orderByDesc('suggested_categories.created_at')
            ->paginate($request->per_page, [
                'suggested_categories.id',
                '.user_id',
                'suggested_categories.suggested_category_name',

            ]);

        return response()->json([
            'status' => true,
            'message' => trans('messages.show_suggested_category'),
            'data' => $suggested_category,
        ], 201);
    }


    public function count_suggested_category()
    {
        $suggested_category = DB::table('suggested_categories')->count();

        return response()->json([
            'status' => true,
            'message' => trans('messages.count_suggested_category'),
            'data' => $suggested_category,
        ], 201);
    }

}
