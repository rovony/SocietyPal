<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;



class TicketExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $search;
    protected $filterStatuses;
    protected $filterRequesterNames;

    public function __construct($search, $filterStatuses = [], $filterRequesterNames = [])
    {
        $this->search = $search;
        $this->filterStatuses = $filterStatuses;
        $this->filterRequesterNames = $filterRequesterNames;
    }

    public function headings(): array
    {
        return [
            __('modules.tickets.ticketNumber'),
            __('modules.tickets.subject'),
            __('modules.tickets.requestedBy'),
            __('modules.tickets.agent'),
            __('modules.tickets.ticketType'),
            __('modules.settings.status'),

        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->subject,
            $ticket->user->name,
            $ticket->agent->name,
            $ticket->ticketType->type_name,
            ucfirst($ticket->status),
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
        $query = Ticket::query();

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->where('subject', 'like', '%' . $this->search . '%')
                ->orWhere('status', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('agent', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('ticketType', function ($typeQuery) {
                    $typeQuery->where('type_name', 'like', '%' . $this->search . '%'); 
                });
            });
        }

        if (!empty($this->filterStatuses)) {
            $query = $query->whereIn('status', $this->filterStatuses);
        }

        if (!empty($this->filterRequesterNames)) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->whereIn('id', $this->filterRequesterNames);
            });
        }
        
        return $query->get();
    }
}
