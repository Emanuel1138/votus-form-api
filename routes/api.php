<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyResponseController;

Route::post('/survey-responses', [SurveyResponseController::class, 'store'])
    ->middleware('throttle:30,1');