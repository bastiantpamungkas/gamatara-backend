# GAMATARA BACKEND



## API SPEK

### Dashboard COUNTS

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