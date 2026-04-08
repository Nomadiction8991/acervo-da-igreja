<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fotos', static function (Blueprint $table): void {
            $table->foreignId('drive_account_id')
                ->nullable()
                ->after('igreja_id')
                ->constrained('drive_accounts')
                ->nullOnDelete();
            $table->string('drive_folder_id')->nullable()->after('drive_account_id');
            $table->string('drive_file_id')->nullable()->after('disk');
            $table->string('drive_link')->nullable()->after('drive_file_id');
            $table->string('sync_status')->nullable()->after('drive_link');
            $table->text('sync_error')->nullable()->after('sync_status');
            $table->timestamp('synced_at')->nullable()->after('sync_error');
            $table->index('sync_status');
        });
    }

    public function down(): void
    {
        Schema::table('fotos', static function (Blueprint $table): void {
            $table->dropIndex(['sync_status']);
            $table->dropConstrainedForeignId('drive_account_id');
            $table->dropColumn([
                'drive_folder_id',
                'drive_file_id',
                'drive_link',
                'sync_status',
                'sync_error',
                'synced_at',
            ]);
        });
    }
};
