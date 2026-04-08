<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drive_accounts', static function (Blueprint $table): void {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('provider')->default('google_drive');
            $table->string('folder_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('refresh_token')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drive_accounts');
    }
};
