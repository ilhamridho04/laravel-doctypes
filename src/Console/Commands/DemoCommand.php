<?php

namespace NgodingSkuyy\Doctypes\Console\Commands;

use Illuminate\Console\Command;
use NgodingSkuyy\Doctypes\Models\Doctype;
use NgodingSkuyy\Doctypes\Services\DoctypeGeneratorService;

class DemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'doctype:demo 
                           {--reset : Reset demo data}
                           {--generate : Generate files for demo doctypes}';

    /**
     * The console command description.
     */
    protected $description = 'Create demo doctypes and show generator usage examples';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('reset')) {
            return $this->resetDemo();
        }

        if ($this->option('generate')) {
            return $this->generateDemoFiles();
        }

        return $this->showDemo();
    }

    /**
     * Show demo and examples
     */
    private function showDemo(): int
    {
        $this->info('🚀 Doctype Generator Demo');
        $this->newLine();

        // Check if demo data exists
        $customerDoctype = Doctype::where('name', 'Customer')->first();
        $productDoctype = Doctype::where('name', 'Product')->first();

        if (!$customerDoctype || !$productDoctype) {
            $this->warn('Demo doctypes not found. Creating them now...');
            $this->call('db:seed', ['--class' => 'NgodingSkuyy\\Doctypes\\Database\\Seeders\\ExampleDoctypeSeeder']);
            $this->newLine();
        }

        // Show available doctypes
        $this->info('📋 Available Doctypes:');
        $doctypes = Doctype::with('fields')->get();

        $headers = ['Name', 'Label', 'Fields', 'Description'];
        $rows = [];

        foreach ($doctypes as $doctype) {
            $rows[] = [
                $doctype->name,
                $doctype->label,
                $doctype->fields->count(),
                $doctype->description ?? 'No description'
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Show generation examples
        $this->info('⚡ Generator Commands:');
        $this->newLine();

        $examples = [
            'Basic generation:' => [
                'php artisan doctype:generate Customer',
                'php artisan doctype:generate Product',
            ],
            'Specific files only:' => [
                'php artisan doctype:generate Customer --types=model,controller',
                'php artisan doctype:generate Product --types=migration',
            ],
            'With custom module:' => [
                'php artisan doctype:generate Customer --module=CRM',
                'php artisan doctype:generate Product --module=Inventory',
            ],
            'Force overwrite:' => [
                'php artisan doctype:generate Customer --force',
            ],
        ];

        foreach ($examples as $category => $commands) {
            $this->comment($category);
            foreach ($commands as $command) {
                $this->line("  <fg=green>$command</>");
            }
            $this->newLine();
        }

        // Show next steps
        $this->info('📝 Quick Start:');
        $this->line('1. <fg=yellow>php artisan doctype:demo --generate</> (Generate demo files)');
        $this->line('2. <fg=yellow>php artisan migrate</> (Run migrations)');
        $this->line('3. Add routes: <fg=yellow>Route::apiResource(\'customers\', CustomerController::class);</>');
        $this->line('4. Visit <fg=cyan>/doctypes</> to manage doctypes via web interface');
        $this->newLine();

        $this->info('📚 Documentation: See GENERATOR_EXAMPLES.md for detailed examples');

        return self::SUCCESS;
    }

    /**
     * Generate files for demo doctypes
     */
    private function generateDemoFiles(): int
    {
        $this->info('🔧 Generating demo files...');
        $this->newLine();

        $doctypes = ['Customer', 'Product'];

        foreach ($doctypes as $doctypeName) {
            $this->comment("Generating files for $doctypeName...");

            try {
                $this->call('doctype:generate', [
                    'doctype' => $doctypeName,
                    '--force' => true
                ]);

                $this->info("✓ $doctypeName files generated successfully");
            } catch (\Exception $e) {
                $this->error("✗ Failed to generate $doctypeName: " . $e->getMessage());
            }

            $this->newLine();
        }

        $this->info('🎉 Demo files generated!');
        $this->newLine();

        $this->info('Next steps:');
        $this->line('1. <fg=yellow>php artisan migrate</> (Run new migrations)');
        $this->line('2. Add API routes to routes/api.php:');
        $this->line('   <fg=green>Route::apiResource(\'customers\', CustomerController::class);</>');
        $this->line('   <fg=green>Route::apiResource(\'products\', ProductController::class);</>');

        return self::SUCCESS;
    }

    /**
     * Reset demo data
     */
    private function resetDemo(): int
    {
        $this->warn('Resetting demo data...');

        // Delete demo doctypes
        Doctype::whereIn('name', ['Customer', 'Product'])->delete();

        $this->info('✓ Demo doctypes deleted');

        // Optionally remove generated files
        if ($this->confirm('Do you want to remove generated demo files?', false)) {
            $filesToRemove = [
                app_path('Models/Customer.php'),
                app_path('Models/Product.php'),
                app_path('Http/Controllers/CustomerController.php'),
                app_path('Http/Controllers/ProductController.php'),
                app_path('Http/Requests/CustomerRequest.php'),
                app_path('Http/Requests/ProductRequest.php'),
                app_path('Http/Resources/CustomerResource.php'),
                app_path('Http/Resources/ProductResource.php'),
            ];

            foreach ($filesToRemove as $file) {
                if (file_exists($file)) {
                    unlink($file);
                    $this->line("Removed: $file");
                }
            }

            // Remove migration files (need to find them by pattern)
            $migrationPath = database_path('migrations');
            $migrations = glob($migrationPath . '/*_create_customers_table.php');
            $migrations = array_merge($migrations, glob($migrationPath . '/*_create_products_table.php'));

            foreach ($migrations as $migration) {
                unlink($migration);
                $this->line("Removed migration: " . basename($migration));
            }
        }

        $this->info('✓ Demo reset complete');

        return self::SUCCESS;
    }
}
