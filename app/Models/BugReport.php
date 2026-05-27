<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $connection = 'bugs';

    protected $fillable = [
        'title',
        'details',
        'page_url',
        'module',
        'severity',
        'reported_by',
        'status',
    ];
}
