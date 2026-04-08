<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordsToBcrypt extends Command
{
    protected $signature = 'passwords:migrate-to-bcrypt';
    protected $description = 'Migrate all passwords from MD5/plain to bcrypt hashing';

    public function handle(): int
    {
        // Safety check - only allow in development
        if (app()->isProduction()) {
            $this->error('This command cannot run in production.');
            return self::FAILURE;
        }

        // Confirmation required
        if (!$this->confirm('This will update ALL user passwords to bcrypt. Continue?')) {
            $this->info('Command cancelled.');
            return self::CANCELLED;
        }

        $updated = 0;

        // Use chunking to avoid loading all users in memory
        User::query()
            ->where('password', '!=', null)
            ->chunk(100, function ($users) use (&$updated) {
                foreach ($users as $user) {
                    // Check if password is already bcrypt (starts with $2a$, $2b$, or $2y$)
                    if (!preg_match('/^\$2[aby]\$/', $user->password)) {
                        // Password is not bcrypt, hash it
                        $user->password = Hash::make($user->password);
                        $user->save();
                        $updated++;
                        $this->line("✓ Updated password for: {$user->email}");
                    }
                }
            });

        $this->info("Migration complete! Updated {$updated} passwords to bcrypt.");
        return self::SUCCESS;
    }
}
