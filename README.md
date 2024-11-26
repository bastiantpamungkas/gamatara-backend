# GAMATARA BACKEND



## API SPEK

### LOGIN

POST
/login

Body
```
{
    'email' : 'admin@gmail.com'
    'password' : 123456
}
```

Response
```
{
    "success": true,
    "message": "Login Success",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzMyMDg4Nzk3LCJleHAiOjM2MDE3MzIwODg3OTcsIm5iZiI6MTczMjA4ODc5NywianRpIjoiNFlJTk1LQmVKM3UwRXpaZiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.pvLQ7TZWFPgHZq-Xd9ekJnweJlji99upXm3kFkgZ-HI",
    "token_type": "bearer",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@gmail.com",
        "email_verified_at": null,
        "pin": "123456",
        "shift_id": 2,
        "created_at": "2024-11-11T05:48:44.000000Z",
        "updated_at": "2024-11-11T05:48:44.000000Z",
        "nip": null,
        "type_employee_id": 1,
        "company_id": 1,
        "status": 1,
        "roles": [
            {
                "id": 9,
                "name": "Super Admin",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:17.000000Z",
                "updated_at": "2024-11-19T23:59:54.000000Z",
                "pivot": {
                    "model_type": "App\\Models\\User",
                    "model_id": 1,
                    "role_id": 9
                },
                "permissions": [
                    {
                        "id": 9,
                        "name": "dashboard",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:17.000000Z",
                        "updated_at": "2024-11-19T23:44:17.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 9
                        }
                    },
                    {
                        "id": 10,
                        "name": "employee",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:19.000000Z",
                        "updated_at": "2024-11-19T23:44:19.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 10
                        }
                    },
                    {
                        "id": 11,
                        "name": "role",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:20.000000Z",
                        "updated_at": "2024-11-19T23:44:20.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 11
                        }
                    },
                    {
                        "id": 12,
                        "name": "company",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:20.000000Z",
                        "updated_at": "2024-11-19T23:44:20.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 12
                        }
                    },
                    {
                        "id": 13,
                        "name": "report",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:22.000000Z",
                        "updated_at": "2024-11-19T23:44:22.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 13
                        }
                    }
                ]
            }
        ],
        "permissions": []
    }
}
```

### LOGOUT

POST
/logout

Response
```
{
    "success": true,
    "message": "Logout Success"
}
```

### ME

GET
/me

Response
```
{
    "success": true,
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@gmail.com",
        "email_verified_at": null,
        "pin": "123456",
        "shift_id": 2,
        "created_at": "2024-11-11T05:48:44.000000Z",
        "updated_at": "2024-11-11T05:48:44.000000Z",
        "nip": null,
        "type_employee_id": 1,
        "company_id": 1,
        "status": 1,
        "roles": [
            {
                "id": 9,
                "name": "Super Admin",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:17.000000Z",
                "updated_at": "2024-11-19T23:59:54.000000Z",
                "pivot": {
                    "model_type": "App\\Models\\User",
                    "model_id": 1,
                    "role_id": 9
                },
                "permissions": [
                    {
                        "id": 9,
                        "name": "dashboard",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:17.000000Z",
                        "updated_at": "2024-11-19T23:44:17.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 9
                        }
                    },
                    {
                        "id": 10,
                        "name": "employee",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:19.000000Z",
                        "updated_at": "2024-11-19T23:44:19.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 10
                        }
                    },
                    {
                        "id": 11,
                        "name": "role",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:20.000000Z",
                        "updated_at": "2024-11-19T23:44:20.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 11
                        }
                    },
                    {
                        "id": 12,
                        "name": "company",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:20.000000Z",
                        "updated_at": "2024-11-19T23:44:20.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 12
                        }
                    },
                    {
                        "id": 13,
                        "name": "report",
                        "guard_name": "api",
                        "created_at": "2024-11-19T23:44:22.000000Z",
                        "updated_at": "2024-11-19T23:44:22.000000Z",
                        "pivot": {
                            "role_id": 9,
                            "permission_id": 13
                        }
                    }
                ]
            }
        ],
        "permissions": []
    }
}
```

### DASHBOARD COUNTS

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

### DASHBOARD CART EMPLOYEE

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

### DASHBOARD LIST ATTENDANCE EMPLOYEE

GET
/attendance/list

Params
```
{
    'page_size' : 10,
    'page'  : 1,
    'keyword' : '123456',
    'shift' : 1,
    'status_checkin' : 1,
    'status_sheckout' : 1,
    'company' : 1,
    'start_date' : '2024-11-12',
    'end_date' : '2024-11-21'
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
                "id": 92,
                "user_id": 37,
                "time_check_in": "2024-11-23 23:25:04",
                "time_check_out": "2024-11-23 23:25:40",
                "time_total": "00:00:42",
                "status_check_in": 2,
                "status_check_out": 2,
                "created_at": "2024-11-23T16:25:11.000000Z",
                "updated_at": "2024-11-23T16:25:46.000000Z",
                "user": {
                    "id": 37,
                    "name": "ANTO",
                    "email": "anto@gmail.com",
                    "email_verified_at": null,
                    "pin": "2021052",
                    "shift_id": null,
                    "created_at": "2024-11-23T16:25:11.000000Z",
                    "updated_at": "2024-11-23T16:25:11.000000Z",
                    "nip": null,
                    "type_employee_id": 1,
                    "company_id": null,
                    "status": 1,
                    "shift": null,
                    "company": null,
                    "type": {
                        "id": 1,
                        "name": "Tetap",
                        "created_at": "2024-11-18T01:02:30.000000Z",
                        "updated_at": "2024-11-18T01:02:30.000000Z"
                    }
                }
            },
            {
                "id": 93,
                "user_id": 36,
                "time_check_in": "2024-11-25 10:24:57",
                "time_check_out": null,
                "time_total": "",
                "status_check_in": 2,
                "status_check_out": null,
                "created_at": "2024-11-23T16:25:01.000000Z",
                "updated_at": "2024-11-23T16:25:36.000000Z",
                "user": {
                    "id": 36,
                    "name": "CANDRA",
                    "email": "candra@gmail.com",
                    "email_verified_at": null,
                    "pin": "515210549",
                    "shift_id": null,
                    "created_at": "2024-11-23T16:25:01.000000Z",
                    "updated_at": "2024-11-23T16:25:01.000000Z",
                    "nip": null,
                    "type_employee_id": 1,
                    "company_id": null,
                    "status": 1,
                    "shift": null,
                    "company": null,
                    "type": {
                        "id": 1,
                        "name": "Tetap",
                        "created_at": "2024-11-18T01:02:30.000000Z",
                        "updated_at": "2024-11-18T01:02:30.000000Z"
                    }
                }
            },
            {
                "id": 91,
                "user_id": 36,
                "time_check_in": "2024-11-23 23:24:57",
                "time_check_out": "2024-11-23 23:25:32",
                "time_total": "00:00:39",
                "status_check_in": 2,
                "status_check_out": 2,
                "created_at": "2024-11-23T16:25:01.000000Z",
                "updated_at": "2024-11-23T16:25:36.000000Z",
                "user": {
                    "id": 36,
                    "name": "CANDRA",
                    "email": "candra@gmail.com",
                    "email_verified_at": null,
                    "pin": "515210549",
                    "shift_id": null,
                    "created_at": "2024-11-23T16:25:01.000000Z",
                    "updated_at": "2024-11-23T16:25:01.000000Z",
                    "nip": null,
                    "type_employee_id": 1,
                    "company_id": null,
                    "status": 1,
                    "shift": null,
                    "company": null,
                    "type": {
                        "id": 1,
                        "name": "Tetap",
                        "created_at": "2024-11-18T01:02:30.000000Z",
                        "updated_at": "2024-11-18T01:02:30.000000Z"
                    }
                }
            },
            {
                "id": 90,
                "user_id": 35,
                "time_check_in": "2024-11-23 23:24:40",
                "time_check_out": "2024-11-23 23:25:19",
                "time_total": "00:00:46",
                "status_check_in": 2,
                "status_check_out": 2,
                "created_at": "2024-11-23T16:24:47.000000Z",
                "updated_at": "2024-11-23T16:25:26.000000Z",
                "user": {
                    "id": 35,
                    "name": "JO",
                    "email": "jo@gmail.com",
                    "email_verified_at": null,
                    "pin": "1319022",
                    "shift_id": null,
                    "created_at": "2024-11-23T16:24:47.000000Z",
                    "updated_at": "2024-11-23T16:24:47.000000Z",
                    "nip": null,
                    "type_employee_id": 1,
                    "company_id": null,
                    "status": 1,
                    "shift": null,
                    "company": null,
                    "type": {
                        "id": 1,
                        "name": "Tetap",
                        "created_at": "2024-11-18T01:02:30.000000Z",
                        "updated_at": "2024-11-18T01:02:30.000000Z"
                    }
                }
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
        "to": 4,
        "total": 4
    }
}
```

### EMPLOYEE LIST

GET
/employee/list

Params
```
{
    'page_size' : 10,
    'page' : 1,
    'keyword' : '',
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
                "id": 37,
                "name": "ANTO",
                "email": "anto@gmail.com",
                "email_verified_at": null,
                "pin": "2021052",
                "shift_id": null,
                "created_at": "2024-11-23T16:25:11.000000Z",
                "updated_at": "2024-11-23T16:25:11.000000Z",
                "nip": null,
                "type_employee_id": 1,
                "company_id": null,
                "status": 1,
                "type": {
                    "id": 1,
                    "name": "Tetap",
                    "created_at": "2024-11-18T01:02:30.000000Z",
                    "updated_at": "2024-11-18T01:02:30.000000Z"
                },
                "company": null,
                "shift": null
            },
            {
                "id": 36,
                "name": "CANDRA",
                "email": "candra@gmail.com",
                "email_verified_at": null,
                "pin": "515210549",
                "shift_id": null,
                "created_at": "2024-11-23T16:25:01.000000Z",
                "updated_at": "2024-11-23T16:25:01.000000Z",
                "nip": null,
                "type_employee_id": 1,
                "company_id": null,
                "status": 1,
                "type": {
                    "id": 1,
                    "name": "Tetap",
                    "created_at": "2024-11-18T01:02:30.000000Z",
                    "updated_at": "2024-11-18T01:02:30.000000Z"
                },
                "company": null,
                "shift": null
            },
            {
                "id": 35,
                "name": "JO",
                "email": "jo@gmail.com",
                "email_verified_at": null,
                "pin": "1319022",
                "shift_id": null,
                "created_at": "2024-11-23T16:24:47.000000Z",
                "updated_at": "2024-11-23T16:24:47.000000Z",
                "nip": null,
                "type_employee_id": 1,
                "company_id": null,
                "status": 1,
                "type": {
                    "id": 1,
                    "name": "Tetap",
                    "created_at": "2024-11-18T01:02:30.000000Z",
                    "updated_at": "2024-11-18T01:02:30.000000Z"
                },
                "company": null,
                "shift": null
            },
            {
                "id": 3,
                "name": "Employee",
                "email": "employee@gmail.com",
                "email_verified_at": null,
                "pin": "123456",
                "shift_id": 1,
                "created_at": "2024-11-11T21:35:58.000000Z",
                "updated_at": "2024-11-21T16:20:59.000000Z",
                "nip": null,
                "type_employee_id": 1,
                "company_id": 1,
                "status": 1,
                "type": {
                    "id": 1,
                    "name": "Tetap",
                    "created_at": "2024-11-18T01:02:30.000000Z",
                    "updated_at": "2024-11-18T01:02:30.000000Z"
                },
                "company": {
                    "id": 1,
                    "name": "GAMATARA",
                    "created_at": "2024-11-18T23:09:06.000000Z",
                    "updated_at": "2024-11-18T23:09:06.000000Z",
                    "status": 1
                },
                "shift": {
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
            },
            {
                "id": 1,
                "name": "Admin",
                "email": "admin@gmail.com",
                "email_verified_at": null,
                "pin": "123456",
                "shift_id": 2,
                "created_at": "2024-11-11T05:48:44.000000Z",
                "updated_at": "2024-11-21T16:19:19.000000Z",
                "nip": null,
                "type_employee_id": 1,
                "company_id": 1,
                "status": 2,
                "type": {
                    "id": 1,
                    "name": "Tetap",
                    "created_at": "2024-11-18T01:02:30.000000Z",
                    "updated_at": "2024-11-18T01:02:30.000000Z"
                },
                "company": {
                    "id": 1,
                    "name": "GAMATARA",
                    "created_at": "2024-11-18T23:09:06.000000Z",
                    "updated_at": "2024-11-18T23:09:06.000000Z",
                    "status": 1
                },
                "shift": {
                    "id": 2,
                    "name": "Testing Shift",
                    "early_check_in": "06:00:00",
                    "check_in": "07:00:00",
                    "late_check_in": "07:35:00",
                    "early_check_out": "12:00:00",
                    "check_out": "17:00:00",
                    "late_check_out": "17:01:00",
                    "created_at": "2024-11-13T21:37:58.000000Z",
                    "updated_at": "2024-11-13T21:37:58.000000Z"
                }
            },
            {
                "id": 2,
                "name": "Security",
                "email": "security@gmail.com",
                "email_verified_at": null,
                "pin": "123456",
                "shift_id": 2,
                "created_at": "2024-11-11T05:48:44.000000Z",
                "updated_at": "2024-11-21T16:20:21.000000Z",
                "nip": null,
                "type_employee_id": 1,
                "company_id": 1,
                "status": 2,
                "type": {
                    "id": 1,
                    "name": "Tetap",
                    "created_at": "2024-11-18T01:02:30.000000Z",
                    "updated_at": "2024-11-18T01:02:30.000000Z"
                },
                "company": {
                    "id": 1,
                    "name": "GAMATARA",
                    "created_at": "2024-11-18T23:09:06.000000Z",
                    "updated_at": "2024-11-18T23:09:06.000000Z",
                    "status": 1
                },
                "shift": {
                    "id": 2,
                    "name": "Testing Shift",
                    "early_check_in": "06:00:00",
                    "check_in": "07:00:00",
                    "late_check_in": "07:35:00",
                    "early_check_out": "12:00:00",
                    "check_out": "17:00:00",
                    "late_check_out": "17:01:00",
                    "created_at": "2024-11-13T21:37:58.000000Z",
                    "updated_at": "2024-11-13T21:37:58.000000Z"
                }
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/employee/list?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/employee/list?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/employee/list?page=1",
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
        "path": "http://127.0.0.1:8000/api/employee/list",
        "per_page": 10,
        "prev_page_url": null,
        "to": 6,
        "total": 6
    }
}
```

### EMPLOYEE STORE

POST
/employee/store

Body
```
{
    'nip' : 123456
    'name' : 'Employee Test',
    'email' : 'employeetest@gmail.com',
    'password' : 123456,
    'shift_id' : 2,
    'role' : 'Employee',
    'type_employee_id' : 1,
    'company_id' : 1
}
```

Response
```
{
    "success": true,
    "message": "Success Added Employee",
    "data": {
        "name": "Employee Test",
        "email": "employeetest@gmail.com",
        "shift_id": "2",
        "nip": "123456",
        "type_employee_id": "1",
        "company_id": "1",
        "updated_at": "2024-11-21T12:38:26.000000Z",
        "created_at": "2024-11-21T12:38:26.000000Z",
        "id": 20
    }
}
```

### EMPLOYEE DETAIL

GET
/employee/detail/{id}

Response
```
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Admin",
        "email": "admin@gmail.com",
        "email_verified_at": null,
        "pin": "123456",
        "shift_id": 2,
        "created_at": "2024-11-11T05:48:44.000000Z",
        "updated_at": "2024-11-21T16:19:19.000000Z",
        "nip": null,
        "type_employee_id": 1,
        "company_id": 1,
        "status": 2,
        "type": {
            "id": 1,
            "name": "Tetap",
            "created_at": "2024-11-18T01:02:30.000000Z",
            "updated_at": "2024-11-18T01:02:30.000000Z"
        },
        "company": {
            "id": 1,
            "name": "GAMATARA",
            "created_at": "2024-11-18T23:09:06.000000Z",
            "updated_at": "2024-11-18T23:09:06.000000Z",
            "status": 1
        },
        "shift": {
            "id": 2,
            "name": "Testing Shift",
            "early_check_in": "06:00:00",
            "check_in": "07:00:00",
            "late_check_in": "07:35:00",
            "early_check_out": "12:00:00",
            "check_out": "17:00:00",
            "late_check_out": "17:01:00",
            "created_at": "2024-11-13T21:37:58.000000Z",
            "updated_at": "2024-11-13T21:37:58.000000Z"
        },
        "roles": [
            {
                "id": 9,
                "name": "Super Admin",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:17.000000Z",
                "updated_at": "2024-11-20T07:57:57.000000Z",
                "pivot": {
                    "model_type": "App\\Models\\User",
                    "model_id": 1,
                    "role_id": 9
                }
            }
        ]
    }
}
```

### EMPLOYEE UPDATE

PUT
/employee/update/{id}

Body
```
{
    'nip' : 123456
    'name' : 'Employee Test Edit',
    'email' : 'employeetestedit@gmail.com',
    'password' : 123456,
    'shift_id' : 2,
    'role' : 'Employee',
    'type_employee_id' : 1,
    'company_id' : 1
}
```

Response
```
{
    "success": true,
    "message": "Success Updated Employee",
    "data": {
        "id": 20,
        "name": "Employee Test Edit",
        "email": "employeetestedit@gmail.com",
        "email_verified_at": null,
        "pin": null,
        "shift_id": 2,
        "created_at": "2024-11-21T12:38:26.000000Z",
        "updated_at": "2024-11-21T12:52:47.000000Z",
        "nip": "123456",
        "type_employee_id": "1",
        "company_id": "1",
        "status": 1
    }
}
```

### EMPLOYEE UPDATE STATUS

PUT
/employee/update_status

Body
```
{
    'ids' : [10,11,20],
    'status' : 2
}
```

Response
```
{
    "success": true,
    "message": "Success Update Status Employee",
    "data": [
        {
            "id": 10,
            "name": "Anto Wiranto",
            "email": "antowiranto@gmail.com",
            "email_verified_at": null,
            "pin": "3",
            "shift_id": null,
            "created_at": "2024-11-21T01:05:34.000000Z",
            "updated_at": "2024-11-21T13:08:04.000000Z",
            "nip": null,
            "type_employee_id": 1,
            "company_id": null,
            "status": 2
        },
        {
            "id": 11,
            "name": "khusnul",
            "email": "khusnul@gmail.com",
            "email_verified_at": null,
            "pin": "2",
            "shift_id": null,
            "created_at": "2024-11-21T01:10:16.000000Z",
            "updated_at": "2024-11-21T13:08:04.000000Z",
            "nip": null,
            "type_employee_id": 1,
            "company_id": null,
            "status": 2
        },
        {
            "id": 20,
            "name": "Employee Test Edit",
            "email": "employeetestedit@gmail.com",
            "email_verified_at": null,
            "pin": null,
            "shift_id": 2,
            "created_at": "2024-11-21T12:38:26.000000Z",
            "updated_at": "2024-11-21T13:08:04.000000Z",
            "nip": "123456",
            "type_employee_id": 1,
            "company_id": 1,
            "status": 2
        }
    ]
}
```

### EMPLOYEE UPDATE SHIFT

PUT
/employee/update_shift

Body
```
{
    'ids' : [10,11,20],
    'shift_id' : 1
}
```

Response
```
{
    "success": true,
    "message": "Success Update Shift Employee",
    "data": [
        {
            "id": 10,
            "name": "Anto Wiranto",
            "email": "antowiranto@gmail.com",
            "email_verified_at": null,
            "pin": "3",
            "shift_id": 1,
            "created_at": "2024-11-21T01:05:34.000000Z",
            "updated_at": "2024-11-21T13:12:42.000000Z",
            "nip": null,
            "type_employee_id": 1,
            "company_id": null,
            "status": 1
        },
        {
            "id": 11,
            "name": "khusnul",
            "email": "khusnul@gmail.com",
            "email_verified_at": null,
            "pin": "2",
            "shift_id": 1,
            "created_at": "2024-11-21T01:10:16.000000Z",
            "updated_at": "2024-11-21T13:12:42.000000Z",
            "nip": null,
            "type_employee_id": 1,
            "company_id": null,
            "status": 1
        },
        {
            "id": 20,
            "name": "Employee Test Edit",
            "email": "employeetestedit@gmail.com",
            "email_verified_at": null,
            "pin": null,
            "shift_id": 1,
            "created_at": "2024-11-21T12:38:26.000000Z",
            "updated_at": "2024-11-21T13:12:42.000000Z",
            "nip": "123456",
            "type_employee_id": 1,
            "company_id": 1,
            "status": 1
        }
    ]
}
```

### SHIFT LIST

GET
/shift/list

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
                "name": "Shift Ketika",
                "early_check_in": "11:41:00",
                "check_in": "15:43:00",
                "late_check_in": "13:42:00",
                "early_check_out": "11:42:00",
                "check_out": "14:42:00",
                "late_check_out": "13:42:00",
                "created_at": "2024-11-11T21:39:48.000000Z",
                "updated_at": "2024-11-11T21:39:48.000000Z"
            },
            {
                "id": 2,
                "name": "Testing Shift",
                "early_check_in": "06:00:00",
                "check_in": "07:00:00",
                "late_check_in": "07:35:00",
                "early_check_out": "12:00:00",
                "check_out": "17:00:00",
                "late_check_out": "17:01:00",
                "created_at": "2024-11-13T21:37:58.000000Z",
                "updated_at": "2024-11-13T21:37:58.000000Z"
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/shift/list?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/shift/list?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/shift/list?page=1",
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
        "path": "http://127.0.0.1:8000/api/shift/list",
        "per_page": 10,
        "prev_page_url": null,
        "to": 2,
        "total": 2
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

### ROLE

GET
/roles/list

Response
```
{
    "message": "Successfully get all roles",
    "roles": [
        {
            "id": 9,
            "name": "Super Admin",
            "guard_name": "api",
            "created_at": "2024-11-19T23:44:17.000000Z",
            "updated_at": "2024-11-19T23:59:54.000000Z"
        },
        {
            "id": 10,
            "name": "Security",
            "guard_name": "api",
            "created_at": "2024-11-19T23:44:23.000000Z",
            "updated_at": "2024-11-19T23:44:23.000000Z"
        },
        {
            "id": 11,
            "name": "Employee",
            "guard_name": "api",
            "created_at": "2024-11-19T23:51:13.000000Z",
            "updated_at": "2024-11-19T23:51:13.000000Z"
        }
    ]
}
```

### PERMISSION

GET
/roles/permissions

Response
```
{
    "message": "Successfully get all permissions",
    "permissions": {
        "": [
            {
                "id": 9,
                "name": "dashboard",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:17.000000Z",
                "updated_at": "2024-11-19T23:44:17.000000Z"
            },
            {
                "id": 10,
                "name": "employee",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:19.000000Z",
                "updated_at": "2024-11-19T23:44:19.000000Z"
            },
            {
                "id": 11,
                "name": "role",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:20.000000Z",
                "updated_at": "2024-11-19T23:44:20.000000Z"
            },
            {
                "id": 12,
                "name": "company",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:20.000000Z",
                "updated_at": "2024-11-19T23:44:20.000000Z"
            },
            {
                "id": 13,
                "name": "report",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:22.000000Z",
                "updated_at": "2024-11-19T23:44:22.000000Z"
            }
        ]
    }
}
```

### ROLE PER ID

GET
/roles/edit-role/{id}

Response
```
{
    "status": "Success",
    "role": {
        "id": 9,
        "name": "Super Admin",
        "guard_name": "api",
        "created_at": "2024-11-19T23:44:17.000000Z",
        "updated_at": "2024-11-19T23:59:54.000000Z",
        "permissions": [
            {
                "id": 9,
                "name": "dashboard",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:17.000000Z",
                "updated_at": "2024-11-19T23:44:17.000000Z",
                "pivot": {
                    "role_id": 9,
                    "permission_id": 9
                }
            },
            {
                "id": 10,
                "name": "employee",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:19.000000Z",
                "updated_at": "2024-11-19T23:44:19.000000Z",
                "pivot": {
                    "role_id": 9,
                    "permission_id": 10
                }
            },
            {
                "id": 11,
                "name": "role",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:20.000000Z",
                "updated_at": "2024-11-19T23:44:20.000000Z",
                "pivot": {
                    "role_id": 9,
                    "permission_id": 11
                }
            },
            {
                "id": 12,
                "name": "company",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:20.000000Z",
                "updated_at": "2024-11-19T23:44:20.000000Z",
                "pivot": {
                    "role_id": 9,
                    "permission_id": 12
                }
            },
            {
                "id": 13,
                "name": "report",
                "guard_name": "api",
                "created_at": "2024-11-19T23:44:22.000000Z",
                "updated_at": "2024-11-19T23:44:22.000000Z",
                "pivot": {
                    "role_id": 9,
                    "permission_id": 13
                }
            }
        ]
    }
}
```

### UPDATE ROLE

POST 
/roles/update-role/{id}

Body
```
{
    'name' : 'Admin'
}
```

Response
```
{
    "message": "Role updated successfully",
    "role": {
        "id": 9,
        "name": "Admin",
        "guard_name": "api",
        "created_at": "2024-11-19T23:44:17.000000Z",
        "updated_at": "2024-11-20T07:57:38.000000Z"
    }
}
```

### COMPANIES LIST

GET
/companies/list

Params
```
{
    'page_size' : 10,
    'page' : 1,
    'name' : 'GAMATARA'
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
                "name": "GAMATARA",
                "created_at": "2024-11-18T23:09:06.000000Z",
                "updated_at": "2024-11-18T23:09:06.000000Z"
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/companies/list?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/companies/list?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/companies/list?page=1",
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
        "path": "http://127.0.0.1:8000/api/companies/list",
        "per_page": 10,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```

### COMPANIES STORE

POST
/companies/store

BODY
```
{
    'name' : 'Shift 1'
}
```

Response
```
{
    "success": true,
    "message": "Success Added Company",
    "data": {
        "name": "Shift 1",
        "updated_at": "2024-11-21T12:20:18.000000Z",
        "created_at": "2024-11-21T12:20:18.000000Z",
        "id": 2
    }
}
```

### COMPANIES DETAIL

GET
/companies/detail/{id}

Response
```
{
    "success": true,
    "data": {
        "id": 2,
        "name": "Shift 1",
        "created_at": "2024-11-21T12:20:18.000000Z",
        "updated_at": "2024-11-21T12:20:18.000000Z"
    }
}
```

### COMPANIES UPDATE

PUT
/companies/update/{id}

Body
```
{
    'name' : 'Shift 1 Edit'
}
```

Response
```
{
    "success": true,
    "message": "Success Updated Company",
    "data": {
        "id": 2,
        "name": "Shift 1",
        "created_at": "2024-11-21T12:20:18.000000Z",
        "updated_at": "2024-11-21T12:20:18.000000Z"
    }
}F
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
    'most_late' : true,
    'month' : '2024-11'
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
    'keyword' : '',
    'most_present' : true,
    'smallest_present' : true,
    'longest_duration' : true,
    'shortest_duration' : true,
    'year' : '2024'
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

### GUEST LIST

GET
/guest/list

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
                "id": 10,
                "name": "Lesmana",
                "phone_number": "081234567890",
                "created_at": "2024-11-25T05:28:50.000000Z",
                "updated_at": "2024-11-25T05:28:50.000000Z",
                "nik": "123456"
            },
            {
                "id": 6,
                "name": "ANTO ANTO",
                "phone_number": "0813838383",
                "created_at": "2024-11-22T08:42:12.000000Z",
                "updated_at": "2024-11-22T08:42:12.000000Z",
                "nik": null
            },
            {
                "id": 5,
                "name": "ANTO ANTO",
                "phone_number": "028282828",
                "created_at": "2024-11-22T08:17:33.000000Z",
                "updated_at": "2024-11-22T08:17:33.000000Z",
                "nik": null
            },
            {
                "id": 4,
                "name": "TEST",
                "phone_number": "92228282822",
                "created_at": "2024-11-22T08:16:35.000000Z",
                "updated_at": "2024-11-22T08:16:35.000000Z",
                "nik": null
            },
            {
                "id": 3,
                "name": "Anto Wiranto",
                "phone_number": "08282828282",
                "created_at": "2024-11-22T08:10:44.000000Z",
                "updated_at": "2024-11-22T08:10:44.000000Z",
                "nik": null
            },
            {
                "id": 1,
                "name": "Candra",
                "phone_number": "081234567890",
                "created_at": "2024-11-19T22:06:44.000000Z",
                "updated_at": "2024-11-19T22:06:44.000000Z",
                "nik": null
            },
            {
                "id": 2,
                "name": "Adut",
                "phone_number": "081234567890",
                "created_at": "2024-11-19T22:06:44.000000Z",
                "updated_at": "2024-11-19T22:06:44.000000Z",
                "nik": null
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/guest/list?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/guest/list?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/guest/list?page=1",
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
        "path": "http://127.0.0.1:8000/api/guest/list",
        "per_page": 10,
        "prev_page_url": null,
        "to": 7,
        "total": 7
    }
}
```

### ATTENDANCE GUEST LIST

GET 
/attendance_guest/list

Params
```
{
    'page_size' : 10,
    'page' : 1,
    'keyword' : '',
    'start_date' : 2024-11-20',
    'end_date' : 2024-11-25
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
                "id": 8,
                "guest_id": 10,
                "institution": "GOJEK",
                "time_check_in": "2024-11-25 12:28:51",
                "time_check_out": "2024-11-25 12:35:02",
                "need": "Test Absen Guest",
                "created_at": "2024-11-25T05:28:51.000000Z",
                "updated_at": "2024-11-25T05:35:03.000000Z",
                "type_vehicle": "Mobil",
                "no_police": "B4716UR",
                "total_guest": 2,
                "duration": "12:41:13",
                "guest": {
                    "id": 10,
                    "name": "Lesmana",
                    "phone_number": "081234567890",
                    "created_at": "2024-11-25T05:28:50.000000Z",
                    "updated_at": "2024-11-25T05:28:50.000000Z",
                    "nik": "123456"
                }
            },
            {
                "id": 7,
                "guest_id": 6,
                "institution": "GOJEK",
                "time_check_in": "2024-11-22 15:42:13",
                "time_check_out": "2024-11-25 12:31:41",
                "need": "Untuk meeting",
                "created_at": "2024-11-22T08:42:13.000000Z",
                "updated_at": "2024-11-25T05:31:42.000000Z",
                "type_vehicle": null,
                "no_police": null,
                "total_guest": null,
                "duration": "68:49:28",
                "guest": {
                    "id": 6,
                    "name": "ANTO ANTO",
                    "phone_number": "0813838383",
                    "created_at": "2024-11-22T08:42:12.000000Z",
                    "updated_at": "2024-11-22T08:42:12.000000Z",
                    "nik": null
                }
            },
            {
                "id": 6,
                "guest_id": 5,
                "institution": "JIKA YA",
                "time_check_in": "2024-11-22 15:17:34",
                "time_check_out": "2024-11-22 15:17:44",
                "need": "Untuk meeting",
                "created_at": "2024-11-22T08:17:34.000000Z",
                "updated_at": "2024-11-22T08:17:44.000000Z",
                "type_vehicle": null,
                "no_police": null,
                "total_guest": null,
                "duration": null,
                "guest": {
                    "id": 5,
                    "name": "ANTO ANTO",
                    "phone_number": "028282828",
                    "created_at": "2024-11-22T08:17:33.000000Z",
                    "updated_at": "2024-11-22T08:17:33.000000Z",
                    "nik": null
                }
            },
            {
                "id": 5,
                "guest_id": 4,
                "institution": null,
                "time_check_in": "2024-11-22 15:16:36",
                "time_check_out": "2024-11-22 15:21:19",
                "need": "EETING",
                "created_at": "2024-11-22T08:16:36.000000Z",
                "updated_at": "2024-11-22T08:21:19.000000Z",
                "type_vehicle": null,
                "no_police": null,
                "total_guest": null,
                "duration": null,
                "guest": {
                    "id": 4,
                    "name": "TEST",
                    "phone_number": "92228282822",
                    "created_at": "2024-11-22T08:16:35.000000Z",
                    "updated_at": "2024-11-22T08:16:35.000000Z",
                    "nik": null
                }
            },
            {
                "id": 4,
                "guest_id": 3,
                "institution": null,
                "time_check_in": "2024-11-22 15:10:46",
                "time_check_out": "2024-11-22 15:21:23",
                "need": "Untuk meeting",
                "created_at": "2024-11-22T08:10:46.000000Z",
                "updated_at": "2024-11-22T08:21:23.000000Z",
                "type_vehicle": null,
                "no_police": null,
                "total_guest": null,
                "duration": null,
                "guest": {
                    "id": 3,
                    "name": "Anto Wiranto",
                    "phone_number": "08282828282",
                    "created_at": "2024-11-22T08:10:44.000000Z",
                    "updated_at": "2024-11-22T08:10:44.000000Z",
                    "nik": null
                }
            },
            {
                "id": 1,
                "guest_id": 1,
                "institution": "Corporation",
                "time_check_in": "2024-11-20 10:07:39",
                "time_check_out": "2024-11-20 14:07:39",
                "need": "Meeting",
                "created_at": "2024-11-19T22:07:39.000000Z",
                "updated_at": "2024-11-19T22:07:39.000000Z",
                "type_vehicle": "Mobil",
                "no_police": "B1234UV",
                "total_guest": 2,
                "duration": "04:00:00",
                "guest": {
                    "id": 1,
                    "name": "Candra",
                    "phone_number": "081234567890",
                    "created_at": "2024-11-19T22:06:44.000000Z",
                    "updated_at": "2024-11-19T22:06:44.000000Z",
                    "nik": null
                }
            },
            {
                "id": 2,
                "guest_id": 2,
                "institution": "Corporation",
                "time_check_in": "2024-11-20 10:07:39",
                "time_check_out": "2024-11-20 15:07:39",
                "need": "Meeting",
                "created_at": "2024-11-19T22:07:39.000000Z",
                "updated_at": "2024-11-19T22:07:39.000000Z",
                "type_vehicle": "Mobil",
                "no_police": "B1234UV",
                "total_guest": 2,
                "duration": "05:00:00",
                "guest": {
                    "id": 2,
                    "name": "Adut",
                    "phone_number": "081234567890",
                    "created_at": "2024-11-19T22:06:44.000000Z",
                    "updated_at": "2024-11-19T22:06:44.000000Z",
                    "nik": null
                }
            },
            {
                "id": 3,
                "guest_id": 2,
                "institution": "Corporation",
                "time_check_in": "2023-12-20 10:07:39",
                "time_check_out": "2023-12-20 15:07:39",
                "need": "Meeting",
                "created_at": "2023-11-19T22:07:39.000000Z",
                "updated_at": "2023-11-19T22:07:39.000000Z",
                "type_vehicle": "Mobil",
                "no_police": "B1234UV",
                "total_guest": 2,
                "duration": "05:00:00",
                "guest": {
                    "id": 2,
                    "name": "Adut",
                    "phone_number": "081234567890",
                    "created_at": "2024-11-19T22:06:44.000000Z",
                    "updated_at": "2024-11-19T22:06:44.000000Z",
                    "nik": null
                }
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/attendance_guest/list?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/attendance_guest/list?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/attendance_guest/list?page=1",
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
        "path": "http://127.0.0.1:8000/api/attendance_guest/list",
        "per_page": 10,
        "prev_page_url": null,
        "to": 8,
        "total": 8
    }
}
```

### CHECK IN ATTENDANCE GUEST

POST
/attendance_guest/store

Body
```
{
    'guest_id' : 2,
    'nik' : 123456,
    'name' : 'Candra',
    'phone_number' : '081234567890',
    'institution' : 'GOJEK',
    'type_vehicle' : 'Mobil',
    'no_police' : 'B4716UR',
    'total_guest' : 2,
    'need' : 'Test Absen Guest'
}
```

Note: Jika guest sudah ada maka bisa pakai id guest

Response
```
{
    "success": true,
    "message": "Success Added Guest And Attendance",
    "data": {
        "guest": {
            "nik": 123456,
            "name": "Lesmana",
            "phone_number": "081234567890",
            "updated_at": "2024-11-25T05:28:50.000000Z",
            "created_at": "2024-11-25T05:28:50.000000Z",
            "id": 10
        },
        "attendace": {
            "guest_id": 10,
            "institution": "GOJEK",
            "time_check_in": "2024-11-25 12:28:51",
            "need": "Test Absen Guest",
            "type_vehicle": "Mobil",
            "no_police": "B4716UR",
            "total_guest": 2,
            "updated_at": "2024-11-25T05:28:51.000000Z",
            "created_at": "2024-11-25T05:28:51.000000Z",
            "id": 8
        }
    }
}
```

### CHECK OUT ATTENDANCE GUEST

PUT
/attendance_guest/update/{id}

Response
```
{
    "success": true,
    "message": "Success Update Attendance",
    "data": {
        "id": 8,
        "guest_id": 10,
        "institution": "GOJEK",
        "time_check_in": "2024-11-25 12:28:51",
        "time_check_out": "2024-11-25 12:35:02",
        "need": "Test Absen Guest",
        "created_at": "2024-11-25T05:28:51.000000Z",
        "updated_at": "2024-11-25T05:35:03.000000Z",
        "type_vehicle": "Mobil",
        "no_police": "B4716UR",
        "total_guest": 2,
        "duration": "12:41:13"
    }
}
```

### GET NOTIFICATION

GET
/notif

Response
```
{
    "success": true,
    "data": [
        {
            "user_id": 36,
            "message": "Hi CANDRA, Sudah Saatnya Absen Keluar"
        }
    ]
}
```