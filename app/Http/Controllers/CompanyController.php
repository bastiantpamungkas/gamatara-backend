<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function list()
    {
        $companies = Company::orderBy('id', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'companies' => $companies,
        ]);
    }
}
