<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class LoggerService
{
    public function __construct(private Database $database)
    {
    }

    public function log(
        string $level,
        string $message,
        array $context = [],
        string $channel = 'app'
    ): void {
        try {
            $sql = 'INSERT INTO logs (level, message, context, channel, ip_address, user_agent, route, method, timestamp) 
                    VALUES (:level, :message, :context, :channel, :ip_address, :user_agent, :route, :method, NOW())';

            $pdo = $this->database->getConnection();
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':level', $level, PDO::PARAM_STR);
            $stmt->bindValue(':message', $message, PDO::PARAM_STR);
            $stmt->bindValue(':context', json_encode($context), PDO::PARAM_STR);
            $stmt->bindValue(':channel', $channel, PDO::PARAM_STR);
            $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':route', $_SERVER['REQUEST_URI'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':method', $_SERVER['REQUEST_METHOD'] ?? null, PDO::PARAM_STR);

            $stmt->execute();
        } catch (\PDOException $e) {
            // Fallback to file logging if database logging fails
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
}