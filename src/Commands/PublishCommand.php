<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublishCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:publish
        {--pages : Publish Inertia page templates}
        {--component= : Publish a single component}
        {--group= : Publish a component group (form, table, layout, actions, widgets)}
        {--all : Publish all components}
        {--config : Publish config file}
        {--force : Overwrite existing files}';

    /** @var string */
    protected $description = 'Publish Inertia Studio assets and components';

    /** @var array<string, string> */
    protected array $frameworkExtensions = [
        'react' => 'tsx',
        'vue' => 'vue',
        'svelte' => 'svelte',
    ];

    /** @var array<string, array<string>> */
    protected array $componentGroups = [
        'form' => ['Form'],
        'table' => ['Table'],
        'layout' => ['Layout'],
        'actions' => ['Actions'],
        'widgets' => ['Widgets'],
    ];

    public function handle(): int
    {
        $framework = config('studio.framework', 'react');

        if ($this->option('config')) {
            $this->call('vendor:publish', [
                '--tag' => 'studio-config',
                '--force' => $this->option('force'),
            ]);

            $this->components->info('Configuration file published.');

            return self::SUCCESS;
        }

        if ($this->option('pages')) {
            return $this->publishPages($framework);
        }

        if ($component = $this->option('component')) {
            return $this->publishComponent($framework, $component);
        }

        if ($group = $this->option('group')) {
            return $this->publishGroup($framework, $group);
        }

        if ($this->option('all')) {
            return $this->publishAll($framework);
        }

        $this->components->error('Please specify what to publish: --pages, --component=, --group=, --all, or --config.');

        return self::FAILURE;
    }

    protected function publishPages(string $framework): int
    {
        $extension = $this->frameworkExtensions[$framework] ?? 'tsx';
        $sourcePath = $this->getUiPackagePath("src/{$framework}/pages");

        if (! File::isDirectory($sourcePath)) {
            $this->components->error("Page templates not found for framework [{$framework}].");

            return self::FAILURE;
        }

        $destinationPath = resource_path('js/Pages/Studio');

        $this->copyDirectory($sourcePath, $destinationPath);

        $this->components->info('Page templates published to [resources/js/Pages/Studio/].');

        return self::SUCCESS;
    }

    protected function publishComponent(string $framework, string $component): int
    {
        $extension = $this->frameworkExtensions[$framework] ?? 'tsx';
        $group = $this->guessComponentGroup($component);
        $sourcePath = $this->getUiPackagePath("src/{$framework}/components/{$group}/{$component}.{$extension}");

        if (! File::exists($sourcePath)) {
            $this->components->error("Component [{$component}] not found for framework [{$framework}].");

            return self::FAILURE;
        }

        $destinationPath = resource_path("js/studio/components/{$group}/{$component}.{$extension}");

        File::ensureDirectoryExists(dirname($destinationPath));

        if (File::exists($destinationPath) && ! $this->option('force')) {
            $this->components->error("Component [{$component}] already exists. Use --force to overwrite.");

            return self::FAILURE;
        }

        File::copy($sourcePath, $destinationPath);

        $this->components->info("Component [{$component}] published to [resources/js/studio/components/{$group}/{$component}.{$extension}].");

        return self::SUCCESS;
    }

    protected function publishGroup(string $framework, string $group): int
    {
        $group = Str::lower($group);

        if (! isset($this->componentGroups[$group])) {
            $this->components->error("Unknown component group [{$group}]. Available groups: ".implode(', ', array_keys($this->componentGroups)));

            return self::FAILURE;
        }

        $extension = $this->frameworkExtensions[$framework] ?? 'tsx';
        $groupDirectory = Str::studly($group);
        $sourcePath = $this->getUiPackagePath("src/{$framework}/components/{$groupDirectory}");

        if (! File::isDirectory($sourcePath)) {
            $this->components->error("Component group [{$group}] not found for framework [{$framework}].");

            return self::FAILURE;
        }

        $destinationPath = resource_path("js/studio/components/{$groupDirectory}");

        $this->copyDirectory($sourcePath, $destinationPath);

        $this->components->info("Component group [{$group}] published to [resources/js/studio/components/{$groupDirectory}/].");

        return self::SUCCESS;
    }

    protected function publishAll(string $framework): int
    {
        $sourcePath = $this->getUiPackagePath("src/{$framework}/components");

        if (! File::isDirectory($sourcePath)) {
            $this->components->error("Components not found for framework [{$framework}].");

            return self::FAILURE;
        }

        $destinationPath = resource_path('js/studio/components');

        $this->copyDirectory($sourcePath, $destinationPath);

        $this->components->info('All components published to [resources/js/studio/components/].');

        return self::SUCCESS;
    }

    protected function copyDirectory(string $source, string $destination): void
    {
        File::ensureDirectoryExists($destination);

        $force = $this->option('force');

        foreach (File::allFiles($source) as $file) {
            $destFile = $destination.'/'.$file->getRelativePathname();

            if (File::exists($destFile) && ! $force) {
                $this->comment("Skipping [{$file->getRelativePathname()}] (already exists).");

                continue;
            }

            File::ensureDirectoryExists(dirname($destFile));
            File::copy($file->getPathname(), $destFile);

            $this->info("Published [{$file->getRelativePathname()}].");
        }
    }

    protected function getUiPackagePath(string $path = ''): string
    {
        $basePath = dirname(__DIR__, 3).'/ui';

        return $path ? "{$basePath}/{$path}" : $basePath;
    }

    protected function guessComponentGroup(string $component): string
    {
        $formComponents = ['TextField', 'TextareaField', 'SelectField', 'ToggleField', 'DateField', 'NumberField', 'FileField', 'BelongsToField'];
        $tableComponents = ['TextColumn', 'BooleanColumn', 'DateColumn', 'BadgeColumn'];

        if (in_array($component, $formComponents) || Str::endsWith($component, 'Field')) {
            return 'Form';
        }

        if (in_array($component, $tableComponents) || Str::endsWith($component, 'Column')) {
            return 'Table';
        }

        return 'Layout';
    }
}
