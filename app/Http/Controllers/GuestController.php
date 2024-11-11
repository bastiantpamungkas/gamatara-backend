<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Guest;

class GuestController extends Controller
{
    public function list(Request $request)
    {
        $guest = Helper::pagination(Guest::query(), $request , ['name', 'nik', 'phone_number']);

        return response()->json([
            'success' => true,
            'data' => $guest
        ], 200);
    }

    public function store(Request $request)
    {
        $valid = Helper::validator($request->all(),[
            'nik' => 'required',
            'name' => 'required',
            'phone_number' => 'required',
        ]);

        if($valid == true){
            try{
                $guest = Guest::create($request->all());

                return response()->json([
                    'success' => true,
                    'message' => "Success Added Guest",
                    'data' => $guest
                ], 200);
            }catch (\Exception $e){
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Added Guest"
        ], 422);
    }

    public function detail($id)
    {
        $guest = Guest::find($id);

        if(!$guest){
            return response()->json([
                'success' => false,
                'message' => 'Guest Not Found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $guest
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $valid = Helper::validator($request->all(),[
            'nik' => 'required',
            'name' => 'required',
            'phone_number' => 'required',
        ]);

        if ($valid == true) {
            try {
                $guest = Guest::find($id);

                if (!$guest) {
                    return response()->json([
                        'success' => false,
                        'message' => "Guest Not Found",
                    ], 404);
                }

                $guest->update($request->all());

                return response()->json([
                    'success' => true,
                    'message' => "Success Updated Guest",
                    'data' => $guest
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Updated Guest",
        ], 422);
    }

    public function delete($id)
    {
        try{
            $guest = Guest::find($id);
    
            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => "Guest Not Found",
                ], 404);
            }

            $guest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Success Deleted Guest'
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Updated Guest",
        ], 422);
    }
}
