<?php

namespace Ngodingskuyy\Doctypes\Console\Commands;

use Illuminate\Console\Command;
use Ngodingskuyy\Doctypes\Services\DoctypeGeneratorService;
use Ngodingskuyy\Doctypes\Models\Doctype;

class GenerateDoctypeCommand extends Command
{
    protected $signature = 'doctype:generate 
                            {doctype : Name of the doctype to generate}
                            {--model : Generate model file}
                            {--controller : Generate controller file}
                            {--request : Generate request file}
                            {--resource : Generate resource file}
                            {--migration : Generate migration file}
                            {--all : Generate all files}
                            {--force : Overwrite existing files}
                            {--dry-run : Show what would be generated without creating files}';

    protected $description = 'Generate Laravel files from DocType configuration';

    protected $generator;

    public function __construct(DoctypeGeneratorService $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    public function handle()
    {
        $doctypeName = $this->argument('doctype');
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("Generating files for DocType: {$doctypeName}");

        // Find the doctype
        $doctype = Doctype::where('name', $doctypeName)->first();

        if (!$doctype) {
            $this->error("DocType '{$doctypeName}' not found!");
            $this->line('Available DocTypes:');
            Doctype::all()->each(function ($dt) {
                $this->line("  - {$dt->name}");
            });
            return 1;
        }

        // Determine what to generate
        $generateTypes = $this->getGenerationTypes();

        if (empty($generateTypes)) {
            $this->error('Please specify what to generate using --model, --controller, --request, --resource, --migration, or --all');
            return 1;
        }

        $this->line('');
        $this->info('Generation plan:');
        foreach ($generateTypes as $type) {
            $this->line("  ✓ {$type}");
        }

        if ($isDryRun) {
            $this->line('');
            $this->warn('DRY RUN - No files will be created');
            $this->showGenerationPreview($doctype, $generateTypes);
            return 0;
        }

        if (!$this->confirm('Proceed with generation?', true)) {
            $this->info('Generation cancelled.');
            return 0;
        }

        // Generate files
        $results = $this->generator->generateFiles($doctype, $generateTypes, $force);

        $this->line('');
        $this->info('Generation results:');

        foreach ($results as $type => $result) {
            if (isset($result['error'])) {
                $this->error("  ✗ {$type}: {$result['error']}");
            } else {
                $this->info("  ✓ {$type}: {$result['path']}");
            }
        }

        $this->line('');
        $this->info('Generation completed!');

        // Show next steps
        $this->showNextSteps($doctypeName, $generateTypes);

        return 0;
    }

    protected function getGenerationTypes(): array
    {
        $types = [];

        if ($this->option('all')) {
            return ['model', 'controller', 'request', 'resource', 'migration'];
        }

        if ($this->option('model'))
            $types[] = 'model';
        if ($this->option('controller'))
            $types[] = 'controller';
        if ($this->option('request'))
            $types[] = 'request';
        if ($this->option('resource'))
            $types[] = 'resource';
        if ($this->option('migration'))
            $types[] = 'migration';

        return $types;
    }

    protected function showGenerationPreview(Doctype $doctype, array $types): void
    {
        $this->line('');
        $this->line('Files that would be generated:');

        foreach ($types as $type) {
            $preview = $this->generator->getGenerationPreview($doctype, $type);
            $this->line("  {$type}: {$preview['path']}");

            if (file_exists($preview['full_path'])) {
                $this->warn("    (Warning: File already exists)");
            }
        }
    }

    protected function showNextSteps(string $doctypeName, array $types): void
    {
        $this->line('');
        $this->info('Next steps:');

        if (in_array('migration', $types)) {
            $this->line('1. Run migration:');
            $this->line('   php artisan migrate');
        }

        if (in_array('controller', $types)) {
            $this->line('2. Add routes to routes/api.php:');
            $this->line("   Route::apiResource('{$doctypeName}', {$this->getClassName($doctypeName)}Controller::class);");
        }

        if (in_array('model', $types)) {
            $this->line('3. You can now use the model:');
            $this->line("   \$data = {$this->getClassName($doctypeName)}::all();");
        }

        $this->line('');
        $this->line('For more examples, check: php artisan doctype:examples');
    }

    protected function getClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
}
