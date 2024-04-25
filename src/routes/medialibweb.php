<?php

use Illuminate\Support\Facades\Route;
use Danir\MediaLib\Livewire\UploadImageFilament;

Route::get('/', UploadImageFilament::class)->name('uploadimagefilament');
