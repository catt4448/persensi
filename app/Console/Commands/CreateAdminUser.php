<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {name?} {email?} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user (or promote existing user to admin)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name') ?: $this->ask('Nama admin');
        $email = $this->argument('email') ?: $this->ask('Email admin');
        $password = $this->option('password') ?: $this->secret('Password admin (kosong = auto-generate)');

        if (!$name || !$email) {
            $this->error('Nama dan email wajib diisi.');
            return 1;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid.');
            return 1;
        }

        $existing = User::where('email', $email)->first();
        if ($existing) {
            $updates = [];
            if (!empty($name) && $existing->name !== $name) {
                $updates['name'] = $name;
            }
            if ($existing->role !== 'admin') {
                $updates['role'] = 'admin';
            }

            if (!empty($updates)) {
                $existing->update($updates);
                $this->info("User {$email} diperbarui menjadi admin.");
            } else {
                $this->info("User {$email} sudah admin.");
            }

            return 0;
        }

        if (!$password) {
            $password = Str::random(12);
            $this->info("Password auto-generate: {$password}");
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
        ]);

        $this->info("Admin {$email} berhasil dibuat.");

        return 0;
    }
}
