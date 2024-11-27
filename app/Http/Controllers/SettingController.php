<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function update(){
        $sti = Setting::find(1);

        $sti->update([
            'status' => $sti->status == "ON" ? "OFF" : "ON"
        ]);

        return response()->json([
            'success' => true,
            'message' => "Button " . ($sti->status == 'ON' ? 'Active' : 'Non-Active'),
        ], 200);
    }
}
