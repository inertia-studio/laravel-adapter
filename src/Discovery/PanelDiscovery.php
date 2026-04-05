<?php

namespace InertiaStudio\Laravel\Discovery;

use InertiaStudio\Module;
use InertiaStudio\Panel;
use ReflectionClass;

class PanelDiscovery
{
    /**
     * Discover panels from app/Studio/{Name}/{Name}.php
     * and their modules from app/Studio/{Name}/Modules/*.php
     *
     * @return array<class-string<Panel>, array<class-string<Module>>>
     */
    public static function discover(string $basePath, string $baseNamespace = 'App\\Studio'): array
    {
        $results = [];

        if (! is_dir($basePath)) {
            return $results;
        }

        // Scan immediate subdirectories of app/Studio/
        $directories = glob($basePath.'/*', GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $dirName = basename($directory);

            // Look for {Name}.php in the directory
            $panelFile = $directory.'/'.$dirName.'.php';

            if (! file_exists($panelFile)) {
                continue;
            }

            $panelClass = $baseNamespace.'\\'.$dirName.'\\'.$dirName;

            if (! class_exists($panelClass)) {
                continue;
            }

            $reflection = new ReflectionClass($panelClass);

            if ($reflection->isAbstract() || ! $reflection->isSubclassOf(Panel::class)) {
                continue;
            }

            // Discover modules from Modules/ subdirectory
            $modules = static::discoverModules(
                $directory.'/Modules',
                $baseNamespace.'\\'.$dirName.'\\Modules',
            );

            $results[$panelClass] = $modules;
        }

        return $results;
    }

    /**
     * @return array<class-string<Module>>
     */
    protected static function discoverModules(string $modulesPath, string $namespace): array
    {
        $modules = [];

        if (! is_dir($modulesPath)) {
            return $modules;
        }

        $files = glob($modulesPath.'/*.php');

        foreach ($files as $file) {
            $className = $namespace.'\\'.pathinfo($file, PATHINFO_FILENAME);

            if (! class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract() || ! $reflection->isSubclassOf(Module::class)) {
                continue;
            }

            $modules[] = $className;
        }

        return $modules;
    }
}
