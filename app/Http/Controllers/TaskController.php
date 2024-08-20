<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    /**
    @OA\Get(
    path="/api/tasks",
    summary="Get list of tasks",
    tags={"Tasks"},
    @OA\Response(
    response=200,
    description="Successful operation",
    @OA\JsonContent()
    )
    )
     */
    public function index()
    {
        return $this->http_response(true, Task::query()->get()->toArray(),'',[]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'end_at' => 'required|date',
            'priority' => 'required|in:high,middle,low',
            'status' => 'required|in:pending,in-progress,completed',
        ]);
        $task=Task::query()->create($request->all())->toArray();
        if ($task['priority'] == 'high')
        {
            event(new SendMessage('create',$task));
        }
        return $this->http_response(true,$task ,'',[]);
    }

    public function update(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);

        $request->validate([
            'description' => 'string',
            'status' => 'in:in-progress,completed',
        ]);
        $task->update($request->all());
        $task=$task->refresh()->toArray();
        event(new SendMessage('update',$task));
        return $this->http_response(true,$task,'successfull update',[]);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $result=$task->toArray();
        $task->delete();
        event(new SendMessage('delete',$result));
        return $this->http_response(true,$result,'successfull delete',[]);
    }
    private function http_response(bool $status,array $data,string $msg,array $error)
    {
        return response()->json(['status'=>$status,'data'=>$data,'msg'=>$msg,'error'=>$error]);
    }
}
