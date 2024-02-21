<?php

namespace App\Exports;

use App\Models\DokPersiapan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PersiapanExport implements FromArray, WithHeadings, WithTitle
{
    protected $dp_dkh;

    public function __construct(array $dp_dkh)
    {
        $this->dp_dkh = $dp_dkh;
    }

    public function array(): array
    {
        return $this->dp_dkh;
    }
    
    public function headings(): array {
        return ['Jenis Barang/Jasa','Satuan','Vol','Harga','Pajak(%)','Keterangan'];
    }
    
    public function title(): string {
        return 'rincian-hps';
    }
}
