<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\VisitorManagement;
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

class VisitorsExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $filterApartments;

    public function __construct($search, $startDate, $endDate, $filterApartments)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->filterApartments = $filterApartments;
    }

    public function headings(): array
    {
        return [
            __('modules.visitorManagement.visitorName'),
            __('modules.visitorManagement.visitorMobile'),
            __('modules.visitorManagement.visitorAddress'),
            __('modules.settings.societyApartmentNumber'),
            __('modules.visitorManagement.inTime'),
            __('modules.visitorManagement.outTime'),
            __('modules.visitorManagement.dateOfVisit'),
        ];
    }

    public function map($visitor): array
{
    $phoneWithCode = '--';
    if ($visitor->phone_number) {
        $phoneWithCode = $visitor->country_phonecode
            ? '+' . $visitor->country_phonecode . ' ' . $visitor->phone_number
            : $visitor->phone_number;
    }

    return [
        $visitor->visitor_name,
        $phoneWithCode,
        $visitor->address,
        $visitor->apartment->apartment_number,
        $visitor->in_time ? Carbon::parse($visitor->in_time)->timezone(timezone())->format('h:i A') : '--',
        $visitor->out_time ? Carbon::parse($visitor->out_time)->timezone(timezone())->format('h:i A') : '--',
        $visitor->date_of_visit->format('d-m-Y'),
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
        $query = VisitorManagement::query()->with(['apartment', 'user']);

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->whereHas('apartment', function ($q) {
                    $q->where('apartment_number', 'like', '%' . $this->search . '%');
                })
                ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                ->orWhere('visitor_name', 'like', '%' . $this->search . '%');
            });
        }
        if ((!empty($this->startDate)) || (!empty($this->endDate))) {
            $startDate = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
            $endDate = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();
            $query->whereBetween('date_of_visit', [$startDate, $endDate]);
        }

        $loggedInUser = user()->id;
        if (!user_can('Show Visitors')) {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('apartment', function ($q) use ($loggedInUser) {
                    $q->where('status', 'available_for_rent')->orWhere('status', 'occupied')->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartment.tenants', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser)
                      ->where('apartment_tenant.status', 'current_resident')
                      ->whereColumn('apartment_tenant.contract_start_date', '<=', 'visitors_management.date_of_visit')
                        ->whereColumn('apartment_tenant.contract_end_date', '>=', 'visitors_management.date_of_visit');
                })
                ->orWhere('added_by', $loggedInUser);
            });
        }
        if (!empty($this->filterApartments)) {
            $query->whereHas('apartment', function ($query) {
                $query->whereIn('apartment_number', $this->filterApartments);
            });
        }

        return $query->get();

    }

}
