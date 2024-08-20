<?php
use Illuminate\Support\Facades\Route;

Route::prefix('task')->controller(\App\Http\Controllers\TaskController::class)->group(function (){
    Route::get('/', 'index')->name('all-task');
    Route::post('/', 'store')->name('store-task');
    Route::put('/{task_id}', 'update')->name('update-task');
    Route::delete('/{task_id}', 'destroy')->name('delete-task');
});
