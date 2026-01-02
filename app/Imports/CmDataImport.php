<?php

namespace App\Imports;

use App\Models\CmData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CmDataImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected int $areaId;
    protected int $insertedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;

    public function __construct(int $areaId)
    {
        $this->areaId = $areaId;
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
            $existing = CmData::where('cm', $cm)
                ->where('container', $container)
                ->first();

            $data = [
                'ppcw' => $row['ppcw'] ?? null,
                'container' => $container,
                'seal' => $row['seal'] ?? null,
                'shipper' => $row['shipper'] ?? null,
                'consignee' => $row['consignee'] ?? null,
                'status' => $row['status'] ?? null,
                'commodity' => $row['commodity'] ?? null,
                'size' => $row['size'] ?? null,
                'weight' => $row['weight'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
                'cm' => $cm,
                'atd' => $this->parseDate($row['atd'] ?? null),
                'no_order_coins' => $row['no_order_coins'] ?? $row['no order coins'] ?? null,
                'area_id' => $this->areaId,
                'imported_by' => Auth::id(),
            ];

            if ($existing) {
                // Update existing record
                $existing->update($data);
                $this->updatedCount++;
            } else {
                // Create new record
                CmData::create($data);
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
