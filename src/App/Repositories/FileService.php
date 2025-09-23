<?php

declare(strict_types=1);

namespace App\Repositories;

class FileService
{
    private const UPLOAD_DIR = '/uploads/products/';
    private const MAX_FILE_SIZE = 1000000; // 1MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

    public function handleUpload(array $uploadedFile): array
    {
        $error = $uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('File upload error: ' . $error);
        }

        // Validate file size
        if ($uploadedFile['size'] > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('File size exceeds maximum allowed size');
        }

        // Validate file type
        if (!in_array($uploadedFile['type'], self::ALLOWED_TYPES)) {
            throw new \RuntimeException('File type not allowed');
        }

        // Create upload directory if it doesn't exist
        $uploadPath = APP_ROOT . self::UPLOAD_DIR;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $uploadPath . $filename;


        // Move uploaded file
        if (!move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
            throw new \RuntimeException('Failed to move uploaded file');
        }

        return [
            'file_name' => $uploadedFile['name'],
            'file_path' => self::UPLOAD_DIR . $filename,
            'file_type' => $uploadedFile['type'],
            'file_size' => $uploadedFile['size']
        ];
    }

    public function deleteFile(string $filePath): bool
    {
        $fullPath = APP_ROOT . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function getFile(string $filePath): ?string
    {
        $fullPath = APP_ROOT . $filePath;
        return file_exists($fullPath) ? $fullPath : null;
    }
}