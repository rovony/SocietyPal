<?php

namespace App\Exports;

use App\Models\BookAmenity;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookAmenitiesExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $filterUsers;
    protected $filterAmenities;

    public function __construct($search, $startDate, $endDate, $filterUsers = [], $filterAmenities = [])
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->filterUsers = $filterUsers;
        $this->filterAmenities = $filterAmenities;
    }

    public function headings(): array
    {
        return [
            __('modules.bookAmenity.amenityName'),
            __('modules.bookAmenity.bookedBy'),
            __('modules.bookAmenity.bookingDate'),
            __('modules.bookAmenity.bookingTime'),
            __('modules.bookAmenity.slotTime'),
            __('modules.bookAmenity.numberOfpersons'),

        ];
    }

    public function map($amenity): array
    {
        return [
            $amenity->first()->amenity->amenities_name,
            $amenity->first()->user->name,
            $amenity->first()->booking_date,
            $amenity->count() > 1 ? $amenity->first()->booking_type : $amenity->first()->booking_time,
            $amenity->first()->amenity->slot_time,
            $amenity->first()->persons == 'null' || $amenity->first()->persons == 0 || $amenity->count() > 1 ? '--' :  $amenity->first()->persons      
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
        $query = BookAmenity::query()->with(['amenity', 'user']);
        if ($this->search != '') {
            $query->where(function ($q) {
                $q->whereHas('amenity', function ($q) {
                        $q->where('amenities_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if (!empty($this->filterUsers)) {
            $query = $query->whereIn('booked_by', $this->filterUsers);
        }

        if (!empty($this->filterAmenities)) {
            $query->whereHas('amenity', function ($q) {
                $q->whereIn('id', $this->filterAmenities);
            });
        }

        if (!user_can('Show Book Amenity')) {
            $userId = user()->id;
            $query->where('booked_by', $userId);
        }
        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
            $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();
            $query->orderBy('Booking_date', 'desc')->whereDate('Booking_date', '>=', $start)->whereDate('Booking_date', '<=', $end);
        }

        return $query->get()->groupBy('unique_id');
    }

}
