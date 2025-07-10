<?php

namespace Doctypes\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallDoctypeCommand extends Command
{
    protected $signature = 'doctype:install {--seed : Seed sample data}';
    protected $description = 'Install the DocType package';

    public function handle()
    {
        $this->info('Installing DocType package...');

        // Publish configuration
        $this->info('Publishing configuration...');
        Artisan::call('vendor:publish', [
            '--tag' => 'doctypes-config',
            '--force' => true
        ]);

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', [
            '--path' => 'vendor/ngodingskuyy/doctypes/database/migrations'
        ]);

        // Seed data if requested
        if ($this->option('seed')) {
            $this->info('Seeding sample data...');
            try {
                Artisan::call('db:seed', [
                    '--class' => 'Doctypes\\Database\\Seeders\\DoctypeSeeder'
                ]);
            } catch (\Exception $e) {
                $this->warn('Could not seed sample data. You may need to run this manually.');
            }
        }

        $this->info('DocType package installed successfully!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Add API routes to your routes/api.php if needed');
        $this->line('2. Configure the frontend components in your Vue.js application');
        $this->line('3. Check the example.html file for usage examples');

        return 0;
    }
}
