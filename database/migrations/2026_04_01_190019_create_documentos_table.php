<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('igreja_id')->constrained('igrejas')->cascadeOnDelete();
            $table->foreignId('grupo_documento_id')->nullable()->constrained('grupo_documentos')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('path');
            $table->string('disk')->default('local');
            $table->string('tipo');
            $table->string('mime_type');
            $table->unsignedBigInteger('tamanho');
            $table->boolean('publico')->default(false);
            $table->string('drive_file_id')->nullable();
            $table->string('drive_link')->nullable();
            $table->string('sync_status')->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->index(['igreja_id', 'publico']);
            $table->index('sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
