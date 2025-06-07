<?php
/*
 * @package     :   Sepremex ManagerFile
 * @handler     :   Master Router
 * @developer   :   Ruben Mc
 * @contact     :   webcorm@gmail.com
 * @company     :   Sepremex | PathHelper.php
 * @date        :   6/4/2025 | 22:07
*/

namespace Sepremex\FilamentTranslationEditor\Utils;

use Illuminate\Support\Facades\File;

class PathHelper
{
    /**
     * Get the configured language path
     */
    public static function getLanguagePath(): string
    {
        return config('filament-translation-editor.language_path', base_path('lang'));
    }

    /**
     * Get the path for a specific language directory
     */
    public static function getLanguageDirectory(string $languageCode): string
    {
        return static::getLanguagePath() . DIRECTORY_SEPARATOR . $languageCode;
    }

    /**
     * Get the path for a PHP translation file
     */
    public static function getPhpFilePath(string $languageCode, string $filename): string
    {
        $filename = static::sanitizeFilename($filename);
        $filename = str_replace('.php', '', $filename); // Remove .php if present

        return static::getLanguageDirectory($languageCode) . DIRECTORY_SEPARATOR . $filename . '.php';
    }

    /**
     * Get the path for a JSON translation file
     */
    public static function getJsonFilePath(string $languageCode): string
    {
        return static::getLanguagePath() . DIRECTORY_SEPARATOR . $languageCode . '.json';
    }

    /**
     * Check if the configured language path exists
     */
    public static function languagePathExists(): bool
    {
        return File::exists(static::getLanguagePath());
    }

    /**
     * Check if a language directory exists
     */
    public static function languageDirectoryExists(string $languageCode): bool
    {
        return File::exists(static::getLanguageDirectory($languageCode));
    }

    /**
     * Check if a PHP translation file exists
     */
    public static function phpFileExists(string $languageCode, string $filename): bool
    {
        return File::exists(static::getPhpFilePath($languageCode, $filename));
    }

    /**
     * Check if a JSON translation file exists
     */
    public static function jsonFileExists(string $languageCode): bool
    {
        return File::exists(static::getJsonFilePath($languageCode));
    }

    /**
     * Create language directory if it doesn't exist
     */
    public static function ensureLanguageDirectoryExists(string $languageCode): bool
    {
        $directory = static::getLanguageDirectory($languageCode);

        if(!File::exists($directory)){
            return File::makeDirectory($directory, 0755, true);
        }

        return true;
    }

    /**
     * Create language path if it doesn't exist
     */
    public static function ensureLanguagePathExists(): bool
    {
        $path = static::getLanguagePath();

        if(!File::exists($path)){
            return File::makeDirectory($path, 0755, true);
        }

        return true;
    }

    /**
     * Get all language codes from existing directories
     */
    public static function getExistingLanguageCodes(): array
    {
        if(!static::languagePathExists()){
            return [];
        }

        $directories = File::directories(static::getLanguagePath());
        $languageCodes = [];

        foreach($directories as $directory) {
            $languageCode = basename($directory);

            // Validate language code format
            if(static::isValidLanguageCode($languageCode)){
                $languageCodes[] = $languageCode;
            }
        }

        // Also check for JSON files
        $jsonFiles = File::glob(static::getLanguagePath() . '/*.json');

        foreach($jsonFiles as $jsonFile) {
            $languageCode = pathinfo($jsonFile, PATHINFO_FILENAME);

            if(static::isValidLanguageCode($languageCode) && !in_array($languageCode, $languageCodes)){
                $languageCodes[] = $languageCode;
            }
        }

        sort($languageCodes);

        return $languageCodes;
    }

    /**
     * Get all PHP files for a language
     */
    public static function getPhpFilesForLanguage(string $languageCode): array
    {
        $directory = static::getLanguageDirectory($languageCode);

        if(!File::exists($directory)){
            return [];
        }

        $files = File::files($directory);
        $phpFiles = [];
        $excludedFiles = config('filament-translation-editor.excluded_files', []);

        foreach($files as $file) {
            if($file->getExtension() === 'php' && !in_array($file->getFilename(), $excludedFiles)){
                $phpFiles[] = ['filename' => $file->getFilenameWithoutExtension(), 'full_filename' => $file->getFilename(), 'path' => $file->getPathname(), 'size' => $file->getSize(), 'modified' => $file->getMTime(),];
            }
        }

        return $phpFiles;
    }

    /**
     * Validate language code format
     */
    public static function isValidLanguageCode(string $languageCode): bool
    {
        // Basic validation for ISO language codes
        return preg_match('/^[a-z]{2,3}(-[A-Z]{2})?$/', $languageCode) === 1;
    }

    /**
     * Sanitize filename to prevent directory traversal
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove directory separators and other potentially dangerous characters
        $filename = str_replace(['/', '\\', '..', "\0"], '', $filename);

        // Remove any non-alphanumeric characters except hyphens, underscores, and dots
        $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $filename);

        return $filename;
    }

    /**
     * Get relative path from base path
     */
    public static function getRelativePath(string $fullPath): string
    {
        $basePath = base_path();

        if(str_starts_with($fullPath, $basePath)){
            return ltrim(substr($fullPath, strlen($basePath)), DIRECTORY_SEPARATOR);
        }

        return $fullPath;
    }

    /**
     * Generate backup path for a file
     */
    public static function getBackupPath(string $languageCode, string $filename, string $type = 'php'): string
    {
        $backupDir = storage_path('app/translation-backups');
        $timestamp = now()->format('Y-m-d_H-i-s');

        if($type === 'json'){
            return $backupDir . DIRECTORY_SEPARATOR . "{$languageCode}_{$timestamp}.json";
        }

        $filename = static::sanitizeFilename($filename);
        return $backupDir . DIRECTORY_SEPARATOR . "{$languageCode}_{$filename}_{$timestamp}.php";
    }

    /**
     * Ensure backup directory exists
     */
    public static function ensureBackupDirectoryExists(): bool
    {
        $backupDir = storage_path('app/translation-backups');

        if(!File::exists($backupDir)){
            return File::makeDirectory($backupDir, 0755, true);
        }

        return true;
    }

    /**
     * Get file size in human readable format
     */
    public static function formatFileSize(int $bytes): string
    {
        if($bytes === 0){
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    /**
     * Validate if path is safe (within allowed directories)
     */
    public static function isPathSafe(string $path): bool
    {
        $realPath = realpath($path);
        $languagePath = realpath(static::getLanguagePath());

        if(!$realPath || !$languagePath){
            return false;
        }

        // Check if the path is within the language directory
        return str_starts_with($realPath, $languagePath);
    }

    /**
     * Get disk usage for language files
     */
    public static function getLanguagesDiskUsage(): array
    {
        $usage = ['total_size' => 0, 'languages' => [],];

        $languageCodes = static::getExistingLanguageCodes();

        foreach($languageCodes as $languageCode) {
            $languageSize = 0;

            // Calculate PHP files size
            $phpFiles = static::getPhpFilesForLanguage($languageCode);
            foreach($phpFiles as $file) {
                $languageSize += $file['size'];
            }

            // Add JSON file size if exists
            if(static::jsonFileExists($languageCode)){
                $languageSize += File::size(static::getJsonFilePath($languageCode));
            }

            $usage['languages'][$languageCode] = ['size' => $languageSize, 'formatted_size' => static::formatFileSize($languageSize), 'php_files_count' => count($phpFiles), 'has_json' => static::jsonFileExists($languageCode),];

            $usage['total_size'] += $languageSize;
        }

        $usage['formatted_total_size'] = static::formatFileSize($usage['total_size']);

        return $usage;
    }

    /**
     * Clean up old backup files
     */
    public static function cleanupOldBackups(int $daysToKeep = 30): int
    {
        $backupDir = storage_path('app/translation-backups');

        if(!File::exists($backupDir)){
            return 0;
        }

        $files = File::files($backupDir);
        $cutoffTime = now()->subDays($daysToKeep)->timestamp;
        $deletedCount = 0;

        foreach($files as $file) {
            if($file->getMTime() < $cutoffTime){
                File::delete($file->getPathname());
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get information about the language path configuration
     */
    public static function getPathInfo(): array
    {
        $languagePath = static::getLanguagePath();

        return ['path' => $languagePath, 'relative_path' => static::getRelativePath($languagePath), 'exists' => static::languagePathExists(), 'is_writable' => File::exists($languagePath) ? File::isWritable($languagePath) : false, 'is_readable' => File::exists($languagePath) ? File::isReadable($languagePath) : false, 'absolute_path' => realpath($languagePath) ?: $languagePath,];
    }
}
