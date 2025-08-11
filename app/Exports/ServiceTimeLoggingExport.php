<?php

namespace App\Exports;

use App\Models\BookAmenity;
use App\Models\ServiceClockInOut;
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

class ServiceTimeLoggingExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $startDate;
    protected $endDate;

    public function __construct($search, $startDate, $endDate)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            __('modules.serviceManagement.contactPersonName'),
            __('modules.serviceManagement.clockInDate'),
            __('modules.serviceManagement.clockInTime'),
            __('modules.serviceManagement.clockOutDate'),
            __('modules.serviceManagement.clockOutTime'),
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->service->contact_person_name,
            $attendance->clock_in_date,
            $attendance->clock_in_time ? Carbon::parse( $attendance->clock_in_time)->format('h:i A') : '--',
            $attendance->clock_out_date ?? '--',
            $attendance->clock_out_time ? Carbon::parse( $attendance->clock_out_time)->format('h:i A') : '--',
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
        $query = ServiceClockInOut::query()->with('service');

        if (!empty($this->search)) {
            $query->whereHas('service', function ($subQuery) {
                $subQuery->where('contact_person_name', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
            $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();
            $query->orderBy('clock_in_date', 'desc')->whereDate('clock_in_date', '>=', $start)->whereDate('clock_in_date', '<=', $end);
        }

        $loggedInUser = user()->id;

        if (!user_can('Show Service Time Logging') && isRole() != 'Guard') {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('service.apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('service.apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser)->where('apartment_tenant.status', 'current_resident');
                    });
                });
            });
        }
        
        return $query->get();
    }

}
