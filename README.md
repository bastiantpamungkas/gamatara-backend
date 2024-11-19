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

## Dashboard CART_EMPLOYEE

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

## Dashboard LIST_ATT_EMPLOYEE

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