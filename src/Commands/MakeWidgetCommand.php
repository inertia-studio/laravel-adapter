<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:widget
        {name : Widget name (e.g. StatsOverview)}
        {--panel=Admin : Panel}';

    /** @var string */
    protected $description = 'Create a new Inertia Studio widget';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $panel = Str::studly($this->option('panel'));

        $directory = app_path("Studio/{$panel}/Widgets");
        $filePath = "{$directory}/{$name}.php";

        if (File::exists($filePath)) {
            $this->components->error("Widget [{$name}] already exists.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($directory);

        $stub = File::get(__DIR__.'/../../stubs/widget.stub');

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ["App\\Studio\\{$panel}\\Widgets", $name],
            $stub,
        );

        File::put($filePath, $content);

        $this->components->info("Widget [{$name}] created successfully at [app/Studio/{$panel}/Widgets/{$name}.php].");

        return self::SUCCESS;
    }
}
