<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Create_file_history extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'status', 'message', 'disk_name', 'path', 'file_name', 'content', 'created_by', 'created_at', 'updated_at'];
}
