<div class="container mt-4">

    <div class="row">
        <div class="col-md-8"><div class="table-responsive table-sm">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>End Date</th>
                        <th>Persian End Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="taskTableBody">
                    @foreach ($tasks as $task)
                        <tr id="task-{{ $task['id'] }}">
                            <td>{{ $task['id'] }}</td>
                            <td>{{ $task['title'] }}</td>
                            <td class="task-description">{{ $task['description'] }}</td>
                            <td>{{ $task['end_at'] }}</td>
                            <td>{{ $task['created_at_tehran'] }}</td>
                            <td class="task-priority">
                                @switch($task['priority'])
                                    @case('high')
                                        <div class="btn btn-danger">high</div>
                                        @break

                                    @case('middle')
                                        <div class="btn btn-warning">middle</div>
                                        @break

                                    @default
                                        <div class="btn btn-secondary">low</div>
                                @endswitch
                            </td>
                            <td class="task-status">
                                @switch($task['status'])
                                    @case('completed')
                                        <div class="status-label btn btn-success">completed</div>
                                        @break
                                    @default
                                        <div class="status-label btn btn-danger">in-progress</div>
                                @endswitch
                            </td>
                            <td>
                                <button data-id="{{ $task['id'] }}" class="btn btn-primary btn-sm edit-btn">Edit</button>
                                <button data-id="{{ $task['id'] }}" class="btn btn-danger btn-sm delete-btn">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div></div>
        <div class="col-md-4"> <div class="card mb-4">
                <div class="card-header">
                    <h3>Add New Task</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="newTaskTitle">Title</label>
                        <input type="text" id="newTaskTitle" class="form-control" placeholder="Enter task title">
                    </div>
                    <div class="form-group mb-3">
                        <label for="newTaskDescription">Description</label>
                        <input type="text" id="newTaskDescription" class="form-control" placeholder="Enter task description">
                    </div>
                    <div class="form-group mb-3">
                        <label for="newTaskEndDate">End Date</label>
                        <input type="date" id="newTaskEndDate" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="newTaskPriority">Priority</label>
                        <select id="newTaskPriority" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="newTaskStatus">Status</label>
                        <select id="newTaskStatus" class="form-select">
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <button id="createTaskBtn" class="btn btn-success">Add Task</button>
                </div>
            </div></div>

    </div>
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editTaskId">
                    <div class="form-group mb-3">
                        <label for="editTaskDescription">Description</label>
                        <input type="text" id="editTaskDescription" class="form-control" placeholder="Enter task description">
                    </div>
                    <div class="form-group mb-3">
                        <label for="editTaskStatus">Status</label>
                        <select id="editTaskStatus" class="form-select">
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="update each task" class="btn btn-success update-task-btn">Update Task</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        window.can_add=true;
        $('.edit-btn').click(function () {
            let taskId = $(this).data('id');
            let taskRow = $('#task-' + taskId);
            $('#editTaskId').val(taskId);
            $('#editTaskDescription').val(taskRow.find('.task-description').text());
            $('#editTaskStatus').val(taskRow.find('.task-status').text());
            $('#editTaskModal').modal('show');
        });
        $('.delete-btn').click(function () {
            let taskId = $(this).data('id');
            $.ajax({
                url: '/api/task/' + taskId,
                method: 'DELETE',
                success: function (response) {
                    $('#task-' + taskId).remove();
                    $('#editTaskModal').modal('hide');
                    $.toast({
                        heading: 'Success',
                        text: response.msg,
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    })
                },
                error: function (xhr) {
                    var err = JSON.parse(xhr.responseText);
                    console.log(err);
                    $.toast({
                        heading: 'Error',
                        text: err.msg,
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-right'
                    })
                }
            });
        });
        $('.update-task-btn').click(function () {
            let taskId = $('#editTaskId').val();
            let description = $('#editTaskDescription').val();
            let status = $('#editTaskStatus').val();

            $.ajax({
                url: '/api/task/' + taskId,
                method: 'PUT',
                data: {
                    description: description,
                    status: status,
                },
                success: function (response) {
                let taskRow = $('#task-' + taskId);
                taskRow.find('.task-description').text(response.data.description);
                status = taskRow.find('.task-status .status-label');
                status.text(response.data.status)
               status.attr('class','');
                status.addClass('status-label')
                    status.addClass('btn')
               if (response.data.status === 'completed')
               {
                   status.addClass('btn-success')

               }else {
                   status.addClass('btn-danger')

               }
                $('#editTaskModal').modal('hide');
                    $.toast({
                        heading: 'Success',
                        text: response.msg,
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    })
            },
            error: function (xhr) {
                var err = JSON.parse(xhr.responseText);
                console.log(err);
                $.toast({
                    heading: 'Error',
                    text: err.msg,
                    showHideTransition: 'fade',
                    icon: 'error',
                    position: 'top-right'
                })
            }
        });
        });
        $('#createTaskBtn').click(function () {
            let title = $('#newTaskTitle').val();
            let description = $('#newTaskDescription').val();
            let endDate = $('#newTaskEndDate').val();
            let priority = $('#newTaskPriority').val();
            let status = $('#newTaskStatus').val();

            $.ajax({
                url: '/api/task',
                method: 'POST',
                data: {
                    title: title,
                    description: description,
                    end_at: endDate,
                    priority: priority,
                    status: status,
                },
                success: function (response) {
                    console.log(response)
                    window.can_add = false
                    let status='';
                    let priority='';
                    if (response.data.status === 'completed')
                    {
                      status='<div class="status-label btn btn-success">completed</div>'
                    }if (response.data.status === 'in-progress'){
                      status='<div class="status-label btn btn-danger">in-progress</div>'
                    }
                    if (response.data.priority === 'high')
                    {
                        priority='<div class=" btn btn-danger">high</div>'
                    }if (response.data.priority === 'low'){
                        priority='<div class=" btn btn-secondary">low</div>'
                    }if (response.data.priority === 'middle'){
                        priority='<div class=" btn btn-warning">middle</div>'
                    }
                    let content = '<tr id="task-'+response.data.id+'">'+'<td>'+response.data.id+'</td>'+
                        ' <td>'+response.data.title+'</td>'+
                        '<td class="task-description">'+response.data.description+'</td>'+
                    '<td>'+response.data.end_at+'</td>'+
                   '<td>'+ response.data.created_at_tehran+'</td>' +
                   ' <td>'+priority+'</td>'+
                   '<td class="task-status">'+status+'</td>' + '<td> <button data-id="'+response.data.id+'" class="btn btn-primary btn-sm edit-btn">Edit</button>'
                        + ' <button data-id="'+response.data.id+'" class="btn btn-danger btn-sm delete-btn">Delete</button></td>'+ '</tr>'
                    $('#taskTableBody').prepend(
                        content
                    );
                    $.toast({
                        heading: 'Success',
                        text: response.msg,
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    })
                    $('#newTaskTitle').val('');
                    $('#newTaskDescription').val('');
                    $('#newTaskEndDate').val('');
                    $('#newTaskPriority').val('low');
                    $('#newTaskStatus').val('pending');
                },
                error: function (xhr) {
                    var err = JSON.parse(xhr.responseText);
                    console.log(err);
                    $.toast({
                        heading: 'Error',
                        text: err.msg,
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-right'
                    })
                }
            });
        });

    });

    </script>
<script>window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT",6001)}}';</script>
<script src="//{{ Request::getHost() }}:{{env('LARAVEL_ECHO_PORT',6001)}}/socket.io/socket.io.js"></script>
<script src="{{ url('/js/laravel-echo-setup.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    function delete_task(data) {
        let taskRow = $('#task-' + data.task.id);
        taskRow.remove()
    }
    function add_task(data){
        let status='';
        let priority='';
        if (data.task.status === 'completed')
        {
            status='<div class="status-label btn btn-success">completed</div>'
        }if (data.task.status === 'in-progress'){
            status='<div class="status-label btn btn-danger">in-progress</div>'
        }
        if (data.task.priority === 'high')
        {
            priority='<div class=" btn btn-danger">high</div>'
        }if (data.task.priority === 'low'){
            priority='<div class=" btn btn-secondary">low</div>'
        }if (data.task.priority === 'middle'){
            priority='<div class=" btn btn-warning">middle</div>'
        }
        let content = '<tr id="task-'+data.task.id+'">'+'<td>'+data.task.id+'</td>'+
            ' <td>'+data.task.title+'</td>'+
            '<td class="task-description">'+data.task.description+'</td>'+
            '<td>'+data.task.end_at+'</td>'+
            '<td>'+ data.task.created_at_tehran+'</td>' +
            ' <td>'+priority+'</td>'+
            '<td class="task-status">'+status+'</td>' + '<td> <button data-id="'+data.task.id+'" class="btn btn-primary btn-sm edit-btn">Edit</button>'+
             ' <button data-id="'+data.task.id+'" class="btn btn-danger btn-sm delete-btn">Delete</button></td>'+ '</tr>'
        $('#taskTableBody').prepend(
            content
        );
        $('#newTaskTitle').val('');
        $('#newTaskDescription').val('');
        $('#newTaskEndDate').val('');
        $('#newTaskPriority').val('low');
        $('#newTaskStatus').val('pending');
    }
    function update_task(data) {
        let taskRow = $('#task-' + data.task.id);
        taskRow.find('.task-description').text(data.task.description);
        let task = taskRow.find('.task-status .status-label');
        task.text(data.task.status)
        task.attr('class','');
        task.addClass('status-label')
        task.addClass('btn')
        if (data.task.status === 'completed')
        {
            task.addClass('btn-success')

        }else {
            task.addClass('btn-danger')
        }
    }
    var i = 0;
    window.Echo.channel('user-channel')
        .listen('.UserEvent', (data) => {
            console.log(data)
            if (data.type === 'update') {
                update_task(data)
            }if(data.type === 'delete') {
                delete_task(data)
            }if (data.type === 'create') {
                if (window.can_add === true)
                {
                    add_task(data)
                }
                else {
                    window.can_add=false
                }

            }
        });
</script>
