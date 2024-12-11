<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class SettingController extends Controller
{
    public function status_settings(){
        $sti = Setting::find(1);
        
        return response()->json([
            'success' => true,
            'message' => "Button " . ($sti->status == 'ON' ? 'Active' : 'Non-Active'),
        ], 200);
    }

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

    public function update_hari_kerja(Request $request){
        $valid = Helper::validator($request->all(), [
            'hari_kerja' => 'required',
        ]);
        $sti = Setting::find(1);

        $sti->update([
            'total_hari_kerja' => $request->input('hari_kerja')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Total Hari Kerja Updated",
        ], 200);
    }
}
