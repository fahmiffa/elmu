<?php

namespace App\Exports;

use App\Models\Head;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $grade;
    protected $kelas;
    protected $unit;
    protected $program;

    public function __construct($grade = null, $kelas = null, $unit = null, $program = null)
    {
        $this->grade   = $grade;
        $this->kelas   = $kelas;
        $this->unit    = $unit;
        $this->program = $program;
    }

    public function query()
    {
        $query = Student::query()
            ->with([
                'users:id,nomor',
                'grade:id,name',
                'reg' => function ($q) {
                    $q->where('done', 0)->with([
                        'units:id,name',
                        'programs:id,name',
                        'class:id,name',
                    ]);
                }
            ]);

        // Filter berdasarkan grade (jenjang)
        if (!empty($this->grade)) {
            $query->where('grade_id', $this->grade);
        }

        // Filter berdasarkan unit, kelas, atau program (melalui relasi Head)
        if (!empty($this->unit) || !empty($this->kelas) || !empty($this->program)) {
            $query->whereHas('reg', function ($q) {
                $q->where('done', 0);
                if (!empty($this->unit))    $q->where('unit', $this->unit);
                if (!empty($this->kelas))   $q->where('kelas', $this->kelas);
                if (!empty($this->program)) $q->where('program', $this->program);
            });
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            ['DATA MURID'],
            ['Diekspor pada: ' . now()->translatedFormat('l, d F Y H:i')],
            [''],
            [
                'No',
                'Nama Lengkap',
                'Nama Panggilan',
                'Jenjang',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Umur',
                'Agama',
                'Jenis Kelamin',
                'Kelas Sekolah',
                'Alamat Rumah',
                'Alamat Sekolah',
                'Nama Ayah',
                'Pekerjaan Ayah',
                'Nama Ibu',
                'Pekerjaan Ibu',
                'No. HP Ortu',
                'Sosmed Murid',
                'Unit',
                'Program',
                'Kelas Kursus',
            ],
        ];
    }

    public function map($student): array
    {
        static $no = 0;
        $no++;

        $activeReg = $student->reg->where('done', 0)->first();

        return [
            $no,
            $student->name,
            $student->nama_panggilan,
            $student->grade->name ?? '-',
            $student->place,
            $student->birth ? \Carbon\Carbon::parse($student->birth)->format('d/m/Y') : '-',
            $student->birth ? \Carbon\Carbon::parse($student->birth)->age . ' Tahun' : '-',
            $student->agama,
            $student->gender == 1 ? 'Laki-laki' : ($student->gender == 2 ? 'Perempuan' : '-'),
            $student->sekolah_kelas,
            $student->alamat,
            $student->alamat_sekolah,
            $student->dad,
            $student->dadJob,
            $student->mom,
            $student->momJob,
            $student->hp_parent,
            $student->sosmedChild,
            $activeReg?->units?->name ?? '-',
            $activeReg?->programs?->name ?? '-',
            $activeReg?->class?->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:U1');
        $sheet->mergeCells('A2:U2');

        $lastRow = $sheet->getHighestRow();

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '6B7280']]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EA580C'], // Orange-600
                ],
            ],
        ];
    }
}
