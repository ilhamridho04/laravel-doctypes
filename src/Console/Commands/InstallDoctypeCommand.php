<?php

/*
 * This file is part of the DocTypes package.
 *
 * (c) Ilham Ridho Asysyifa'a <ilhamridho.ir@gmail.com>
 */

namespace Ngodingskuyy\Doctypes\Console\Commands;

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

        // Publish migrations
        $this->info('Publishing migrations...');
        Artisan::call('vendor:publish', [
            '--tag' => 'doctypes-migrations',
            '--force' => true
        ]);

        // Publish frontend components
        $this->info('Publishing frontend components...');
        Artisan::call('vendor:publish', [
            '--tag' => 'doctypes-frontend',
            '--force' => true
        ]);

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate');

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
        $this->line('1. Check resources/js/features/doctypes/ for Vue.js components');
        $this->line('2. Import components in your Vue.js application');
        $this->line('3. Configure API routes if needed');
        $this->line('4. Check the example.html file for usage examples');
        $this->line('');
        $this->line('Frontend components published to: resources/js/features/doctypes/');

        return 0;
    }
}
