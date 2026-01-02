<?php

namespace App\Imports;

use App\Models\CoinsData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CoinsDataImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected int $wilayahId;
    protected int $insertedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;

    public function __construct(int $wilayahId)
    {
        $this->wilayahId = $wilayahId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $cm = $row['cm'] ?? null;
            $container = $row['container'] ?? null;

            // Skip rows without cm or container
            if (empty($cm) && empty($container)) {
                $this->skippedCount++;
                continue;
            }

            // Check for existing record based on cm + container
            $existing = CoinsData::where('cm', $cm)
                ->where('container', $container)
                ->first();

            $data = [
                'cm' => $cm,
                'order' => $row['order'] ?? null,
                'container' => $container,
                'seal' => $row['seal'] ?? null,
                'size_20' => $row['20'] ?? $row['size_20'] ?? null,
                'size_40' => $row['40'] ?? $row['size_40'] ?? null,
                'no_po' => $row['no_po'] ?? $row['no po'] ?? null,
                'kereta' => $row['kereta'] ?? null,
                'atd' => $this->parseDate($row['atd'] ?? null),
                'customer' => $row['customer'] ?? null,
                'stasiun_asal' => $row['stasiun_asal'] ?? $row['stasiun asal'] ?? null,
                'stasiun_tujuan' => $row['stasiun_tujuan'] ?? $row['stasiun tujuan'] ?? null,
                'gudang_asal' => $row['gudang_asal'] ?? $row['gudang asal'] ?? null,
                'gudang_tujuan' => $row['gudang_tujuan'] ?? $row['gudang tujuan'] ?? null,
                'jenis' => $row['jenis'] ?? null,
                'service' => $row['service'] ?? null,
                'payment' => $row['payment'] ?? null,
                'so' => $row['so'] ?? null,
                'submit_so' => $this->parseDate($row['submit_so'] ?? $row['submit so'] ?? null),
                'nominal_ppn' => $row['nominal_ppn'] ?? $row['nominal ppn'] ?? null,
                'sa_ppn' => $row['sa_ppn'] ?? $row['sa ppn'] ?? null,
                'loading_ppn' => $row['loading_ppn'] ?? $row['loading ppn'] ?? null,
                'unloading_ppn' => $row['unloading_ppn'] ?? $row['unloading ppn'] ?? null,
                't_orig_ppn' => $row['t_orig_ppn'] ?? $row['t orig ppn'] ?? null,
                't_dest_ppn' => $row['t_dest_ppn'] ?? $row['t dest ppn'] ?? null,
                'sa' => $row['sa'] ?? null,
                'loading' => $row['loading'] ?? null,
                'unloading' => $row['unloading'] ?? null,
                't_orig' => $row['t_orig'] ?? $row['t orig'] ?? null,
                't_dest' => $row['t_dest'] ?? $row['t dest'] ?? null,
                'nominal' => $row['nominal'] ?? null,
                'klaim' => $row['klaim'] ?? null,
                'dokumen_klaim' => $row['dokumen_klaim'] ?? $row['dokumen klaim'] ?? null,
                'alur' => $row['alur'] ?? null,
                'dokumen' => $row['dokumen'] ?? null,
                'berat' => $row['berat'] ?? null,
                'isi_barang' => $row['isi_barang'] ?? $row['isi barang'] ?? null,
                'ppcw' => $row['ppcw'] ?? null,
                'owner' => $row['owner'] ?? null,
                'wilayah_id' => $this->wilayahId,
                'imported_by' => Auth::id(),
            ];

            if ($existing) {
                // Update existing record
                $existing->update($data);
                $this->updatedCount++;
            } else {
                // Create new record
                CoinsData::create($data);
                $this->insertedCount++;
            }
        }
    }

    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            // Excel serial date
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'cm' => 'nullable|string',
            'container' => 'nullable|string',
        ];
    }

    public function getInsertedCount(): int
    {
        return $this->insertedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
