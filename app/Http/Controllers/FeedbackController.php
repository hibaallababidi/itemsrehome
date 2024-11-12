<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function add_feedback(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'reaction' => 'required|integer',
            'message' => 'required|string',
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $add_feedback = Feedback::create([
            'message' => $request->message,
            'reaction' => $request->reaction,
            'is_seen' => false,
        ]);


        return response()->json([
            'status' => true,
            'message' => trans('messages.add_feedback'),
            'data' => [],
        ], 201);


    }

    public function display_feedback(Request $request)
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

        $display_feedbacks = DB::table('feedback')->orderByDesc('created_at')
            ->paginate($request->per_page, [
                'id',
                'message',
                'reaction',
                'is_seen'
            ]);


        return response()->json([
            'status' => true,
            'message' => trans('messages.displayFeedbacks'),
            'data' => $display_feedbacks,
        ], 200);


    }

}
