<?php
declare(strict_types=1);

namespace App\Services;



class LoggerService
{
    private string $logDirectory;
    private string $dateFormat = 'Y-m-d H:i:s';

    public function __construct()
    {
        // Set log directory - you might want to configure this path
        $this->logDirectory = APP_ROOT . '/logs/';

        // Create logs directory if it doesn't exist
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }
    }

    public function log(
        string $level,
        string $message,
        array $context = [],
        string $channel = 'app'
    ): void {
        try {
            $timestamp = date($this->dateFormat);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $route = $_SERVER['REQUEST_URI'] ?? 'unknown';
            $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';

            // Format the log entry
            $logEntry = sprintf(
                "[%s] %s.%s: %s %s %s %s %s %s" . PHP_EOL,
                $timestamp,
                $channel,
                strtoupper($level),
                $message,
                json_encode($context),
                $ipAddress,
                $userAgent,
                $route,
                $method
            );

            // Write to channel-specific log file
            $logFile = $this->logDirectory . $channel . '.log';
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

            // Also write to main application log
            if ($channel !== 'app') {
                $mainLogFile = $this->logDirectory . 'app.log';
                file_put_contents($mainLogFile, $logEntry, FILE_APPEND | LOCK_EX);
            }

        } catch (\Exception $e) {
            // Fallback to error_log if file writing fails
            error_log("Logger Error: " . $e->getMessage());
            error_log("Original Log - Level: $level, Message: $message, Channel: $channel");
        }
    }

    // Convenience methods for different log levels
    public function error(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->log('error', $message, $context, $channel);
    }

    public function warning(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->log('warning', $message, $context, $channel);
    }

    public function info(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->log('info', $message, $context, $channel);
    }

    public function success(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->log('success', $message, $context, $channel);
    }

    public function debug(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->log('debug', $message, $context, $channel);
    }

    // Product-specific logging methods
    public function productCreated(int $productId, array $productData): void
    {
        $this->success('Product created successfully', [
            'product_id' => $productId,
            'product_data' => $productData
        ], 'product');
    }

    public function productUpdated(int $productId, array $oldData, array $newData): void
    {
        $this->info('Product updated', [
            'product_id' => $productId,
            'old_data' => $oldData,
            'new_data' => $newData
        ], 'product');
    }

    public function productDeleted(int $productId, array $productData): void
    {
        $this->warning('Product deleted', [
            'product_id' => $productId,
            'product_data' => $productData
        ], 'product');
    }

    public function productError(string $operation, string $error, array $context = []): void
    {
        $this->error("Product operation failed: $operation", [
            'error' => $error,
            'operation' => $operation,
            'additional_context' => $context
        ], 'product');
    }

    /**
     * Rotate log files to prevent them from growing too large
     */
    public function rotateLogs(int $maxSize = 10485760): void // 10MB default
    {
        try {
            $logFiles = glob($this->logDirectory . '*.log');

            foreach ($logFiles as $file) {
                if (filesize($file) > $maxSize) {
                    $backupFile = $file . '.' . date('Y-m-d_His');
                    rename($file, $backupFile);

                    // Create new empty log file
                    touch($file);
                    chmod($file, 0644);

                    $this->info('Log file rotated', [
                        'original_file' => basename($file),
                        'backup_file' => basename($backupFile),
                        'size' => filesize($backupFile)
                    ]);
                }
            }
        } catch (\Exception $e) {
            error_log("Log rotation failed: " . $e->getMessage());
        }
    }

    /**
     * Get recent logs (useful for debugging or admin panels)
     */
    public function getRecentLogs(string $channel = 'app', int $lines = 100): array
    {
        $logFile = $this->logDirectory . $channel . '.log';

        if (!file_exists($logFile)) {
            return [];
        }

        $content = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($content === false) {
            return [];
        }

        return array_slice($content, -$lines);
    }
}
