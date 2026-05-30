<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    use HasSoftDelete;

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

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
