<?php

namespace VormiaCms\StarterKit\Console;

use Illuminate\Console\Command;
use VormiaCms\StarterKit\VormiaStarterKit;

class InstallCommand extends Command
{
    protected $signature = 'vormia:install';

    protected $description = 'Install Vormia CMS Starter Kit';

    public function handle()
    {
        $this->info('Installing Vormia CMS Starter Kit...');

        $starter = new VormiaStarterKit();
        $starter->install();

        // Add .gitignore entries
        $this->appendToGitIgnore([
            '# Custom Ignore',
            '.DS_Store',
            '/storage/app/public/media',
            '/public/media'
        ]);

        $this->info('Vormia CMS Starter Kit files have been installed successfully!');

        // Check if we should run database commands
        if (!$this->option('no-interaction') && !$this->confirm('Would you like to set up the database now? This will wipe your current database.', true)) {
            $this->info('Database setup skipped.');
            return;
        }

        $this->info('Setting up the database...');

        // Run database commands
        // $this->call('db:wipe');
        $this->call('migrate');
        $this->call('db:seed');

        // Run API installation
        $this->call('install:api');

        $this->info('Vormia CMS Starter Kit has been completely installed!');
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
