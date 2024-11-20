# GAMATARA BACKEND



## API SPEK

### Dashboard COUNTS

GET
/dashboard/counts

Response
```
{
    "employee_count": 3,
    "employee_present": 0,
    "employee_late_in": 0,
    "employee_early_out": 3,
    "people_in": 0,
    "people_out": 0
}
```

### Dashboard CART_EMPLOYEE

GET
/dashboard/charts_employee

Params
```
{
    'type_employee' : 1,
    'date_start' : '2024-11-12',
    'date_end' : '2024-11-18'
}
```

Response
```
{
    "filter": {
        "type_employee": null,
        "date_start": "2024-11-12",
        "date_end": "2024-11-18"
    },
    "days": [
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
        "Minggu",
        "Senin"
    ],
    "data": [
        {
            "type_employee_id": 2,
            "type_employee_name": "Karyawan Out Sourcing",
            "attendance": [
                0,
                0,
                0,
                0,
                0,
                0,
                0
            ]
        },
        {
            "type_employee_id": 3,
            "type_employee_name": "Karyawan Sub Count",
            "attendance": [
                0,
                0,
                0,
                0,
                0,
                0,
                0
            ]
        },
        {
            "type_employee_id": 1,
            "type_employee_name": "Karyawan Tetap",
            "attendance": [
                3,
                0,
                0,
                0,
                0,
                0,
                0
            ]
        }
    ]
}
```

### Dashboard LIST_ATT_EMPLOYEE

GET
/dashboard/list

Params
```
{
    'page_size' : 10,
    'page'  : 1,
    'keyword' : '123456',
    'shift' : 1,
    'status_checkin' : 1,
    'status_sheckout' : 1,
    'company' : 1
}
```

Desciption
```
status_checkin [
    1 => 'early_check_in'
    2 => 'on_time'
    3 => 'late_check-in
],
status_checkout [
    1 => 'early_check_out'
    2 => 'on_time'
    3 => 'late_check_out'
],
```

Response
```
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 3,
                "time_check_in": "2024-11-12 10:00:00",
                "time_check_out": "2024-11-12 17:00:00",
                "time_total": "08:00:00",
                "status_check_in": 1,
                "status_check_out": 1,
                "created_at": "2024-11-11T06:46:31.000000Z",
                "updated_at": "2024-11-11T06:46:31.000000Z",
                "user": [
                    {
                        "id": 3,
                        "name": "Employee",
                        "email": "employee@gmail.com",
                        "email_verified_at": null,
                        "pin": "123456",
                        "shift_id": 1,
                        "created_at": "2024-11-11T21:35:58.000000Z",
                        "updated_at": "2024-11-11T21:35:58.000000Z",
                        "nip": null,
                        "type_employee_id": 1,
                        "company_id": 1,
                        "shift": [
                            {
                                "id": 1,
                                "name": "Shift Ketika",
                                "early_check_in": "11:41:00",
                                "check_in": "15:43:00",
                                "late_check_in": "13:42:00",
                                "early_check_out": "11:42:00",
                                "check_out": "14:42:00",
                                "late_check_out": "13:42:00",
                                "created_at": "2024-11-11T21:39:48.000000Z",
                                "updated_at": "2024-11-11T21:39:48.000000Z"
                            }
                        ],
                        "company": [
                            {
                                "id": 1,
                                "name": "GAMATARA",
                                "created_at": "2024-11-18T23:09:06.000000Z",
                                "updated_at": "2024-11-18T23:09:06.000000Z"
                            }
                        ]
                    }
                ]
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/attendance/list?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/attendance/list?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/attendance/list?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://127.0.0.1:8000/api/attendance/list",
        "per_page": 10,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```
### TYPE EMPLOYEE

GET
/type_employee

Response
```
[
    {
        "id": 3,
        "name": "Sub-Kon",
        "created_at": "2024-11-18T01:02:30.000000Z",
        "updated_at": "2024-11-18T01:02:30.000000Z"
    },
    {
        "id": 2,
        "name": "Outsourcing",
        "created_at": "2024-11-18T01:02:30.000000Z",
        "updated_at": "2024-11-18T01:02:30.000000Z"
    },
    {
        "id": 1,
        "name": "Tetap",
        "created_at": "2024-11-18T01:02:30.000000Z",
        "updated_at": "2024-11-18T01:02:30.000000Z"
    }
]
```

### REPORT EMPLOYEE

GET
/attendance/report

Params
```
{
    'page_size' : 10
    'page' : 1
    'keyword' : ''
    'status' : 1,
    'most_present' : true,
    'smallest_late' : true,
    'most_late' : true
}
```

Desciption
```
status [
    1 => 'Aktif'
    2 => 'Tidak Aktif'
],
```

Response
```
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 3,
                "name": "Employee",
                "nip": null,
                "status": 1,
                "attendance_count": 9,
                "ontime_attendance": 0,
                "late_attendance": 8,
                "early_checkout": 9
            },
            {
                "id": 4,
                "name": "Employee 1",
                "nip": null,
                "status": 1,
                "attendance_count": 8,
                "ontime_attendance": 0,
                "late_attendance": 7,
                "early_checkout": 8
            },
            {
                "id": 5,
                "name": "Employee 2",
                "nip": null,
                "status": 2,
                "attendance_count": 8,
                "ontime_attendance": 0,
                "late_attendance": 7,
                "early_checkout": 8
            },
            {
                "id": 1,
                "name": "Admin",
                "nip": null,
                "status": 1,
                "attendance_count": 1,
                "ontime_attendance": 0,
                "late_attendance": 1,
                "early_checkout": 1
            },
            {
                "id": 2,
                "name": "Security",
                "nip": null,
                "status": 1,
                "attendance_count": 0,
                "ontime_attendance": 0,
                "late_attendance": 0,
                "early_checkout": 0
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/attendance/report?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/attendance/report?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/attendance/report?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://127.0.0.1:8000/api/attendance/report",
        "per_page": 10,
        "prev_page_url": null,
        "to": 5,
        "total": 5
    }
}
```

### REPORT GUEST

GET
/attendance_guest/report

Params
```
{
    'page_size' : 10,
    'page' : 1,
    'keyword' : ''
}
```

Response
```
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Candra",
                "phone_number": "081234567890",
                "total_attendance": 1,
                "last_visit": "2024-11-20 05:07:39",
                "total_duration": "04:00:00"
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/attendance_guest/report?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/attendance_guest/report?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/attendance_guest/report?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://127.0.0.1:8000/api/attendance_guest/report",
        "per_page": 10,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```