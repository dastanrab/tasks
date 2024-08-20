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

    public function fetchTasks()
    {
            $response = Http::get('localhost/api/task');
            $this->tasks = $response->json();
    }

    public function selectTask($taskId)
    {
        dd('sss');
        $task = collect($this->tasks)->firstWhere('id', $taskId);
        $this->selectedTaskId = $task['id'];
        $this->taskDescription = $task['description'];
        $this->taskStatus = $task['status'];
    }

    public function updateTask()
    {
        if ($this->selectedTaskId) {
            try {
                Http::put("https://your-api-url.com/api/tasks/{$this->selectedTaskId}", [
                    'description' => $this->taskDescription,
                    'status' => $this->taskStatus,
                ]);

                $this->fetchTasks();
                $this->resetInputFields();
            } catch (\Exception $e) {
                Log::error('Error updating task: ' . $e->getMessage());
            }
        }
    }

    public function taskUpdated($taskData)
    {
        // Update the task in the local list
        $this->tasks = collect($this->tasks)->map(function ($task) use ($taskData) {
            if ($task['id'] === $taskData['id']) {
                return $taskData;
            }
            return $task;
        })->toArray();
    }

    private function resetInputFields()
    {
        $this->taskDescription = '';
        $this->taskStatus = '';
        $this->selectedTaskId = null;
    }

    public function render()
    {
        return view('livewire.task-table');
    }
}
