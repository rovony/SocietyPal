<?php

namespace App\Exports;

use App\Models\ServiceManagement;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceManagementExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $serviceId;
    protected $filterStatuses;
    protected $filterApartments;

    public function __construct($search, $serviceId = null ,$filterStatuses = [], $filterApartments = [])
    {
        $this->search = $search;
        $this->serviceId = $serviceId;
        $this->filterStatuses = $filterStatuses;
        $this->filterApartments = $filterApartments;
    }

    public function headings(): array
    {
        return [
            __('modules.serviceManagement.serviceType'),
            __('modules.serviceManagement.contactPersonName'),
            __('modules.serviceManagement.contactNumber'),
            __('modules.settings.dailyHelp'),
            __('modules.settings.apartment'),
            __('modules.serviceManagement.price'),
            __('modules.serviceManagement.websiteLink'),
            __('modules.serviceManagement.description'),
        ];
    }

    public function map($serviceManagement): array
    {
        $phoneWithCode = '--';
        if ($serviceManagement->phone_number) {
            $phoneWithCode = $serviceManagement->country_phonecode
                ? '+' . $serviceManagement->country_phonecode . ' ' . $serviceManagement->phone_number
                : $serviceManagement->phone_number;
        }

        return [
            $serviceManagement->serviceType->name,
            $serviceManagement->contact_person_name,
            $phoneWithCode,
            $serviceManagement->daily_help ? 'Yes' : 'No',
            $serviceManagement->apartmentManagements->pluck('apartment_number')->join(', ') ?? '--',
            currency_format($serviceManagement->price),
            $serviceManagement->website_link,
            $serviceManagement->description,
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
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'name' => 'Arial'], 'fill'  => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => array('rgb' => 'f5f5f5'),
            ]],
        ];
    }

    public function collection()
    {
        $query = ServiceManagement::query()->with('serviceType', 'apartmentManagements') ->when($this->serviceId, function ($query) {
            $query->where('service_type_id', $this->serviceId);
        });

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('contact_person_name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterStatuses)) {
            $query->whereIn('status', $this->filterStatuses);
        }

        if (!empty($this->filterApartments)) {
            $query->whereHas('apartmentManagements', function ($q) {
                $q->whereIn('apartment_management_id', $this->filterApartments);
            });
        }
        $loggedInUser = user()->id;

        if ((!user_can('Show Service Provider'))) {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser)->where('apartment_tenant.status', 'current_resident');
                    });
                })
                ->orWhereDoesntHave('apartmentManagements');
            });
        }

        return $query->get();
    }

}
