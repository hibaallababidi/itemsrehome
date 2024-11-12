<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function add_report(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [

            'product_id' => 'required|integer|exists:products,id',
            'message' => 'required|string',

        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $add_report = Report::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'message' => $request->message,
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.add_report'),
            'data' => [],
        ], 201);

    }

    public function show_reports(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => 'fail',
                'data' => $validator->errors()
            ], 400);

        $show_reports = User::join('products', 'users.id', '=', 'products.seller_id')
            ->join('reports', 'reports.product_id', '=', 'products.id')
            ->get([
                'products.product_name',
                'users.first_name As seller_first_name',
                'users.last_name As seller_last_name',
                'reports.message',
                'reports.user_id',
                'products.seller_id',
                'reports.created_at'
            ]);
        $show_reports = json_decode(json_encode($show_reports), true);

        for ($i = 0; $i < sizeof($show_reports); $i++) {
            $order = DB::table('users')
                ->where('id', $show_reports[$i]['user_id'])
                ->get(['users.first_name As Buyer_first_name',
                    'users.last_name As Buyer_last_name'])->first();
            $order = json_decode(json_encode($order), true);
            $show_reports[$i]['user_first_name'] = $order['Buyer_first_name'];
            $show_reports[$i]['user_last_name'] = $order['Buyer_last_name'];
        }
        $show_reports = collect($show_reports);
        data_get($show_reports, 'created_at');
        $show_reports = $show_reports->sortByDesc('created_at');

        $totalGroup = count($show_reports);
        $perPage = $request->per_page;
        $page = Paginator::resolveCurrentPage('page');
        $show_reports = new LengthAwarePaginator($show_reports->forPage($page, $perPage), $totalGroup, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return response()->json([
            'status' => true,
            'message' => trans('messages.show_report'),
            'data' => $show_reports,
        ], 201);

    }

}
