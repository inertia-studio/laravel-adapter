<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePanelCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:panel {name : Panel name (e.g. Admin, Customer)}';

    /** @var string */
    protected $description = 'Create a new Inertia Studio panel';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $path = '/'.Str::kebab($name);

        $directory = app_path("Studio/{$name}");
        $filePath = "{$directory}/{$name}.php";

        if (File::exists($filePath)) {
            $this->components->error("Panel [{$name}] already exists.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($directory);
        File::ensureDirectoryExists("{$directory}/Modules");

        $stub = File::get(__DIR__.'/../../stubs/panel.stub');

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ path }}'],
            ["App\\Studio\\{$name}", $name, $path],
            $stub,
        );

        File::put($filePath, $content);

        $this->components->info("Panel [{$name}] created successfully at [app/Studio/{$name}/{$name}.php].");

        return self::SUCCESS;
    }
}
