<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use ZipArchive;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup {--path= : Custom backup path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        try {
            // Validate database configuration
            $this->validateDatabaseConfig();

            // Generate filename
            $filename = "database_backup_" . now()->format('Y-m-d_H-i-s') . ".sql";
            
            // Determine backup path
            $backupPath = $this->option('path') ?? 'backups';
            $fullPath = storage_path("app/{$backupPath}");
            
            // Ensure backup directory exists
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            $filePath = "{$fullPath}/{$filename}";

            // Build mysqldump command
            $command = $this->buildMysqlDumpCommand($filePath);

            // Execute backup
            $this->executeBackup($command, $filePath);

            // Create ZIP archive
            $zipPath = $this->createZipBackup($filePath);

            $this->info("Database backup completed successfully!");
            $this->info("Backup saved to: {$zipPath}");

        } catch (Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Validate database configuration
     */
    private function validateDatabaseConfig()
    {
        $required = ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
        
        foreach ($required as $key) {
            if (empty(env($key))) {
                throw new Exception("Missing required environment variable: {$key}");
            }
        }
    }

    /**
     * Build the mysqldump command
     */
    private function buildMysqlDumpCommand(string $filePath): string
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT', 3306);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        // Build command with proper escaping
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --single-transaction --routines --triggers --lock-tables=false %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        return $command;
    }

    /**
     * Execute the backup command
     */
    private function executeBackup(string $command, string $filePath)
    {
        $this->info('Executing database dump...');
        
        $output = [];
        $returnVar = null;
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            // Clean up failed backup file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            throw new Exception("mysqldump failed with exit code: {$returnVar}. Output: " . implode("\n", $output));
        }

        if (!file_exists($filePath) || filesize($filePath) === 0) {
            throw new Exception("Backup file was not created or is empty");
        }
    }

    /**
     * Create ZIP archive of the backup
     */
    private function createZipBackup(string $filePath): string
    {
        $this->info('Creating ZIP archive...');
        
        $zipPath = str_replace('.sql', '.zip', $filePath);
        
        // Check if ZipArchive is available
        if (!class_exists('ZipArchive')) {
            throw new Exception("ZipArchive class is not available. Please install php-zip extension.");
        }
        
        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        if ($result !== TRUE) {
            throw new Exception("Cannot create ZIP file: {$zipPath}. Error code: {$result}");
        }
        
        // Add SQL file to ZIP with just the filename (not full path)
        $zip->addFile($filePath, basename($filePath));
        $zip->close();
        
        // Verify ZIP was created
        if (!file_exists($zipPath) || filesize($zipPath) === 0) {
            throw new Exception("ZIP file was not created or is empty");
        }
        
        // Remove the original SQL file
        unlink($filePath);
        
        $this->info('ZIP archive created successfully');
        
        return $zipPath;
    }
}