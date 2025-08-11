<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Amenities;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AmenitiesExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterStatuses;
    protected $filterBookingRequired;

    public function __construct($search, $filterStatuses = [], $filterBookingRequired = [])
    {
        $this->search = $search;
        $this->filterStatuses = $filterStatuses;
        $this->filterBookingRequired = $filterBookingRequired;
    }

    public function headings(): array
    {
        return [
            __('modules.settings.societyAmenitiesName'),
            __('modules.settings.societyAmenitiesStatus'),
            __('modules.settings.societySlotTiming'),
            __('modules.visitorManagement.inTime'),
            __('modules.visitorManagement.outTime'),
            __('modules.settings.societyMultipleBooking'),
            __('modules.settings.numberOfPerson'),
        ];
    }

    public function map($amenities): array
    {
        return [
            $amenities->amenities_name,
            $amenities->status,
            $amenities->slot_time ? $amenities->slot_time . " Min" : '--' ,
            $amenities->start_time ? Carbon::parse($amenities->start_time)->format('h:i A') : '--',
            $amenities->end_time ? Carbon::parse($amenities->end_time)->format('h:i A') : '--',
            $amenities->multiple_booking_status == 1 ? 'Yes' : ($amenities->multiple_booking_status === null ? '--' : 'No') ,
            $amenities->number_of_person ?? '--'

        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle
            ->getFont()
            ->setName('Arial');
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'name' => 'Arial',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'f5f5f5'],
                ],
            ],
        ];
    }

    public function collection()
    {
        $query = Amenities::query();

        if (!empty($this->search)) {
            $query->where('amenities_name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterStatuses)) {
            $query->whereIn('status', $this->filterStatuses);
        }

        if (!empty($this->filterBookingRequired)) {
            $query->whereIn('booking_status', $this->filterBookingRequired);
        }

        return $query->get();
    }

}
