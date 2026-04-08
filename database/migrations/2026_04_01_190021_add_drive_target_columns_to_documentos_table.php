<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('documentos')) {
            return;
        }

        Schema::table('documentos', static function (Blueprint $table): void {
            if (! Schema::hasColumn('documentos', 'drive_account_id')) {
                $table->foreignId('drive_account_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('drive_accounts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('documentos', 'drive_folder_id')) {
                $table->string('drive_folder_id')
                    ->nullable()
                    ->after('drive_account_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('documentos')) {
            return;
        }

        Schema::table('documentos', static function (Blueprint $table): void {
            if (Schema::hasColumn('documentos', 'drive_account_id')) {
                $table->dropConstrainedForeignId('drive_account_id');
            }

            if (Schema::hasColumn('documentos', 'drive_folder_id')) {
                $table->dropColumn('drive_folder_id');
            }
        });
    }
};
