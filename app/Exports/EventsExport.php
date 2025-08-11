<?php

namespace App\Exports;

use App\Models\Event;
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

class EventsExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $search;
    protected $filterStatuses;

    public function __construct($search, $filterStatuses = [])
    {
        $this->search = $search;
        $this->filterStatuses = $filterStatuses;
    }

    public function headings(): array
    {
        return [
            __('modules.event.eventName'),
            __('modules.event.where'),
            __('modules.event.startDateTime'),
            __('modules.event.endDateTime'),
            __('modules.assets.status'),
        ];
    }

    public function map($event): array
    {
        return [
            $event->event_name,
            $event->where,
            $event->start_date_time->format('Y-m-d H:i'),
            $event->end_date_time->format('Y-m-d H:i'),
            ucfirst($event->status),
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
        $query = Event::query();

        if ($this->search != '') {
            $query->where(function ($q) {
                $q->where('event_name', 'like', '%' . $this->search . '%')
                ->orWhere('where', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterStatuses)) {
            $query = $query->whereIn('status', $this->filterStatuses);
        }

        if (!user_can('Show Event')) {
            $query->whereHas('attendee', function ($q) {
                $q->where('user_id', user()->id);
            });
        }

        return $query->get();
    }

}
