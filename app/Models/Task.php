<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;


class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'end_at', 'priority', 'status'
    ];
    const HIGH_PRIORITY='high';
    const MIDDLE_PRIORITY='middle';
    const LOW_PRIORITY='low';
    protected $appends=[
        'created_at_tehran',
    ];

    public function getCreatedAtTehranAttribute()
    {
        return Jalalian::fromDateTime($this->end_at)->format('Y/m/d');
    }
    public $timestamps =false;
}
