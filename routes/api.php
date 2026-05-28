<?php

use App\Http\Controllers\BugReportController;
use Illuminate\Support\Facades\Route;

Route::get('/bugs', [BugReportController::class, 'fetchRemote']);
