<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyResponseRequest;
use App\Services\SurveyResponseService;

class SurveyResponseController extends Controller
{
    public function __construct(protected SurveyResponseService $service) {}

    public function store(StoreSurveyResponseRequest $request)
    {
        $response = $this->service->store($request->validated());

        return response()->json(['id' => $response->id], 201);
    }

    public function summary()
    {
        return response()->json($this->service->getResultsSummary());
    }
}