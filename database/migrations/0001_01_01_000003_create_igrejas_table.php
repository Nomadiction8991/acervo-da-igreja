<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('igrejas', static function (Blueprint $table): void {
            $table->id();
            $table->string('codigo_controle')->unique();
            $table->string('nome_fantasia');
            $table->string('razao_social');
            $table->string('matricula')->nullable();
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();

            // Controle de visibilidade por campo (JSON)
            $table->json('visibilidade')->nullable();

            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('igrejas');
    }
};
