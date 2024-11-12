<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    public function add_block(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $add_block = Block::create([
            'user_id' => $request->user_id,
            'status' => True,
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.add_block'),
            'data' => [],
        ], 201);

    }

    public function delete_my_Blocks_List(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'user_id' => 'required|integer|exists:users,id',
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

//        $delete_my_Blocks_List = DB::table('blocks')
//            ->where('blocks.user_id', $request->user_id)
//            ->latest()
//            ->first()
//            ->update(['status' => false,]);

        Block::where('user_id', $request->user_id)
            ->latest()
            ->first()
            ->update(['status' => false,]);


        return response()->json([
            'status' => true,
            'message' => trans('messages.delete_block'),
            'data' => [],
        ], 200);
    }
}
