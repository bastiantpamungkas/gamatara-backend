<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class SettingController extends Controller
{
    public function detail($id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => "Setting Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting
        ], 200);
    }
    
    public function status_settings(){
        $sti = Setting::find(1);
        
        return response()->json([
            'success' => true,
            'message' => "Button " . ($sti->status == 'ON' ? 'Active' : 'Non-Active'),
            'data' => [
                'is_active' => $sti->status == 'ON',
            ]
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
