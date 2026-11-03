<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

class InstallDemoDataCommand extends Command
{
    protected $signature = 'demo:install {--fresh : Wipe database before installing}';
    protected $description = 'Install complete enterprise demo data across 3 business tenants and 8 personas';

    public function handle(): int
    {
        $this->info('🚀 Initializing AI Sales Assistant Demo Dataset...');

        if ($this->option('fresh')) {
            $this->warn('Wiping database and running migrations...');
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->call('migrate', ['--force' => true]);
        }

        $this->info('Seeding 76 modules, 11 business presets, 3 tenants, and 100+ scored leads...');
        $this->call('db:seed', ['--class' => DemoDataSeeder::class, '--force' => true]);

        $this->info('Clearing application and module caches...');
        $this->call('cache:clear');

        $this->newLine();
        $this->info('✅ Demo installation completed successfully!');
        $this->table(
            ['Role Persona', 'Email', 'Password', 'Target Panel'],
            [
                ['Platform Super Admin', 'me@imrandev.bd', 'password', '/super-admin'],
                ['Tenant Executive (MD)', 'admin@novatrading.com', 'password', '/app'],
                ['Senior Account Exec', 'sales@novatrading.com', 'password', '/app'],
                ['Head of Finance', 'finance@novatrading.com', 'password', '/app'],
                ['Customer Support', 'support@novatrading.com', 'password', '/app'],
                ['HR & Attendance Lead', 'hr@novatrading.com', 'password', '/app'],
                ['Client Portal User', 'david@sterlingglobal.com', 'password', '/portal'],
                ['Partner Channel Lead', 'sophia@chenpartners.com', 'password', '/partner'],
            ]
        );

        $this->info('Public Marketing URL: http://localhost:8000');
        $this->info('One-Click Demo Switcher: http://localhost:8000/demo');

        return Command::SUCCESS;
    }
}
