<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = ['user_id', 'title', 'event_date', 'type', 'description'];
    protected $casts = ['event_date' => 'date'];
}