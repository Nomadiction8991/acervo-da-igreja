<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('acao'); // 'criar', 'editar', 'deletar', 'login'
            $table->string('modulo'); // 'igrejas', 'fotos', 'usuarios'
            $table->string('entidade'); // 'Igreja', 'Foto'
            $table->unsignedBigInteger('entidade_id');
            $table->json('antes')->nullable();
            $table->json('depois')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['modulo', 'entidade', 'entidade_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
