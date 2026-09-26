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

    public function getResultsSummary(): array
    {
        $summary = [];

        SurveyResponse::pluck('answers')->each(function ($answers) use (&$summary) {
            foreach ($answers as $answer) {
                $questionId = $answer['question_id'];
                $answerText = $answer['answer'];

                $summary[$questionId][$answerText] = ($summary[$questionId][$answerText] ?? 0) + 1;
            }
        });

        return $summary;
    }
}