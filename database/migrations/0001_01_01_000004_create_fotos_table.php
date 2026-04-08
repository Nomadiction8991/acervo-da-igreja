<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('igreja_id')->constrained('igrejas')->cascadeOnDelete();
            $table->string('caminho'); // Path to file
            $table->string('nome_original');
            $table->string('mime_type');
            $table->integer('tamanho');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_principal')->default(false);
            $table->integer('ordem')->default(0);

            $table->timestamps();
            $table->softDeletes()->index();

            $table->index('igreja_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos');
    }
};
