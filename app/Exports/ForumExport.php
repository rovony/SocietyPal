<?php

namespace App\Exports;

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
use App\Models\Forum;

class ForumExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $categoryId;

    public function __construct($search, $categoryId = [])
    {
        $this->search = $search;
        $this->categoryId = $categoryId;
    }
    
    public function headings(): array
    {
        return [
            __('modules.forum.title'),
            __('modules.forum.categoryName'),
            __('modules.forum.createdBy'),
            __('modules.forum.discussionType'),
            __('app.date'),
        ];
    }

    public function map($forum): array
    {
        return [
            $forum->title,
            $forum->category->name,
            $forum->user->name,
            $forum->discussion_type,
            $forum->date,
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
        $query = Forum::query();
        
        if (!user_can('Show Forum')) {
            $query->where('discussion_type', 'public');

            $query->orWhereHas('users', function ($query) {
                $query->where('user_id', user()->id);
            });
        }

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }
    
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
    

        return $query->get();
    }

}
