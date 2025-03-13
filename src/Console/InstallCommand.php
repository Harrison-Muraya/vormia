<?php

namespace VormiaCms\StarterKit\Console;

use Illuminate\Console\Command;
use VormiaCms\StarterKit\VormiaStarterKit;

class InstallCommand extends Command
{
    protected $signature = 'vormia:install {--no-interaction : Run without asking for confirmation}';

    protected $description = 'Install Vormia Starter Kit';

    public function handle()
    {
        $this->info('Installing Vormia Starter Kit...');

        // Make sure Sanctum is installed
        if (!class_exists(\Laravel\Sanctum\HasApiTokens::class)) {
            $this->error('Laravel Sanctum is required but not installed.');
            $this->info('Please run: composer require laravel/sanctum');
            return 1;
        }

        $starter = new VormiaStarterKit();
        $starter->install();

        // Add .gitignore entries
        $this->appendToGitIgnore([
            '# Custom Ignore',
            '.DS_Store',
            '/storage/app/public/media',
            '/public/media'
        ]);

        $this->info('Vormia Starter Kit files have been installed successfully!');

        // Run API installation
        $this->call('install:api');

        // Check if we should run database commands
        if (!$this->option('no-interaction') && !$this->confirm('Would you like to set up the database now? Backup your database.', true)) {
            $this->info('Database setup skipped.');
            return;
        }

        $this->info('Setting up the database...');

        // Run database commands
        $this->call('migrate');
        $this->call('db:seed');

        $this->info('Vormia Starter Kit has been completely installed!');
        $this->info('Remember to run "php artisan serve" to start your application.');
    }

    /**
     * Append lines to .gitignore.
     */
    protected function appendToGitIgnore(array $lines)
    {
        $gitignorePath = base_path('.gitignore');
        $content = file_exists($gitignorePath) ? file_get_contents($gitignorePath) : '';

        // Add each line if it doesn't already exist
        foreach ($lines as $line) {
            if (!str_contains($content, $line)) {
                $content .= PHP_EOL . $line;
            }
        }

        file_put_contents($gitignorePath, $content);
    }
}
