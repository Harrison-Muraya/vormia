<?php

namespace VormiaPHP\Vormia;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class VormiaVormia
{
    /**
     * Get the name of the vormia kit.
     */
    public function name(): string
    {
        return 'VormiaVormia';
    }

    /**
     * Get the description of the vormia kit.
     */
    public function description(): string
    {
        return 'Vormia - A complete vormia kit for Laravel 12';
    }

    /**
     * Install the vormia kit.
     */
    public function install(): void
    {
        // Your existing installation logic
        $this->copyStubs();
    }

    /**
     * Update the vormia kit.
     */
    public function update(): void
    {
        // Update only changed files
        $this->updateStubs();
    }

    /**
     * Uninstall the vormia kit.
     */
    public function uninstall(): void
    {
        // Remove installed files
        $this->removeInstalledFiles();
    }

    /**
     * Copy Stubs to directories.
     */
    protected function copyStubs(): void
    {
        $filesystem = new Filesystem();

        // Copy migrations
        $this->copyDirectory($filesystem, 'migrations', $this->databasePath('migrations'));

        // Copy models
        $this->copyDirectory($filesystem, 'models', $this->appPath('Models'));

        // Copy controllers
        $this->copyDirectory($filesystem, 'controllers', $this->appPath('Http/Controllers'));

        // Copy livewire components
        $this->copyDirectory($filesystem, 'livewire', $this->appPath('Livewire'));

        // Copy middleware
        $this->copyDirectory($filesystem, 'middleware', $this->appPath('Http/Middleware'));

        // Copy rules
        $this->copyDirectory($filesystem, 'rules', $this->appPath('Rules'));

        // Copy services
        $this->copyDirectory($filesystem, 'services', $this->appPath('Services'));

        // Copy views
        $this->copyDirectory($filesystem, 'views', $this->resourcePath('views'));

        // Copy seeders
        $this->copyDirectory($filesystem, 'seeders', $this->databasePath('seeders'));

        // Copy routes
        // $this->copyDirectory($filesystem, 'routes', $this->basePath('routes'));

        // Copy public assets
        $this->copyDirectory($filesystem, 'public', $this->publicPath());
    }

    /**
     * Update Stubs in directories.
     */
    protected function updateStubs(): void
    {
        $filesystem = new Filesystem();

        // Update migrations
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/migrations', $this->databasePath('migrations'));

        // Update models
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/models', $this->appPath('Models'));

        // Update controllers
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/controllers', $this->appPath('Http/Controllers'));

        // Update livewire components
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/livewire', $this->appPath('Livewire'));

        // Update middleware
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/middleware', $this->appPath('Http/Middleware'));

        // Update rules
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/rules', $this->appPath('Rules'));

        // Update services
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/services', $this->appPath('Services'));

        // Update views
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/views', $this->resourcePath('views'));

        // Update seeders
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/seeders', $this->databasePath('seeders'));

        // Update routes - be careful with routes as they might be heavily customized
        // You might want to skip this or implement special handling
        // $this->updateDirectory($filesystem, __DIR__ . '/stubs/routes', $this->basePath('routes'));

        // Update public assets
        $this->updateDirectory($filesystem, __DIR__ . '/stubs/public', $this->publicPath());
    }

    /**
     * Update a directory with new files.
     */
    protected function updateDirectory($filesystem, $source, $destination): void
    {
        if (!File::isDirectory($source)) {
            return;
        }

        // Create destination directory if it doesn't exist
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $files = $filesystem->files($source);

        foreach ($files as $file) {
            $destPath = $destination . '/' . $file->getFilename();

            // Check if destination file exists and compare modification times
            if (!File::exists($destPath) || filemtime($file->getPathname()) > filemtime($destPath)) {
                File::copy($file->getPathname(), $destPath);
            }
        }

        // Recursively update subdirectories
        $directories = $filesystem->directories($source);

        foreach ($directories as $directory) {
            $dirName = basename($directory);
            $this->updateDirectory(
                $filesystem,
                $directory,
                $destination . '/' . $dirName
            );
        }
    }

    /**
     * Remove all installed files.
     */
    protected function removeInstalledFiles(): void
    {
        $filesystem = new Filesystem();

        // Define directories to remove - these should match what was installed
        $directoriesToRemove = [
            // Controllers
            $this->appPath('Http/Controllers/Admin'),
            $this->appPath('Http/Controllers/Api/V1/Auth'),
            $this->appPath('Http/Controllers/Setup'),

            // Livewire components - use appropriate path
            $this->appPath('Livewire/LiveSetting.php'),

            // Middleware - only remove Vormia specific middleware
            $this->appPath('Http/Middleware/CheckRolePermission.php'),

            // Views - only remove Vormia specific views
            $this->resourcePath('views/vormia'),

            // Public assets
            $this->publicPath('vormia'),
        ];

        // Remove specific files from migrations, seeders, etc.
        $filesToRemove = [
            // Add specific migration, seeder, or other files
            // E.g., $this->databasePath('migrations/2023_01_01_000000_create_vormia_tables.php'),
        ];

        // Remove directories
        foreach ($directoriesToRemove as $directory) {
            $this->removeDirectory($filesystem, $directory);
        }

        // Remove specific files
        foreach ($filesToRemove as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }

    /**
     * Remove a directory if it exists.
     */
    protected function removeDirectory($filesystem, $path): void
    {
        if (File::isDirectory($path)) {
            $filesystem->deleteDirectory($path);
        }
    }

    /**
     * Helper to copy directories.
     */
    protected function copyDirectory(Filesystem $filesystem, $source, $destination): void
    {
        $source = __DIR__ . '/stubs/' . $source;

        if ($filesystem->isDirectory($source)) {
            $filesystem->ensureDirectoryExists($destination);
            $filesystem->copyDirectory($source, $destination);
        }
    }

    /**
     * Get the application path.
     */
    protected function appPath($path = ''): string
    {
        return app_path($path);
    }

    /**
     * Get the database path.
     */
    protected function databasePath($path = ''): string
    {
        return database_path($path);
    }

    /**
     * Get the resource path.
     */
    protected function resourcePath($path = ''): string
    {
        return resource_path($path);
    }

    /**
     * Get the base path.
     */
    protected function basePath($path = ''): string
    {
        return base_path($path);
    }

    /**
     * Get the config path.
     */
    protected function configPath($path = ''): string
    {
        return config_path($path);
    }

    /**
     * Get the public path.
     */
    protected function publicPath($path = ''): string
    {
        return public_path($path);
    }
}
