<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'super-admin:create {--name=} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new super admin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('Enter super admin name');
        $email = $this->option('email') ?: $this->ask('Enter super admin email');
        $password = $this->option('password') ?: $this->secret('Enter super admin password');

        if (SuperAdmin::where('email', $email)->exists()) {
            $this->error('A super admin with this email already exists!');
            return Command::FAILURE;
        }

        SuperAdmin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        $this->info("Super admin '{$name}' created successfully!");
        $this->line("Email: {$email}");
        $this->line("Login URL: " . url('/super-admin/login'));

        return Command::SUCCESS;
    }
}
