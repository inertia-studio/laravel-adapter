<?php

namespace InertiaStudio\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class InstallCommand extends Command
{
    /** @var string */
    protected $signature = 'studio:install
        {--framework= : Frontend framework (react, vue, svelte)}';

    /** @var string */
    protected $description = 'Install Inertia Studio and scaffold the default panel';

    public function handle(): int
    {
        $this->components->info('Installing Inertia Studio...');

        // 1. Publish config
        $this->callSilently('vendor:publish', ['--tag' => 'studio-config']);
        $this->info('  Published config/studio.php');

        // 2. Detect framework
        $framework = $this->option('framework') ?? $this->detectFramework();

        if (! $framework) {
            $framework = $this->choice(
                'Which frontend framework are you using?',
                ['react', 'vue', 'svelte'],
                'react',
            );
        }

        $this->updateConfigFramework($framework);
        $this->info("  Framework: {$framework}");

        // 3. Install npm package
        $this->installNpmPackage();

        // 4. Create app/Studio directory and default panel
        if (! File::isDirectory(app_path('Studio'))) {
            File::makeDirectory(app_path('Studio'), 0755, true);
        }

        $this->call('studio:panel', ['name' => 'Admin', '--no-interaction' => true]);

        // 5. Configure Vite plugin
        $this->configureVite($framework);

        // 6. Configure app entry
        $this->configureAppEntry($framework);

        // 7. Configure Tailwind
        $this->configureTailwind();

        $this->newLine();
        $this->components->info('Inertia Studio installed successfully.');

        $this->newLine();
        $this->comment('  Run your dev server:');
        $this->comment('    composer run dev');
        $this->comment('');
        $this->comment('  Then visit /admin');

        return self::SUCCESS;
    }

    protected function installNpmPackage(): void
    {
        $packageJsonPath = base_path('package.json');

        if (! File::exists($packageJsonPath)) {
            $this->warn('  No package.json found, skipping npm install.');

            return;
        }

        $packageJson = json_decode(File::get($packageJsonPath), true);
        $allDeps = array_merge(
            $packageJson['dependencies'] ?? [],
            $packageJson['devDependencies'] ?? [],
        );

        if (isset($allDeps['@inertia-studio/ui'])) {
            $this->info('  @inertia-studio/ui already installed.');

            return;
        }

        $this->info('  Installing @inertia-studio/ui...');

        // Detect package manager
        $pm = $this->detectPackageManager();
        $installCmd = match ($pm) {
            'yarn' => 'yarn add @inertia-studio/ui',
            'pnpm' => 'pnpm add @inertia-studio/ui',
            'bun' => 'bun add @inertia-studio/ui',
            default => 'npm install @inertia-studio/ui',
        };

        $result = Process::path(base_path())->run($installCmd);

        if ($result->successful()) {
            $this->info("  Installed @inertia-studio/ui via {$pm}.");
        } else {
            $this->warn("  Failed to install @inertia-studio/ui. Run manually: {$installCmd}");
        }
    }

    protected function configureVite(string $framework): void
    {
        $viteConfigPath = base_path('vite.config.ts');

        if (! File::exists($viteConfigPath)) {
            $viteConfigPath = base_path('vite.config.js');
        }

        if (! File::exists($viteConfigPath)) {
            $this->warn('  No vite.config found, skipping Vite plugin setup.');

            return;
        }

        $contents = File::get($viteConfigPath);

        // Already configured?
        if (str_contains($contents, '@inertia-studio/ui/vite')) {
            $this->info('  Vite plugin already configured.');

            return;
        }

        // Add import
        $contents = "import studio from '@inertia-studio/ui/vite';\n".$contents;

        // Add studio() to plugins array — insert after react()/vue()/svelte()
        $frameworkPlugin = match ($framework) {
            'vue' => 'vue(',
            'svelte' => 'svelte(',
            default => 'react(',
        };

        if (str_contains($contents, $frameworkPlugin)) {
            $contents = preg_replace(
                '/('.preg_quote($frameworkPlugin, '/').'[^)]*\)\s*(?:,)?)/s',
                "$1\n        studio(),",
                $contents,
                1,
            );
            $this->info('  Added studio() to vite.config plugins.');
        } else {
            $this->warn('  Could not auto-configure Vite. Add studio() to your plugins array manually.');
        }

        File::put($viteConfigPath, $contents);
    }

    protected function configureAppEntry(string $framework): void
    {
        $ext = match ($framework) {
            'vue' => 'ts',
            'svelte' => 'ts',
            default => 'tsx',
        };

        $entryPath = resource_path("js/app.{$ext}");

        if (! File::exists($entryPath)) {
            return;
        }

        $contents = File::get($entryPath);

        if (str_contains($contents, 'resolveStudioPage')) {
            $this->info('  App entry already configured.');

            return;
        }

        // Add imports
        if (! str_contains($contents, '@inertia-studio/ui/vite')) {
            $contents = "import { resolveStudioPage } from '@inertia-studio/ui/vite';\n".$contents;
        }

        if (! str_contains($contents, 'resolvePageComponent') && ! str_contains($contents, 'laravel-vite-plugin/inertia-helpers')) {
            $contents = "import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';\n".$contents;
        }

        $hasResolveProperty = (bool) preg_match('/\bresolve\s*[:({]/', $contents);

        // Case 1: Already has a resolve property with resolvePageComponent
        if ($hasResolveProperty && str_contains($contents, 'resolvePageComponent')) {
            $contents = preg_replace(
                '/(resolve:\s*\(name\)\s*(?:=>|{)\s*(?:return\s+)?)(\s*resolvePageComponent)/s',
                "$1resolveStudioPage(name) ??\n$2",
                $contents,
                1,
            );
            $this->info('  Added resolveStudioPage() to existing resolve function.');
        }
        // Case 2: Has createInertiaApp but no resolve property (e.g. @inertiajs/vite auto-resolution)
        elseif (str_contains($contents, 'createInertiaApp') && ! $hasResolveProperty) {
            // Add resolvePageComponent import
            if (! str_contains($contents, 'resolvePageComponent')) {
                $contents = "import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';\n".$contents;
            }

            // Add page glob before createInertiaApp
            if (! str_contains($contents, 'import.meta.glob')) {
                $contents = str_replace(
                    'createInertiaApp(',
                    "const appPages = import.meta.glob<{ default: React.ComponentType }>('./pages/**/*.{$ext}');\n\ncreateInertiaApp(",
                    $contents,
                );
            }

            // Insert resolve property right after createInertiaApp({
            $tpl = '`./pages/${name}.' . $ext . '`';
            $resolveCode = "\n    resolve: (name) => resolveStudioPage(name) ?? resolvePageComponent({$tpl}, appPages),";
            $contents = preg_replace(
                '/(createInertiaApp\(\{)\s*\n/',
                "$1{$resolveCode}\n",
                $contents,
                1,
            );

            // Add Studio:: guard to layout function
            if (preg_match('/layout[:\s(]/', $contents)) {
                $contents = preg_replace(
                    '/(layout\s*[\(:]\s*\(name[^)]*\)\s*(?:=>)\s*\{)\s*\n/',
                    "$1\n        if (name.startsWith('Studio::')) return null;\n",
                    $contents,
                    1,
                );
            }

            $this->info('  Configured page resolver in app entry.');
        } else {
            $this->warn('  Could not auto-configure page resolver. See docs for manual setup.');
        }

        File::put($entryPath, $contents);
    }

    protected function configureTailwind(): void
    {
        $cssPath = resource_path('css/app.css');

        if (! File::exists($cssPath)) {
            return;
        }

        $contents = File::get($cssPath);

        if (str_contains($contents, '@inertia-studio/ui')) {
            $this->info('  Tailwind sources already configured.');

            return;
        }

        // Add Studio CSS import (design tokens, dark mode, transitions)
        $studioImport = "@import '@inertia-studio/ui/studio.css';";

        // Add @source directives
        $sourceLines = "@source '../../vendor/inertia-studio/laravel-adapter';\n@source '../../node_modules/@inertia-studio/ui';";

        // Build the block to inject
        $injection = "\n{$studioImport}\n\n{$sourceLines}\n";

        if (str_contains($contents, '@source')) {
            // Add after last @source line
            $contents = preg_replace(
                '/(@source\s+[^\n]+\n)(?!@source)/s',
                "$1{$injection}",
                $contents,
                1,
            );
        } elseif (str_contains($contents, "@import 'tailwindcss'")) {
            $contents = str_replace(
                "@import 'tailwindcss';",
                "@import 'tailwindcss';\n{$injection}",
                $contents,
            );
        }

        File::put($cssPath, $contents);
        $this->info('  Added Studio styles and Tailwind source paths.');
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

    protected function detectPackageManager(): string
    {
        if (File::exists(base_path('bun.lockb')) || File::exists(base_path('bun.lock'))) {
            return 'bun';
        }

        if (File::exists(base_path('pnpm-lock.yaml'))) {
            return 'pnpm';
        }

        if (File::exists(base_path('yarn.lock'))) {
            return 'yarn';
        }

        return 'npm';
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
