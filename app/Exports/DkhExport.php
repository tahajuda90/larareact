<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DkhExport implements FromArray, WithHeadings, WithTitle
{
    protected $dkh;
    
    public function __construct(array $dkh)
    {
        $this->dkh = $dkh;
    }

    public function array(): array
    {
        return $this->dkh;
    }
    
    public function headings(): array {
        return ['Jenis Barang/Jasa','Satuan','Vol','Harga','Pajak(%)','Keterangan'];
    }
    
    public function title(): string {
        return 'rincian-hps';
    }
}
