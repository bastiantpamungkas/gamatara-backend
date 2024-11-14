<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $att = Helper::pagination(Attendance::with('user'), $request, [
            'time_check_in',
            'time_check_out',
            'user.name'
        ]);

        return response()->json([
            'success' => true,
            'data' => $att
        ], 200);
    }

    public function list_per_employee(Request $request, $id)
    {
        $att = Helper::pagination(Attendance::with('user')->where('user_id', $id), $request, [
            'time_check_in',
            'time_check_out'
        ]);

        return response()->json([
            'success' => true,
            'data' => $att
        ], 200);
    }

    public function detail($id)
    {
        $att = Attendance::with('user')->where('id', $id)->first();

        if (!$att) {
            return response()->json([
                'success' => false,
                'message' => "Attendance Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $att ?? []
        ], 200);
    }

    public function report(Request $request){
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));

        $user = User::select('id', 'name', 'nip', 'created_at')
            ->withCount([
                'attendance',
                'attendance as late_attendance' => function ($q) {
                    $q->where('status_check_in', 3);
                }
            ])
            ->when($keyword, function ($q) use ($keyword) {
                $q->orWhereRaw("LOWER(CAST(name AS TEXT)) LIKE ?", ['%' . $keyword . '%'])
                ->orWhereRaw("LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
            })
            ->paginate($pageSize, ['*'], 'page', $page);

        $user->getCollection()->transform(function($q) {
            $createdAt = Carbon::parse($q->created_at);
            $usrFormated = Carbon::parse($createdAt)->format('Y-m-d');
            $ranges = range($usrFormated, Carbon::now()->format('Y-m-d'));
            $range = intval($ranges[0]);

            $q->not_attendance = $range - $q->attendance_count;

            return $q;
        });

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }
}
