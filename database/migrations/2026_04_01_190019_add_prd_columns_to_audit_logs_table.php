<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', static function (Blueprint $table): void {
            $table->json('old_values')->nullable()->after('entidade_id');
            $table->json('new_values')->nullable()->after('old_values');
            $table->index(['acao', 'modulo', 'created_at']);
        });

        DB::table('audit_logs')
            ->select(['id', 'antes', 'depois'])
            ->orderBy('id')
            ->get()
            ->each(static function (object $log): void {
                DB::table('audit_logs')
                    ->where('id', $log->id)
                    ->update([
                        'old_values' => $log->antes,
                        'new_values' => $log->depois,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('audit_logs', static function (Blueprint $table): void {
            $table->dropIndex(['acao', 'modulo', 'created_at']);
            $table->dropColumn(['old_values', 'new_values']);
        });
    }
};
