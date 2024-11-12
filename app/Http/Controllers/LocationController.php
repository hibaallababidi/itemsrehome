<?php /** @noinspection PhpMissingReturnTypeInspection */

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function displayCities(Request $request)
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
        $cities = City::paginate($request->per_page, ['id', 'city_name']);
        return response()->json([
            'status' => true,
            'message' => trans('messages.city'),
            'data' => $cities
        ]);
    }

    public function displayLocations(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'city_id' => ['required', 'int', 'exists:cities,id'],
            'per_page' => ['required', 'int'],
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);
        $locations = Location::where('city_id', $request->city_id)->paginate($request->per_page, ['id', 'location_name']);
        return response()->json([
            'status' => true,
            'message' => trans('messages.location'),
            'data' => $locations
        ]);
    }
}
