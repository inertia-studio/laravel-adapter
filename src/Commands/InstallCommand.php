<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:install {--framework= : Frontend framework (react, vue, svelte)}';

    /** @var string */
    protected $description = 'Install Inertia Studio and scaffold the default panel';

    public function handle(): int
    {
        $this->components->info('Installing Inertia Studio...');

        $this->callSilently('vendor:publish', [
            '--tag' => 'studio-config',
        ]);

        $this->info('Published configuration file.');

        $framework = $this->option('framework') ?? $this->detectFramework();

        if ($framework) {
            $this->updateConfigFramework($framework);
            $this->info("Framework detected: {$framework}");
        }

        if (! File::isDirectory(app_path('Studio'))) {
            File::makeDirectory(app_path('Studio'), 0755, true);
            $this->info('Created app/Studio/ directory.');
        }

        $this->call('studio:panel', ['name' => 'Admin']);

        $this->newLine();
        $this->components->info('Inertia Studio installed successfully.');

        $this->newLine();
        $this->comment('Next steps:');
        $this->comment('  1. Install the frontend package: npm install @inertia-studio/ui');
        $this->comment('  2. Add the Tailwind preset to your tailwind.config.js');
        $this->comment('  3. Visit /admin to see your panel');

        return self::SUCCESS;
    }

    protected function detectFramework(): ?string
    {
        $packageJsonPath = base_path('package.json');

        if (! File::exists($packageJsonPath)) {
            return null;
        }

        $packageJson = json_decode(File::get($packageJsonPath), true);
        $dependencies = array_merge(
            $packageJson['dependencies'] ?? [],
            $packageJson['devDependencies'] ?? [],
        );

        if (isset($dependencies['@inertiajs/react'])) {
            return 'react';
        }

        if (isset($dependencies['@inertiajs/vue3'])) {
            return 'vue';
        }

        if (isset($dependencies['@inertiajs/svelte'])) {
            return 'svelte';
        }

        return null;
    }

    protected function updateConfigFramework(string $framework): void
    {
        $configPath = config_path('studio.php');

        if (! File::exists($configPath)) {
            return;
        }

        $contents = File::get($configPath);
        $contents = preg_replace(
            "/'framework'\s*=>\s*'[^']*'/",
            "'framework' => '{$framework}'",
            $contents,
        );

        File::put($configPath, $contents);
    }
}
