<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:page
        {name : Page name (e.g. Analytics)}
        {--module= : Module to attach to (optional)}
        {--panel=Admin : Panel}';

    /** @var string */
    protected $description = 'Create a new Inertia Studio page';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $panel = Str::studly($this->option('panel'));
        $module = $this->option('module');

        $slug = Str::kebab($name);
        $label = Str::headline($name);

        if ($module) {
            $module = Str::studly($module);
            $namespace = "App\\Studio\\{$panel}\\Modules\\{$module}\\Pages";
            $directory = app_path("Studio/{$panel}/Modules/{$module}/Pages");
            $relativePath = "app/Studio/{$panel}/Modules/{$module}/Pages/{$name}.php";
        } else {
            $namespace = "App\\Studio\\{$panel}\\Pages";
            $directory = app_path("Studio/{$panel}/Pages");
            $relativePath = "app/Studio/{$panel}/Pages/{$name}.php";
        }

        $filePath = "{$directory}/{$name}.php";

        if (File::exists($filePath)) {
            $this->components->error("Page [{$name}] already exists.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($directory);

        $stub = File::get(__DIR__.'/../../stubs/page.stub');

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ slug }}', '{{ label }}'],
            [$namespace, $name, $slug, $label],
            $stub,
        );

        File::put($filePath, $content);

        $this->components->info("Page [{$name}] created successfully at [{$relativePath}].");

        return self::SUCCESS;
    }
}
