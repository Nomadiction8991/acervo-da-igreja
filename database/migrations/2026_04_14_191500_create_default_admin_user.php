<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $admin = config('admin.default', []);

        $name = is_string($admin['name'] ?? null) ? trim($admin['name']) : '';
        $email = is_string($admin['email'] ?? null) ? trim($admin['email']) : '';
        $password = is_string($admin['password'] ?? null) ? $admin['password'] : '';

        if ($name === '' || $email === '' || $password === '') {
            return;
        }

        $now = now();
        $existing = DB::table('users')->where('email', $email)->first();

        $payload = [
            'name' => $name,
            'password' => Hash::make($password),
            'email_verified_at' => $now,
            'is_admin' => true,
            'is_active' => true,
            'updated_at' => $now,
        ];

        if ($existing === null) {
            $payload['created_at'] = $now;

            DB::table('users')->insert(array_merge(['email' => $email], $payload));

            return;
        }

        DB::table('users')->where('email', $email)->update($payload);
    }

    public function down(): void
    {
        $admin = config('admin.default', []);
        $email = is_string($admin['email'] ?? null) ? trim($admin['email']) : '';

        if ($email === '') {
            return;
        }

        DB::table('users')->where('email', $email)->delete();
    }
};
