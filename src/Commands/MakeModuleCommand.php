<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:module
        {name : Module name (e.g. Users, Orders)}
        {--panel=Admin : Panel to create module in}
        {--simple : Create simple modal CRUD module}
        {--generate : Auto-generate from database schema}';

    /** @var string */
    protected $description = 'Create a new Inertia Studio module';

    /** @var array<string> */
    protected array $skipColumns = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'password',
        'remember_token',
    ];

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $panel = Str::studly($this->option('panel'));
        $model = Str::singular($name);
        $modelClass = "App\\Models\\{$model}";

        $directory = app_path("Studio/{$panel}/Modules");
        $filePath = "{$directory}/{$name}.php";

        if (File::exists($filePath)) {
            $this->components->error("Module [{$name}] already exists in panel [{$panel}].");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($directory);

        $stubFile = $this->option('simple') ? 'module.simple.stub' : 'module.stub';
        $stub = File::get(__DIR__.'/../../stubs/'.$stubFile);

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelClass }}'],
            ["App\\Studio\\{$panel}\\Modules", $name, $model, $modelClass],
            $stub,
        );

        if ($this->option('generate')) {
            $content = $this->generateFromSchema($content, $modelClass);
        }

        File::put($filePath, $content);

        $this->components->info("Module [{$name}] created successfully at [app/Studio/{$panel}/Modules/{$name}.php].");

        return self::SUCCESS;
    }

    protected function generateFromSchema(string $content, string $modelClass): string
    {
        if (! class_exists($modelClass)) {
            $this->comment("Model [{$modelClass}] not found. Skipping schema generation.");

            return $content;
        }

        /** @var Model $model */
        $model = new $modelClass;
        $table = $model->getTable();

        if (! Schema::hasTable($table)) {
            $this->comment("Table [{$table}] not found. Skipping schema generation.");

            return $content;
        }

        $columns = Schema::getColumns($table);
        $fields = [];
        $tableColumns = [];

        foreach ($columns as $column) {
            $columnName = $column['name'];

            if (in_array($columnName, $this->skipColumns)) {
                continue;
            }

            $typeName = $column['type_name'];
            $type = $column['type'];

            if (Str::endsWith($columnName, '_id')) {
                $relationName = Str::camel(Str::beforeLast($columnName, '_id'));
                $fields[] = "            Field::belongsTo('{$relationName}')";
                $tableColumns[] = "                Column::text('{$columnName}')";

                continue;
            }

            [$field, $tableColumn] = $this->mapColumnType($columnName, $typeName, $type);

            if ($field !== null) {
                $fields[] = "            {$field}";
            }

            if ($tableColumn !== null) {
                $tableColumns[] = "                {$tableColumn}";
            }
        }

        $fieldString = implode(",\n", $fields).',';
        $columnString = implode(",\n", $tableColumns).',';

        $content = str_replace(
            "->schema([\n            //\n        ])",
            "->schema([\n{$fieldString}\n        ])",
            $content,
        );

        $content = str_replace(
            "->columns([\n                //\n            ])",
            "->columns([\n{$columnString}\n            ])",
            $content,
        );

        return $content;
    }

    /**
     * Map a database column type to Field and Column definitions.
     *
     * @return array{0: string|null, 1: string|null}
     */
    protected function mapColumnType(string $name, string $typeName, string $type): array
    {
        return match (true) {
            in_array($typeName, ['string', 'varchar', 'char']) => [
                "Field::text('{$name}')",
                "Column::text('{$name}')",
            ],
            in_array($typeName, ['text', 'longtext', 'mediumtext']) => [
                "Field::textarea('{$name}')",
                null,
            ],
            in_array($typeName, ['integer', 'bigint', 'smallint', 'tinyint']) && $type !== 'tinyint(1)' => [
                "Field::number('{$name}')",
                "Column::text('{$name}')",
            ],
            $typeName === 'boolean' || $type === 'tinyint(1)' => [
                "Field::toggle('{$name}')",
                "Column::boolean('{$name}')",
            ],
            $typeName === 'date' => [
                "Field::date('{$name}')",
                "Column::date('{$name}')",
            ],
            in_array($typeName, ['datetime', 'timestamp']) => [
                "Field::date('{$name}')->withTime()",
                "Column::date('{$name}')->dateTime()",
            ],
            in_array($typeName, ['decimal', 'float', 'double']) => [
                "Field::number('{$name}')",
                "Column::text('{$name}')",
            ],
            $typeName === 'json' => [
                "Field::textarea('{$name}')",
                null,
            ],
            $typeName === 'enum' => [
                "Field::select('{$name}')",
                "Column::text('{$name}')",
            ],
            default => [
                "Field::text('{$name}')",
                "Column::text('{$name}')",
            ],
        };
    }
}
