<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRelationCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:relation
        {name : Relation name (e.g. PostsRelation)}
        {--module= : Parent module}
        {--panel=Admin : Panel}';

    /** @var string */
    protected $description = 'Create a new Inertia Studio relation manager';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $panel = Str::studly($this->option('panel'));
        $module = $this->option('module');

        if (! $module) {
            $this->components->error('The --module option is required.');

            return self::FAILURE;
        }

        $module = Str::studly($module);
        $relationship = Str::camel(Str::beforeLast($name, 'Relation'));

        if ($relationship === Str::camel($name)) {
            $relationship = Str::camel($name);
        }

        $directory = app_path("Studio/{$panel}/Modules/{$module}/Relations");
        $filePath = "{$directory}/{$name}.php";

        if (File::exists($filePath)) {
            $this->components->error("Relation [{$name}] already exists.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($directory);

        $stub = File::get(__DIR__.'/../../stubs/relation.stub');

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ relationship }}'],
            ["App\\Studio\\{$panel}\\Modules\\{$module}\\Relations", $name, $relationship],
            $stub,
        );

        File::put($filePath, $content);

        $this->components->info("Relation [{$name}] created successfully at [app/Studio/{$panel}/Modules/{$module}/Relations/{$name}.php].");

        return self::SUCCESS;
    }
}
