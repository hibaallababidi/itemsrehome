<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OperationAdminController extends Controller
{

    public function DisplayUsersForAdmin(Request $request)
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

        $getUsers = User::paginate($request->per_page, [
            'users.id',
            'users.first_name as first_name',
            'users.last_name as last_name',
            'users.email as email',
            'users.phone_number as phone_number',
        ]);


        for ($i = 0; $i < sizeof($getUsers); $i++) {
            $get = DB::table('blocks')
                ->where('user_id', $getUsers[$i]['id'])
                ->get()->last();
            if ($get) {
                $getUsers[$i]['status'] = $get->status;


            } else
                $getUsers[$i]['status'] = 0;

        }

        return response()->json([
            'status' => true,
            'message' => trans('messages.displayUsers'),
            'data' => $getUsers,
        ], 200);


    }


    public function DisplayBlocksUsersForAdmin(Request $request)
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

        $getUsers = User::join('blocks', 'blocks.user_id', '=', 'users.id')
            ->where('blocks.status', 1)
            ->distinct()
            ->paginate($request->per_page, [
                'users.id',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email as email',
                'users.phone_number as phone_number',
                'blocks.status'
            ]);

        return response()->json([
            'status' => true,
            'message' => trans('messages.bannedUsers'),
            'data' => $getUsers,
        ], 200);
    }


    public function BestBuyers(Request $request)
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
        $getUsers = User::join('orders', 'orders.user_id', '=', 'users.id')
            ->where('orders.order_status', 2)
            ->distinct()
            ->get([
                'users.id',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email as email',
                'users.phone_number as phone_number',
            ]);


        for ($i = 0; $i < sizeof($getUsers); $i++) {
            $order = DB::table('orders')
                ->where('user_id', $getUsers[$i]['id'])
                ->where('orders.order_status', 2)
                ->get()->count();
            $getUsers[$i]['count'] = $order;
        }

        $getUsers = collect($getUsers);
        data_get($getUsers, 'count');
        $getUsers = $getUsers->sortByDesc('count');

        $totalGroup = count($getUsers);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $getUsers = new LengthAwarePaginator($getUsers
            ->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        return response()->json([
            'status' => true,
            'message' => trans('messages.BestBuyers'),
            'data' => $getUsers,
        ], 200);


    }


    public function BestSaller(Request $request)
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
        $getUsers = User::
        join('products', 'products.seller_id', '=', 'users.id')
            ->where('products.is_sold', 1)
            ->distinct()
            ->get([
                'users.id',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email as email',
                'users.phone_number as phone_number',
            ]);


        for ($i = 0; $i < sizeof($getUsers); $i++) {
            $product = DB::table('products')
                ->where('seller_id', $getUsers[$i]['id'])
                ->where('products.is_sold', 1)
                ->get()->count();
            $getUsers[$i]['count'] = $product;
        }

        $getUsers = collect($getUsers);
        data_get($getUsers, 'count');
        $getUsers = $getUsers->sortByDesc('count');

        $totalGroup = count($getUsers);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $getUsers = new LengthAwarePaginator($getUsers
            ->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.BestSaller'),
            'data' => $getUsers,
        ], 200);


    }
}

