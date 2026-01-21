<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockOpnameExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private ?string $from;
    private ?string $to;
    private int $rowNumber = 0;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from ?: null;
        $this->to = $to ?: null;
    }

    public function collection(): Collection
    {
        $salesAgg = Sale::query()
            ->select('product_id', DB::raw('SUM(quantity) as qty_sold'))
            ->when($this->from, function ($q) {
                $q->whereDate('transaction_date', '>=', $this->from);
            })
            ->when($this->to, function ($q) {
                $q->whereDate('transaction_date', '<=', $this->to);
            })
            ->groupBy('product_id');

        return Product::query()
            ->leftJoinSub($salesAgg, 'sales_agg', function ($join) {
                $join->on('products.id', '=', 'sales_agg.product_id');
            })
            ->select('products.*', DB::raw('COALESCE(sales_agg.qty_sold, 0) as qty_sold'))
            ->orderBy('products.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Produk',
            'Satuan',
            'Qty Terjual',
            'Stok Saat Ini',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->name,
            strtoupper((string) $row->unit_type),
            (int) $row->qty_sold,
            (int) $row->stock,
        ];
    }

    public function title(): string
    {
        return 'Stock Opname';
    }
}
