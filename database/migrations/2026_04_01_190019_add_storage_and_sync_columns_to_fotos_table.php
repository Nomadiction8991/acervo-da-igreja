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
        Schema::table('fotos', static function (Blueprint $table): void {
            $table->string('disk')->default('public')->after('caminho');
        });

        DB::table('fotos')->update(['disk' => 'public']);
    }

    public function down(): void
    {
        Schema::table('fotos', static function (Blueprint $table): void {
            $table->dropColumn('disk');
        });
    }
};
