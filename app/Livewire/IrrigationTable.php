<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\IrrigationData;

class IrrigationTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.irrigation-table', [
            'tableData' => IrrigationData::orderBy('tanggal', 'desc')->paginate(10)
        ]);
    }
}
