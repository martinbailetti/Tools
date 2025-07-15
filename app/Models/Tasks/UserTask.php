<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'subject', 'description', 'client', 'client_contact', 'task_datetime', 'task_hours', 'fault_of', 'resolved'];
}
