<?php

use App\Events\SendMessage;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/t', function () {
    event(new SendMessage());
    dd('Event Run Successfully.');
});
Route::get('/',\App\Livewire\TaskTable::class);
