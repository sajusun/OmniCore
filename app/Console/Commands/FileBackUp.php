<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FileBackUp extends Command
{
    protected $signature = 'file:backup {--path=uploads : Directory to backup relative to public path}';
    
    protected $description = 'Create a backup of uploaded files in a ZIP archive';

    public function handle()
    {
        $this->info('Starting file backup...');
        
        $relativePath = $this->option('path');
        $sourcePath = public_path($relativePath);
        $backupDir = storage_path('app/backups');
        
        // Validate source directory exists
        if (!is_dir($sourcePath)) {
            $this->error("Source directory does not exist: {$sourcePath}");
            return Command::FAILURE;
        }
        
        // Create backup directory if it doesn't exist
        if (!$this->ensureBackupDirectory($backupDir)) {
            return Command::FAILURE;
        }
        
        $zipFileName = "file_backup_" . date("Y-m-d_H-i-s") . ".zip";
        $zipPath = $backupDir . DIRECTORY_SEPARATOR . $zipFileName;
        
        try {
            $fileCount = $this->createBackupZip($sourcePath, $zipPath, $relativePath);
            
            $this->info("✓ Backup completed successfully!");
            $this->info("📁 Files backed up: {$fileCount}");
            $this->info("📦 Backup saved to: {$zipPath}");
            $this->info("💾 File size: " . $this->formatBytes(filesize($zipPath)));
            
            Log::info("File backup completed", [
                'source' => $sourcePath,
                'backup' => $zipPath,
                'files_count' => $fileCount,
                'file_size' => filesize($zipPath)
            ]);
            
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            Log::error("File backup failed", [
                'source' => $sourcePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up incomplete backup file
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            
            return Command::FAILURE;
        }
    }
    
    private function ensureBackupDirectory($path)
    {
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                $this->error("Failed to create backup directory: {$path}");
                return false;
            }
        }
        
        if (!is_writable($path)) {
            $this->error("Backup directory is not writable: {$path}");
            return false;
        }
        
        return true;
    }
    
    private function createBackupZip($sourcePath, $zipPath, $relativePath)
    {
        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        if ($result !== TRUE) {
            throw new Exception("Cannot create ZIP file: " . $this->getZipError($result));
        }
        
        $fileCount = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        $this->output->progressStart($iterator->getMaxDepth() > 0 ? iterator_count($iterator) : 1);
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativeName = $relativePath . DIRECTORY_SEPARATOR . 
                               substr($filePath, strlen($sourcePath) + 1);
                
                // Normalize path separators for cross-platform compatibility
                $relativeName = str_replace('\\', '/', $relativeName);
                
                if ($zip->addFile($filePath, $relativeName)) {
                    $fileCount++;
                } else {
                    $this->warn("Failed to add file: {$filePath}");
                }
            }
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        
        if (!$zip->close()) {
            throw new Exception("Failed to close ZIP file");
        }
        
        return $fileCount;
    }
    
    private function getZipError($code)
    {
        switch($code) {
            case ZipArchive::ER_OK: return 'No error';
            case ZipArchive::ER_MULTIDISK: return 'Multi-disk zip archives not supported';
            case ZipArchive::ER_RENAME: return 'Renaming temporary file failed';
            case ZipArchive::ER_CLOSE: return 'Closing zip archive failed';
            case ZipArchive::ER_SEEK: return 'Seek error';
            case ZipArchive::ER_READ: return 'Read error';
            case ZipArchive::ER_WRITE: return 'Write error';
            case ZipArchive::ER_CRC: return 'CRC error';
            case ZipArchive::ER_ZIPCLOSED: return 'Containing zip archive was closed';
            case ZipArchive::ER_NOENT: return 'No such file';
            case ZipArchive::ER_EXISTS: return 'File already exists';
            case ZipArchive::ER_OPEN: return 'Can\'t open file';
            case ZipArchive::ER_TMPOPEN: return 'Failure to create temporary file';
            case ZipArchive::ER_ZLIB: return 'Zlib error';
            case ZipArchive::ER_MEMORY: return 'Memory allocation failure';
            case ZipArchive::ER_CHANGED: return 'Entry has been changed';
            case ZipArchive::ER_COMPNOTSUPP: return 'Compression method not supported';
            case ZipArchive::ER_EOF: return 'Premature EOF';
            case ZipArchive::ER_INVAL: return 'Invalid argument';
            case ZipArchive::ER_NOZIP: return 'Not a zip archive';
            case ZipArchive::ER_INTERNAL: return 'Internal error';
            case ZipArchive::ER_INCONS: return 'Zip archive inconsistent';
            case ZipArchive::ER_REMOVE: return 'Can\'t remove file';
            case ZipArchive::ER_DELETED: return 'Entry has been deleted';
            default: return "Unknown error code: {$code}";
        }
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}