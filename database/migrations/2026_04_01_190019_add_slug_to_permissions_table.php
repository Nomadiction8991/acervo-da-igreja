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
        Schema::table('permissions', static function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('id');
        });

        DB::table('permissions')
            ->select(['id', 'nome', 'modulo', 'acao'])
            ->orderBy('id')
            ->get()
            ->each(static function (object $permission): void {
                $slug = $permission->nome ?: $permission->modulo.'.'.$permission->acao;

                DB::table('permissions')
                    ->where('id', $permission->id)
                    ->update(['slug' => $slug]);
            });

        Schema::table('permissions', static function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', static function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
