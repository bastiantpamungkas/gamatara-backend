<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TypeEmployee;
use Illuminate\Http\Request;

class TypeEmployeeController extends Controller
{
    public function list(){
        $data = TypeEmployee::all();

        return response()->json($data);
    }
}
