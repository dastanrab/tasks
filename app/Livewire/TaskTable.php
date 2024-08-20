<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TaskTable extends Component
{
    public $tasks = [];
    public $taskDescription = '';
    public $taskStatus = '';
    public $selectedTaskId = null;

    protected $listeners = ['taskUpdated'];

    public function mount()
    {
        $this->tasks=Task::query()->get()->toArray();
    }



    public function render()
    {
        return view('livewire.task-table');
    }
}
