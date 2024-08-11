<?php

use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('admin');
});
Route::get('email-templates/{emailTemplate}/preview', [EmailController::class, 'preview'])->name('email-template.preview');
