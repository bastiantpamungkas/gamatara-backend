<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function list(Request $request)
    {
        $keyword = $request->input('keyword');
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $data = Shift::orderBy($sort, $sortDirection)
        ->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'ilike', '%'.$keyword.'%');
        });

        $shift = Helper::pagination($data, $request, [
            'name',
            'early_check_in',
            'check_in',
            'late_check_in',
            'early_check_out',
            'check_out',
            'late_check_out'
        ]);

        return response()->json([
            'success' => true,
            'data' => $shift
        ], 200);
    }

    public function store(Request $request)
    {
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'early_check_in' => 'required',
            'check_in' => 'required',
            'late_check_in' => 'required',
            'early_check_out' => 'required',
            'check_out' => 'required',
            'late_check_out' => 'required'
        ]);

        if ($valid == true) {
            try {
                $shift = Shift::create($request->all());

                return response()->json([
                    'success' => true,
                    'message' => "Success Added Shift",
                    'data' => $shift
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
            'message' => "Failed Added Shift",
        ], 422);
    }

    public function detail($id)
    {
        $shift = Shift::find($id);

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => "Shift Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shift
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'early_check_in' => 'required',
            'check_in' => 'required',
            'late_check_in' => 'required',
            'early_check_out' => 'required',
            'check_out' => 'required',
            'late_check_out' => 'required'
        ]);

        if ($valid == true) {
            try {
                $shift = Shift::find($id);

                if (!$shift) {
                    return response()->json([
                        'success' => false,
                        'message' => "Shift Not Found",
                    ], 404);
                }

                $shift->update($request->all());

                return response()->json([
                    'success' => true,
                    'message' => "Success Updated Shift",
                    'data' => $shift
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
            'message' => "Failed Updated Shift",
        ], 422);
    }

    public function delete($id)
    {
        try {
            $shift = Shift::find($id);

            if (!$shift) {
                return response()->json([
                    'success' => true,
                    'message' => "Shift Not Found",
                ], 200);
            }

            $shift->delete();

            return response()->json([
                'success' => true,
                'message' => "Success Deleted Shift",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
