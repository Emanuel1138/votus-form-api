<?php

namespace App\Services;

use App\Models\SurveyResponse;

class SurveyResponseService
{
    public function store(array $data): SurveyResponse
    {
        return SurveyResponse::firstOrCreate(
            [
                'device_id' => $data['device_id'] ?? null,
                'answered_at' => $data['answered_at'],
            ],
            [
                'answers' => $data['answers'],
            ]
        );
    }
}