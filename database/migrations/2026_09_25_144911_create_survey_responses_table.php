<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->uuid('device_id')->nullable()->index(); // gerado no app, evita duplicar se reenviar
            $table->json('answers'); // [{"question_id": 1, "answer": "Bom"}, ...]
            $table->timestamp('answered_at'); // quando a pessoa respondeu no celular (pode ser antes de sincronizar)
            $table->timestamps();

            $table->unique(['device_id', 'answered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};