<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_uma_resposta_de_pesquisa(): void
    {
        $payload = [
            'device_id' => '550e8400-e29b-41d4-a716-446655440000',
            'answered_at' => '2026-09-25 14:00:00',
            'answers' => [
                [
                    'question_id' => 1,
                    'answer' => 'Bom',
                ],
                [
                    'question_id' => 2,
                    'answer' => 'Muito bom',
                ],
            ],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
            ]);

        $this->assertDatabaseHas('survey_responses', [
            'device_id' => $payload['device_id'],
            'answered_at' => $payload['answered_at'],
        ]);

        $this->assertDatabaseCount('survey_responses', 1);
    }

    public function test_nao_permite_resposta_sem_data(): void
    {
        $payload = [
            'device_id' => '550e8400-e29b-41d4-a716-446655440000',
            'answers' => [
                [
                    'question_id' => 1,
                    'answer' => 'Bom',
                ],
            ],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['answered_at']);

        $this->assertDatabaseCount('survey_responses', 0);
    }

    public function test_nao_permite_answers_vazio(): void
    {
        $payload = [
            'device_id' => '550e8400-e29b-41d4-a716-446655440000',
            'answered_at' => '2026-09-25 14:00:00',
            'answers' => [],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['answers']);

        $this->assertDatabaseCount('survey_responses', 0);
    }

    public function test_nao_permite_answer_sem_question_id(): void
    {
        $payload = [
            'device_id' => '550e8400-e29b-41d4-a716-446655440000',
            'answered_at' => '2026-09-25 14:00:00',
            'answers' => [
                [
                    'answer' => 'Bom',
                ],
            ],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'answers.0.question_id',
            ]);
    }

    public function test_nao_permite_answer_sem_resposta(): void
    {
        $payload = [
            'device_id' => '550e8400-e29b-41d4-a716-446655440000',
            'answered_at' => '2026-09-25 14:00:00',
            'answers' => [
                [
                    'question_id' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'answers.0.answer',
            ]);
    }

    public function test_device_id_deve_ser_um_uuid_valido(): void
    {
        $payload = [
            'device_id' => 'nao-e-um-uuid',
            'answered_at' => '2026-09-25 14:00:00',
            'answers' => [
                [
                    'question_id' => 1,
                    'answer' => 'Bom',
                ],
            ],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'device_id',
            ]);
    }

    public function test_nao_cria_duplicata_com_mesmo_device_id_e_answered_at(): void
    {
        $payload = [
            'device_id' => '550e8400-e29b-41d4-a716-446655440000',
            'answered_at' => '2026-09-25 14:00:00',
            'answers' => [
                [
                    'question_id' => 1,
                    'answer' => 'Bom',
                ],
            ],
        ];

        $primeira = $this->postJson('/api/survey-responses', $payload);

        $primeira->assertStatus(201);

        $segunda = $this->postJson('/api/survey-responses', $payload);

        $segunda->assertStatus(201);

        $this->assertSame(
            $primeira->json('id'),
            $segunda->json('id')
        );

        $this->assertDatabaseCount('survey_responses', 1);
    }

    public function test_pode_criar_resposta_sem_device_id(): void
    {
        $payload = [
            'answered_at' => '2026-09-25 15:00:00',
            'answers' => [
                [
                    'question_id' => 1,
                    'answer' => 'Bom',
                ],
            ],
        ];

        $response = $this->postJson('/api/survey-responses', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
            ]);

        $this->assertDatabaseCount('survey_responses', 1);
    }
}