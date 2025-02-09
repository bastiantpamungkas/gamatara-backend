<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceGuestReport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {   
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            "Nama Tamu",
            "Tanggal Kunjungan",
            "Jumlah Tamu",
            "Waktu Kunjungan",
            "Institusi"
        ];
    }
}