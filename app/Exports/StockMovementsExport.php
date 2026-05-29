<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockMovementsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return StockMovement::query()
            ->with(['product', 'location', 'actor'])
            ->when($this->filters['start_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($this->filters['end_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when($this->filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($this->filters['location_id'] ?? null, fn ($q, $id) => $q->where('location_id', $id))
            ->latest('created_at');
    }

    public function headings(): array
    {
        return [
            ['HMS - STOCK MOVEMENT REPORT'],
            ['Generated: ' . now()->format('d/m/Y H:i')],
            [],
            ['DATE/TIME', 'PRODUCT', 'LOCATION', 'TYPE', 'QTY', 'BEFORE', 'AFTER', 'REFERENCE', 'BY'],
        ];
    }

    public function map($movement): array
    {
        $reference = '—';
        if ($movement->reference_type && $movement->reference_id) {
            $reference = class_basename($movement->reference_type) . ' #' . $movement->reference_id;
        }

        return [
            $movement->created_at->format('d/m/Y H:i'),
            $movement->product->name ?? '',
            $movement->location->name ?? '',
            strtoupper(str_replace('_', ' ', $movement->type)),
            $movement->quantity,
            $movement->quantity_before,
            $movement->quantity_after,
            $reference,
            $movement->actor->name ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E79'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Stock Movements';
    }
}
