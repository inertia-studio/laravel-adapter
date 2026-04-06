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

        // 1. Publish config + assets
        $this->callSilently('vendor:publish', ['--tag' => 'studio-config']);
        $this->callSilently('vendor:publish', ['--tag' => 'studio-assets', '--force' => true]);
        $this->info('  Published config and assets.');

        // 2. Detect framework
        $framework = $this->option('framework') ?? $this->detectFramework();

        if (! $framework) {
            if ($this->input->isInteractive()) {
                $framework = $this->choice(
                    'Which frontend framework would you like to use?',
                    ['react', 'vue', 'svelte'],
                    'react',
                );
            } else {
                $framework = 'react';
            }
        }

        $this->updateConfigFramework($framework);
        $this->info("  Framework: {$framework}");

        // 3. Install Inertia + framework dependencies if missing
        $this->installDependencies($framework);

        // 4. Install npm package
        $this->installNpmPackage();

        // 5. Create app/Studio directory and default panel
        if (! File::isDirectory(app_path('Studio'))) {
            File::makeDirectory(app_path('Studio'), 0755, true);
        }

        $this->call('studio:panel', ['name' => 'Admin', '--no-interaction' => true]);

        // 6. Configure Vite plugin
        $this->configureVite($framework);

        // 7. Configure app entry
        $this->configureAppEntry($framework);

        // 8. Configure Tailwind
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

    protected function installDependencies(string $framework): void
    {
        $packageJsonPath = base_path('package.json');
        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $composerDeps = array_merge(
            $composerJson['require'] ?? [],
            $composerJson['require-dev'] ?? [],
        );

        $npmDeps = [];
        if (File::exists($packageJsonPath)) {
            $packageJson = json_decode(File::get($packageJsonPath), true);
            $npmDeps = array_merge(
                $packageJson['dependencies'] ?? [],
                $packageJson['devDependencies'] ?? [],
            );
        }

        $pm = $this->detectPackageManager();
        $missingComposer = [];
        $missingNpm = [];

        // Check Inertia Laravel
        if (! isset($composerDeps['inertiajs/inertia-laravel'])) {
            $missingComposer[] = 'inertiajs/inertia-laravel';
        }

        // Check framework npm packages
        $frameworkPackages = match ($framework) {
            'vue' => ['@inertiajs/vue3', 'vue', '@vitejs/plugin-vue'],
            'svelte' => ['@inertiajs/svelte', 'svelte', '@sveltejs/vite-plugin-svelte'],
            default => ['@inertiajs/react', 'react', 'react-dom', '@vitejs/plugin-react'],
        };

        foreach ($frameworkPackages as $pkg) {
            if (! isset($npmDeps[$pkg])) {
                $missingNpm[] = $pkg;
            }
        }

        // Check Inertia vite plugin
        if (! isset($npmDeps['@inertiajs/vite'])) {
            $missingNpm[] = '@inertiajs/vite';
        }

        if (empty($missingComposer) && empty($missingNpm)) {
            $this->info('  All dependencies already installed.');

            return;
        }

        // Show what's missing and ask
        $this->newLine();
        $this->components->warn('Missing dependencies detected:');

        if (! empty($missingComposer)) {
            foreach ($missingComposer as $pkg) {
                $this->line("    <comment>composer</comment> {$pkg}");
            }
        }

        if (! empty($missingNpm)) {
            foreach ($missingNpm as $pkg) {
                $this->line("    <comment>{$pm}</comment>     {$pkg}");
            }
        }

        $this->newLine();

        if ($this->input->isInteractive() && ! $this->confirm('Install missing dependencies?', true)) {
            $this->warn('  Skipped. Install them manually before running your app.');

            return;
        }

        // Install Composer dependencies
        if (! empty($missingComposer)) {
            $composerCmd = 'composer require '.implode(' ', $missingComposer).' --no-interaction';
            $this->info("  Running: {$composerCmd}");
            $result = Process::path(base_path())->timeout(120)->run($composerCmd);

            if ($result->successful()) {
                $this->info('  Composer dependencies installed.');
            } else {
                $this->error('  Failed to install Composer dependencies.');
                $this->line($result->errorOutput());
            }
        }

        // Install npm dependencies
        if (! empty($missingNpm)) {
            $installCmd = match ($pm) {
                'yarn' => 'yarn add '.implode(' ', $missingNpm),
                'pnpm' => 'pnpm add '.implode(' ', $missingNpm),
                'bun' => 'bun add '.implode(' ', $missingNpm),
                default => 'npm install '.implode(' ', $missingNpm),
            };

            $this->info("  Running: {$installCmd}");
            $result = Process::path(base_path())->timeout(120)->run($installCmd);

            if ($result->successful()) {
                $this->info('  npm dependencies installed.');
            } else {
                $this->error('  Failed to install npm dependencies.');
                $this->line($result->errorOutput());
            }
        }
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

        // Add imports
        $frameworkImport = match ($framework) {
            'vue' => "import vue from '@vitejs/plugin-vue';",
            'svelte' => "import { svelte } from '@sveltejs/vite-plugin-svelte';",
            default => "import react from '@vitejs/plugin-react';",
        };

        if (! str_contains($contents, '@inertia-studio/ui/vite')) {
            $contents = "import studio from '@inertia-studio/ui/vite';\n".$contents;
        }

        if (! str_contains($contents, $frameworkImport)) {
            $contents = "{$frameworkImport}\n".$contents;
        }

        // Add framework plugin + studio() to plugins array
        $frameworkPlugin = match ($framework) {
            'vue' => 'vue(',
            'svelte' => 'svelte(',
            default => 'react(',
        };

        if (str_contains($contents, $frameworkPlugin)) {
            // Framework plugin exists — add studio() after it
            if (! str_contains($contents, 'studio()')) {
                $contents = preg_replace(
                    '/('.preg_quote($frameworkPlugin, '/').'[^)]*\)\s*(?:,)?)/s',
                    "$1\n        studio(),",
                    $contents,
                    1,
                );
            }
            $this->info('  Added studio() to vite.config plugins.');
        } else {
            // Framework plugin missing — add both after laravel() or tailwindcss()
            $frameworkCall = match ($framework) {
                'vue' => 'vue(),',
                'svelte' => 'svelte(),',
                default => 'react(),',
            };

            $insertAfter = str_contains($contents, 'tailwindcss()') ? 'tailwindcss(),' : 'refresh: true,';
            if (str_contains($contents, $insertAfter)) {
                $contents = str_replace(
                    $insertAfter,
                    "{$insertAfter}\n        {$frameworkCall}\n        studio(),",
                    $contents,
                );
                $this->info("  Added {$frameworkCall} and studio() to vite.config plugins.");
            } else {
                $this->warn('  Could not auto-configure Vite. Add '.$frameworkCall.' and studio() to your plugins array manually.');
            }
        }

        // Update input entry from .js to .tsx for React
        if ($framework === 'react' && str_contains($contents, 'app.js')) {
            $contents = str_replace('app.js', 'app.tsx', $contents);
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
            // Create a fresh Inertia app entry
            $appContent = match ($framework) {
                'vue' => <<<'VUE'
import { resolveStudioPage } from '@inertia-studio/ui/vite';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';

const appPages = import.meta.glob('./pages/**/*.vue');

createInertiaApp({
    resolve: (name) => resolveStudioPage(name) ?? resolvePageComponent(`./pages/${name}.vue`, appPages),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
VUE,
                'svelte' => <<<'SVELTE'
import { resolveStudioPage } from '@inertia-studio/ui/vite';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createInertiaApp } from '@inertiajs/svelte';

const appPages = import.meta.glob('./pages/**/*.svelte');

createInertiaApp({
    resolve: (name) => resolveStudioPage(name) ?? resolvePageComponent(`./pages/${name}.svelte`, appPages),
});
SVELTE,
                default => <<<'REACT'
import { resolveStudioPage } from '@inertia-studio/ui/vite';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createInertiaApp } from '@inertiajs/react';

const appPages = import.meta.glob<{ default: React.ComponentType }>('./pages/**/*.tsx');

createInertiaApp({
    resolve: (name) => resolveStudioPage(name) ?? resolvePageComponent(`./pages/${name}.tsx`, appPages),
    progress: {
        color: '#4B5563',
    },
});
REACT,
            };

            File::put($entryPath, $appContent);
            $this->info("  Created app entry at resources/js/app.{$ext}");

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
