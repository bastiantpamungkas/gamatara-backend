<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PersPerson;
use Illuminate\Http\Request;
use App\Jobs\JobPersPerson;
use Carbon\Carbon;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $shift_employee = $request->input('shift_employee') ?? null;
        $company = $request->input('company') ?? null;
        $status = $request->input('status') ?? null;

        $query = User::with('type', 'company', 'shift')->orderBy('created_at', 'desc');
        if ($type_employee) {
            $query->where('type_employee_id', $type_employee);
        }
        if ($shift_employee) {
            $query->where('shift_id', $shift_employee);
        }
        if ($company) {
            $query->where('company_id', $company);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $user = Helper::pagination($query, $request, ['name', 'email', 'nip']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function store(Request $request)
    {
        $valid = Helper::validator($request->all(), [
            'nip' => 'required',
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => 'required',
            'type_employee_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($valid == true) {
            try {
                $user = User::create($request->all());

                $user->assignRole($request->role);

                return response()->json([
                    'success' => true,
                    'message' => "Success Added Employee",
                    'data' => $user
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
            'message' => "Failed Added Employee",
        ], 422);
    }

    public function sync(Request $request)
    {
        try {
            // sync data persperson dari mesin absensi
            JobPersPerson::dispatch();

            // $person = PersPerson::limit(5)->get();
            // // $person = PersPerson::get();
            // if ($person) {
            //     foreach ($person as $row) {
            //         $user = User::where('nip', $row->pin)->first();
            //         if ($user) {
            //             $user->name = $row->name;
            //             $user->pin = $row->pin;
            //             $user->nip = $row->pin;
            //             $user->email = Str::slug($row->name) . $row->pin . '@gmail.com';
            //             $user->password = '1235678';
            //             $user->type_employee_id = 1;
            //             $user->save();
            //         } else {
            //             User::create(
            //                 [
            //                     'name' => $row->name,
            //                     'pin' => $row->pin,
            //                     'nip' => $row->pin,
            //                     'email' => Str::slug($row->name) . $row->pin . '@gmail.com',
            //                     'password' => '12345678',
            //                     'type_employee_id' => 1,
            //                 ]
            //             );
            //         }
            //     };
            // }

            return response()->json([
                'success' => true,
                'message' => "Success Sync Employee"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Added Employee",
        ], 422);
    }

    public function detail($id)
    {
        $user = User::with('type', 'company', 'shift')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Employee Not Found",
            ], 404);
        }

        $user->getRoleNames();

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $valid = Helper::validator($request->all(), [
            // 'nip' => 'required',
            // 'name' => 'required',
            // 'email' => 'email|required|unique:users,email,' . $id,
            // 'password' => 'required',
            'type_employee_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($valid == true) {
            try {
                $user = User::find($id);

                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => "Employee Not Found",
                    ], 404);
                }

                $user->update($request->all());

                $user->syncRoles([]);

                $user->assignRole($request->role);

                return response()->json([
                    'success' => true,
                    'message' => "Success Updated Employee",
                    'data' => $user
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
            'message' => "Failed Updated Employee",
        ], 422);
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => "Employee Not Found",
                ], 200);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => "Success Deleted Employee",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_status(Request $request)
    {
        $ids = $request->input('ids');
        $status = $request->input('status', 1);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No IDs provided.'
            ], 422);
        }

        $user = User::whereIn('id', $ids);

        $user->update(['status' => $status]);


        return response()->json([
            'success' => true,
            'message' => 'Success Update Status Employee',
            'data' => User::whereIn('id', $ids)->get()
        ], 200);
    }

    public function update_shift(Request $request)
    {
        $ids = $request->input('ids');
        $shift_id = $request->input('shift_id', 1);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No IDs provided.'
            ], 422);
        }

        $user = User::whereIn('id', $ids);

        $user->update(['shift_id' => $shift_id]);


        return response()->json([
            'success' => true,
            'message' => 'Success Update Shift Employee',
            'data' => User::whereIn('id', $ids)->get()
        ], 200);
    }

    public function list_absent(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $keyword = $request->input('keyword') ?? null;

        $absent = User::where('status', 1)->with('type', 'company', 'shift');
        if ($type_employee) {
            $absent->where('type_employee_id', $type_employee);
        }
        if ($keyword) {
            $absent->whereRaw("LOWER(CAST(name AS TEXT)) LIKE ? or LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $absent->whereDoesntHave('attendance', function ($query) {
            $query->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        });

        $user = Helper::pagination($absent->orderBy('created_at', 'desc'), $request, ['name', 'nip', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function list_present(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $keyword = $request->input('keyword') ?? null;

        $present = User::where('status', 1)->with('type', 'company', 'shift');
        if ($type_employee) {
            $present->where('type_employee_id', $type_employee);
        }
        if ($keyword) {
            $present->whereRaw("LOWER(CAST(name AS TEXT)) LIKE ? or LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $present->whereHas('attendance', function ($query) {
            $start_date = Date("Y-m-d");
            $end_date = Date("Y-m-d", strtotime("+1 day"));

            $query->whereBetween('time_check_out', [$start_date, $end_date])
                ->orWhereBetween('time_check_in', [$start_date, $end_date]);
        });

        $user = Helper::pagination($present->orderBy('created_at', 'desc'), $request, ['name', 'nip', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function list_late(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $keyword = $request->input('keyword') ?? null;

        $late = User::where('status', 1)->with('type', 'company', 'shift', 'attendance');
        if ($type_employee) {
            $late->where('type_employee_id', $type_employee);
        }
        if ($keyword) {
            $late->whereRaw("LOWER(CAST(name AS TEXT)) LIKE ? or LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $late->whereHas('attendance', function ($query) {
            $start_date = Date("Y-m-d");
            $end_date = Date("Y-m-d", strtotime("+1 day"));

            $query->where('status_check_in', 3);
            $query->whereBetween('time_check_out', [$start_date, $end_date])
                ->orWhereBetween('time_check_in', [$start_date, $end_date]);
        });

        $user = Helper::pagination($late->orderBy('created_at', 'desc'), $request, ['name', 'nip', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function list_early_checkout(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $keyword = $request->input('keyword') ?? null;

        $early_checkout = User::where('status', 1)->with('type', 'company', 'shift', 'attendance');
        if ($type_employee) {
            $early_checkout->where('type_employee_id', $type_employee);
        }
        if ($keyword) {
            $early_checkout->whereRaw("LOWER(CAST(name AS TEXT)) LIKE ? or LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $early_checkout->whereHas('attendance', function ($query) {
            $query->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->where('status_check_out', 2);
        });

        $user = Helper::pagination($early_checkout->orderBy('created_at', 'desc'), $request, ['name', 'nip', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function list_in_gate(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $keyword = $request->input('keyword') ?? null;

        $in_gate = User::where('status', 1)->with('type', 'company', 'shift', 'attendance');
        if ($type_employee) {
            $in_gate->where('type_employee_id', $type_employee);
        }
        if ($keyword) {
            $in_gate->whereRaw("LOWER(CAST(name AS TEXT)) LIKE ? or LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $in_gate->whereHas('att_log', function ($query) {
            $query->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereNotNull('time_check_in');
        });

        $user = Helper::pagination($in_gate->orderBy('created_at', 'desc'), $request, ['name', 'nip', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function list_out_gate(Request $request)
    {
        $type_employee = $request->input('type_employee') ?? null;
        $keyword = $request->input('keyword') ?? null;

        $out_gate = User::where('status', 1)->with('type', 'company', 'shift', 'attendance');
        if ($type_employee) {
            $out_gate->where('type_employee_id', $type_employee);
        }
        if ($keyword) {
            $out_gate->whereRaw("LOWER(CAST(name AS TEXT)) LIKE ? or LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $out_gate->whereHas('att_log', function ($query) {
            $start_date = Date("Y-m-d");
            $end_date = Date("Y-m-d", strtotime("+1 day"));

            $query->whereBetween('time_check_out', [$start_date, $end_date])
                ->orWhereBetween('time_check_in', [$start_date, $end_date]);
        });

        $user = Helper::pagination($out_gate->orderBy('created_at', 'desc'), $request, ['name', 'nip', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }
}
