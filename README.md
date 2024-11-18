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
        "type_employee": "1",
        "date_start": "2024-11-12",
        "date_end": "2024-11-18"
    },
    "var": [
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
        "Minggu",
        "Senin"
    ],
    "val": [
        3,
        0,
        0,
        0,
        0,
        0,
        0
    ]
}
```