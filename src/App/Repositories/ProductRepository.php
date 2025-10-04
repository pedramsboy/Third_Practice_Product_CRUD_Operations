<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;
use App\Services\LoggerService;

class ProductRepository
{
    public function __construct(private Database $database,
                                private LoggerService $logger)
    {

    }
    public function getAll(): array
    {
//        $sql='SELECT * FROM product_test';
//        $pdo=$this->database->getConnection();
//        $stmt = $pdo->query($sql);
//        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        try {
            $sql = 'SELECT * FROM product_test';
            $pdo = $this->database->getConnection();
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->logger->info('Retrieved all products', [
                'count' => count($result)
            ], 'product');

            return $result;
        } catch (\PDOException $e) {
            $this->logger->productError('getAll', $e->getMessage());
            throw $e;
        }
    }

    public function getById(int $id): array|bool
    {
//        $sql = 'SELECT *FROM product_test
//                WHERE id = :id';
//
//        $pdo = $this->database->getConnection();
//
//        $stmt = $pdo->prepare($sql);
//
//        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
//
//        $stmt->execute();
//
//        return $stmt->fetch(PDO::FETCH_ASSOC);

        try {
            $sql = 'SELECT * FROM product_test WHERE id = :id';
            $pdo = $this->database->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this->logger->debug('Product retrieved by ID', ['product_id' => $id], 'product');
            } else {
                $this->logger->warning('Product not found', ['product_id' => $id], 'product');
            }

            return $result;
        } catch (\PDOException $e) {
            $this->logger->productError('getById', $e->getMessage(), ['product_id' => $id]);
            throw $e;
        }
    }

    public function create(array $data): string
    {
//        $sql = 'INSERT INTO product_test (name, description, size)
//                VALUES (:name, :description, :size)';

        try {
            $sql = 'INSERT INTO product_test (name, description, size, file_name, file_path, file_type, file_size)
                VALUES (:name, :description, :size, :file_name, :file_path, :file_type, :file_size)';

            $pdo = $this->database->getConnection();

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);

            if (empty($data['description'])) {

                $stmt->bindValue(':description', null, PDO::PARAM_NULL);

            } else {

                $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);

            }

            $stmt->bindValue(':size', $data['size'], PDO::PARAM_INT);

            // File data
            $stmt->bindValue(':file_name', $data['file_name'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':file_path', $data['file_path'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':file_type', $data['file_type'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':file_size', $data['file_size'] ?? null, PDO::PARAM_INT);


            $stmt->execute();

            //return $pdo->lastInsertId();

            $id = $pdo->lastInsertId();

            $this->logger->productCreated((int)$id, $data);

            return $id;

        }
        catch (\PDOException $e) {
            $this->logger->productError('create', $e->getMessage(), $data);
            throw $e;
        }
    }

    public function update(int $id, array $data): int
    {
//        $sql = 'UPDATE product_test
//                SET name = :name,
//                    description = :description,
//                    size = :size
//                WHERE id = :id';

        try {

            // Get old data for logging
            $oldData = $this->getById($id);

            $sql = 'UPDATE product_test
                SET name = :name,
                    description = :description,
                    size = :size,
                    file_name = :file_name,
                    file_path = :file_path,
                    file_type = :file_type,
                    file_size = :file_size
                WHERE id = :id';

            $pdo = $this->database->getConnection();

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);

            if (empty($data['description'])) {

                $stmt->bindValue(':description', null, PDO::PARAM_NULL);

            } else {

                $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);

            }

            $stmt->bindValue(':size', $data['size'], PDO::PARAM_INT);
            // File data
            $stmt->bindValue(':file_name', $data['file_name'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':file_path', $data['file_path'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':file_type', $data['file_type'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':file_size', $data['file_size'] ?? null, PDO::PARAM_INT);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            //return $stmt->rowCount();

            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $this->logger->productUpdated($id, $oldData ?: [], $data);
            }

            return $rows;
        }
        catch (\PDOException $e) {
            $this->logger->productError('update', $e->getMessage(), ['product_id' => $id, 'data' => $data]);
            throw $e;
        }
    }

    public function delete(string $id): int
    {

        try {
            $product = $this->getById((int)$id);

            if ($product && !empty($product['file_path'])) {
                $filePath = APP_ROOT . $product['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                    $this->logger->info('Product file deleted', [
                        'product_id' => $id,
                        'file_path' => $filePath
                    ], 'product');
                }
            }

            $sql = 'DELETE FROM product_test
                WHERE id = :id';

            $pdo = $this->database->getConnection();

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            //return $stmt->rowCount();
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $this->logger->productDeleted((int)$id, $product ?: []);
            }

            return $rows;
        }

        catch (\PDOException $e) {
            $this->logger->productError('delete', $e->getMessage(), ['product_id' => $id]);
            throw $e;
        }
    }

    public function updateFile(int $id, array $fileData): int
    {
        try {
            $sql = 'UPDATE product_test 
            SET file_name = :file_name,
                file_path = :file_path, 
                file_type = :file_type,
                file_size = :file_size
            WHERE id = :id';

            $pdo = $this->database->getConnection();
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':file_name', $fileData['file_name'], PDO::PARAM_STR);
            $stmt->bindValue(':file_path', $fileData['file_path'], PDO::PARAM_STR);
            $stmt->bindValue(':file_type', $fileData['file_type'], PDO::PARAM_STR);
            $stmt->bindValue(':file_size', $fileData['file_size'], PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            //return $stmt->rowCount();

            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $this->logger->info('Product file updated', [
                    'product_id' => $id,
                    'file_data' => $fileData
                ], 'product');
            }

            return $rows;
        }
        catch (\PDOException $e) {
            $this->logger->productError('updateFile', $e->getMessage(), [
                'product_id' => $id,
                'file_data' => $fileData
            ]);
            throw $e;
        }
    }
}