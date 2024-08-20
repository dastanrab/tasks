<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        try {
            DB::beginTransaction();
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
            DB::commit();

            return $this->http_response(true,$task ,'',[]);
        }catch (ValidationException $exception){
            DB::rollBack();
            $this->store_log($exception);
            return $this->http_response(false,[] ,$exception->getMessage(),[],422);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            $this->store_log($exception);
            return $this->http_response(false,[] ,'system error for more detail checl logs',[],500);
        }

    }

    public function update(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);
        try {
            DB::beginTransaction();
            $request->validate([
                'description' => 'string',
                'status' => 'in:in-progress,completed',
            ]);
            $task->update($request->all());
            $task=$task->refresh()->toArray();
            event(new SendMessage('update',$task));
            DB::commit();
            return $this->http_response(true,$task,'successfull update',[]);
        }catch (ValidationException $exception){
            DB::rollBack();
            $this->store_log($exception);
            return $this->http_response(false,[] ,$exception->getMessage(),[],422);
        }
        catch (Exception $exception){
            DB::rollBack();
            $this->store_log($exception);
            return $this->http_response(false,[] ,'system error for more detail check logs',[],500);
        }
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        try {
            DB::beginTransaction();
            $result=$task->toArray();
            $task->delete();
            event(new SendMessage('delete',$result));
            DB::commit();
            return $this->http_response(true,$result,'successfull delete',[]);
        }catch (ValidationException $exception){
            DB::rollBack();
            $this->store_log($exception);
            return $this->http_response(false,[] ,$exception->getMessage(),[],422);
        }
        catch (Exception $exception){
            DB::rollBack();
            $this->store_log($exception);
            return $this->http_response(false,[] ,'system error for more detail check logs',[],500);
        }
    }
    private function http_response(bool $status,array $data,string $msg,array $error,int $http_code = 200)
    {
        return response()->json(['status'=>$status,'data'=>$data,'msg'=>$msg,'error'=>$error],status: $http_code);
    }
    private function store_log($exception)
    {
        saveException(
            class_basename($exception),
            $exception->getMessage(),
            $exception instanceof HttpException ? $exception->getStatusCode() : 500,
            1,
            $exception->getTraceAsString(),
            json_encode([
                'request_data' => request()->all()??null,
                'exception_trace' => $exception->getTraceAsString(),
            ]),
            auth()->id()??null ,
            request()->fullUrl()??null,
            json_encode(request()->all())??null,
            json_encode(request()->headers->all()??null),
            request()->header('User-Agent')??null);
    }
}
