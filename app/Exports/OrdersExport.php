<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements FromCollection, WithHeadings, WithColumnFormatting, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $dataCount;
    public function collection()
    {
        $orders = Order::with(['user', 'cart.cartItems.product'])->get();
        $orders = $orders->map(function ($order) {
            return [
                $order->id,
                $order->user->name,
                $order->is_shipped,
                $order->cart->cartItems->sum(function ($cartItem)
                {
                    return $cartItem->product->price * $cartItem->quantity;
                }),
                $order->created_at
            ];
        });
        $this->dataCount = $orders->count(); // 紀錄有幾筆資料
        return $orders;
    }

    public function headings(): array
    {
        // return Schema::getColumnListing('orders');  // 取得column
        return ['編號', '購買者', '是否運送', '總價', '建立時間'];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(50);
                for ($i=0; $i < $this->dataCount; $i++) { 
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(20);
                }
                $event->sheet->getDelegate()->getStyle('A1:E'.$this->dataCount)->getAlignment()->setVertical('center'); // 置中
                $event->sheet->getDelegate()->getStyle('A1:E'.$this->dataCount + 1)->applyFromArray([
                    'font' => [
                        'name' => 'Arial', // 字型
                        'bold' => true, // 粗度
                        'italic' => true, // 斜度
                        'color' => [
                            'rgb' => 'FF0000'
                        ]
                    ],
                    'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => '000000'
                            ],
                            'endColor' => [
                                'rgb' => '000000'
                            ]
                        ]
                ]);
                $event->sheet->getDelegate()->mergeCells('G1:H1'); // 合併儲存格
            }
        ];
    }
}
