<?php

namespace App\Exports;

use App\Models\CoinsData;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonitoringPoExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return CoinsData::query()->withMatchingCm();
    }

    public function headings(): array
    {
        return [
            'ID', 'CM', 'Order', 'Container', 'Seal', 'Size 20', 'Size 40', 'No PO', 'Kereta', 'ATD',
            'Customer', 'Stasiun Asal', 'Stasiun Tujuan', 'Gudang Asal', 'Gudang Tujuan', 'Jenis',
            'Service', 'Payment', 'SO', 'Submit SO', 'Nominal PPN', 'SA PPN', 'Loading PPN',
            'Unloading PPN', 'T Orig PPN', 'T Dest PPN', 'SA', 'Loading', 'Unloading', 'T Orig',
            'T Dest', 'Nominal', 'Klaim', 'Dokumen Klaim', 'Alur', 'Dokumen', 'Berat', 'Isi Barang',
            'PPCW', 'Owner', 'Wilayah ID', 'Imported By', 'Created At', 'Updated At', 'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->cm,
            $row->order,
            $row->container,
            $row->seal,
            $row->size_20,
            $row->size_40,
            $row->no_po,
            $row->kereta,
            $row->atd ? $row->atd->format('Y-m-d') : '',
            $row->customer,
            $row->stasiun_asal,
            $row->stasiun_tujuan,
            $row->gudang_asal,
            $row->gudang_tujuan,
            $row->jenis,
            $row->service,
            $row->payment,
            $row->so,
            $row->submit_so ? $row->submit_so->format('Y-m-d') : '',
            $row->nominal_ppn,
            $row->sa_ppn,
            $row->loading_ppn,
            $row->unloading_ppn,
            $row->t_orig_ppn,
            $row->t_dest_ppn,
            $row->sa,
            $row->loading,
            $row->unloading,
            $row->t_orig,
            $row->t_dest,
            $row->nominal,
            $row->klaim,
            $row->dokumen_klaim,
            $row->alur,
            $row->dokumen,
            $row->berat,
            $row->isi_barang,
            $row->ppcw,
            $row->owner,
            $row->wilayah_id,
            $row->imported_by,
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '',
            $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '',
            'Matched',
        ];
    }
}
