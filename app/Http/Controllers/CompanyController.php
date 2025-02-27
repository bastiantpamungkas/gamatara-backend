<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function list(Request $request){
        $keyword = $request->input('keyword');
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $data = Company::orderBy($sort, $sortDirection)
        ->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'ilike', '%'.$keyword.'%');
        });

        $company = Helper::pagination($data, $request, [
            'name'
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $company,
        ]);
    }
    
    public function detail($id){
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => "Company Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $company
        ], 200);
    }

    public function store(Request $request){
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
        ]);

        if($valid == true){
            try {
                $company = Company::create($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Success Added Company',
                    'data' => $company
                ],200);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ],422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Added Company",
        ], 422);
    }
    
    public function update(Request $request, $id){
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
        ]);

        if($valid == true){
            try {
                $company = Company::find($id);

                $company->update($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Success Updated Company',
                    'data' => $company
                ],200);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ],422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Updated Company",
        ], 422);
    }
}
