<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

class Helper
{

    public static function validator($data, $rules)
    {
        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 422);    
        } else {
            return true;
        }
    }

    public static function pagination($model, $request, $params)
    {
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));

        // Mulai query dengan pencarian
        $data = $model->when($keyword, function ($query) use ($keyword, $params) {
            foreach ($params as $param) {
                // Cek jika parameter mengandung "." untuk relasi
                if (strpos($param, '.') !== false) {
                    [$relation, $column] = explode('.', $param, 2);

                    // Gunakan orWhereHas untuk relasi
                    // $query->orWhereHas($relation, function ($q) use ($column, $keyword) {
                    //     $q->whereRaw("LOWER(CAST($column AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
                    // });
                } else {
                    // Cek jika keyword adalah tahun
                    // if (preg_match('/^\d{4}$/', $keyword)) {
                    //     // Gunakan whereYear untuk kolom tanggal
                    //     $query->orWhereYear($param, $keyword);
                    // } else {
                    //     // Gunakan LIKE untuk kolom teks
                    //     $query->orWhereRaw("LOWER(CAST($param AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
                    // }
                }
            }
        });

        return $data->paginate($pageSize, ['*'], 'page', $page);
    }

    public static function statusAtt($status){
        if($status == 1){
            return 'Masuk Lebih Awal';
        }else if($status == 2){
            return 'Masuk Tepat Waktu';
        }else{
            return 'Terlambat Masuk';
        }
    }
}
