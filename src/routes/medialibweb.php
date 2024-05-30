<?php

use Illuminate\Support\Facades\Route;
use Danir\MediaLib\Livewire\Medialibrary\UploadImageFilament;
use Danir\MediaLib\Livewire\Modalselect\Modalselect;


Route::get('/medialib', UploadImageFilament::class)->name('uploadimagefilament');

Route::get('/modalselect', Modalselect::class)->name('modalselect');
