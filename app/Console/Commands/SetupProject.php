<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class SetupProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:setup {--fresh : Whether to refresh the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the project (runs npm install, build, migrations, and clears caches)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Project Setup...');

        // 1. NPM Install
        $this->warn('📦 Running npm install...');
        $this->runProcess('npm install');

        // 2. NPM Build
        $this->warn('🏗️ Building frontend assets (npm run build)...');
        $this->runProcess('npm run build');

        // 3. Database Migrations
        $this->warn('🗄️ Running migrations...');
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--seed' => true]);
        } else {
            $this->call('migrate', ['--force' => true]);
        }

        // 4. Clear Caches
        $this->warn('🧹 Clearing caches...');
        $this->call('optimize:clear');

        $this->info('✅ Project Setup Complete! You are ready to go.');
    }

    /**
     * Helper to run a shell process and stream the output to the console.
     */
    protected function runProcess(string $command)
    {
        $result = Process::run($command, function (string $type, string $output) {
            if ($type === 'err') {
                $this->line("<error>{$output}</error>");
            } else {
                $this->line("<info>{$output}</info>");
            }
        });

        if ($result->failed()) {
            $this->error("❌ Command failed: {$command}");
        }
    }
}
