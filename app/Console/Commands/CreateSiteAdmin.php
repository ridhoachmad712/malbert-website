<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('site:create-admin')]
#[Description('Membuat administrator pertama website sekolah secara interaktif')]
class CreateSiteAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::where('role', 'admin')->exists()) {
            $this->error('Administrator sudah tersedia.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Nama administrator'));
        $email = trim((string) $this->ask('Email administrator'));
        $password = (string) $this->secret('Kata sandi (minimal 12 karakter)');
        $confirmation = (string) $this->secret('Ulangi kata sandi');

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12 || $password !== $confirmation) {
            $this->error('Nama, email, atau kata sandi tidak valid. Administrator tidak dibuat.');

            return self::FAILURE;
        }

        User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'admin']);
        $this->info('Administrator berhasil dibuat.');

        return self::SUCCESS;
    }
}
