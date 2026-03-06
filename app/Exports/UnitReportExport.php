<?php

namespace App\Exports;

use App\Models\Unit;
use App\Models\Head;
use App\Models\Student;
use App\Models\Paid;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $bulan;
    protected $tahun;
    protected $bulanName;

    public function __construct($bulan, $tahun, $bulanName)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->bulanName = $bulanName;
    }

    public function collection()
    {
        $query = Unit::query();
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role == 4) {
            $unitIds = \App\Models\Zone_units::where('zone_id', \Illuminate\Support\Facades\Auth::user()->zone_id)->pluck('unit_id');
            $query->whereIn('id', $unitIds);
        }

        $data = $query->get()->map(function ($unit) {
            $headIds = Head::where('unit', $unit->id)->where('done', 0)->pluck('id');

            $unit->total_siswa = Student::whereHas('reg', function ($q) use ($unit) {
                $q->where('unit', $unit->id)->where('done', 0);
            })->count();

            // Total Monthly Payment
            $paidMonthly = Paid::whereIn('head', $headIds)
                ->where('bulan', $this->bulan)
                ->where('tahun', $this->tahun)
                ->get();

            $unit->paid_monthly = $paidMonthly->where('status', 1)->sum(function ($p) {
                return $p->total;
            });
            $unit->unpaid_monthly = $paidMonthly->where('status', '!=', 1)->sum(function ($p) {
                return $p->total;
            });

            // Total Service Payment
            $paidService = Order::whereIn('head', $headIds)
                ->whereMonth('created_at', $this->bulan)
                ->whereYear('created_at', $this->tahun)
                ->with('product')
                ->get();

            $unit->paid_service = $paidService->where('status', 1)->sum(function ($o) {
                return $o->product->harga ?? 0;
            });
            $unit->unpaid_service = $paidService->where('status', '!=', 1)->sum(function ($o) {
                return $o->product->harga ?? 0;
            });

            return $unit;
        });

        // Add Total Row
        $data->push((object)[
            'name'           => 'TOTAL KESELURUHAN',
            'total_siswa'    => $data->sum('total_siswa'),
            'paid_monthly'   => $data->sum('paid_monthly'),
            'unpaid_monthly' => $data->sum('unpaid_monthly'),
            'paid_service'   => $data->sum('paid_service'),
            'unpaid_service' => $data->sum('unpaid_service'),
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PEMBAYARAN & LAYANAN PER UNIT'],
            ['Periode:', $this->bulanName . ' ' . $this->tahun],
            [''],
            [
                'Unit',
                'Total Siswa',
                'Bulanan (Lunas)',
                'Bulanan (Belum)',
                'Layanan (Lunas)',
                'Layanan (Belum)',
                'Total Bayar'
            ]
        ];
    }

    public function map($unit): array
    {
        return [
            $unit->name,
            $unit->total_siswa,
            $unit->paid_monthly,
            $unit->unpaid_monthly,
            $unit->paid_service,
            $unit->unpaid_service,
            ($unit->paid_monthly + $unit->paid_service)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('B2:G2');

        $lastRow = $sheet->getHighestRow();

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EA580C'] // Orange-600
                ]
            ],
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF1EA'] // Light orange
                ]
            ]
        ];
    }
}
