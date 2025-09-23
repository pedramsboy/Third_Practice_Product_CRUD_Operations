<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    public function __construct(private Database $database)
    {

    }
    public function getAll(): array
    {
        $sql='SELECT * FROM product_test';
        $pdo=$this->database->getConnection();
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *FROM product_test
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
//        $sql = 'INSERT INTO product_test (name, description, size)
//                VALUES (:name, :description, :size)';

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

        return $pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
//        $sql = 'UPDATE product_test
//                SET name = :name,
//                    description = :description,
//                    size = :size
//                WHERE id = :id';

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

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $product = $this->getById((int)$id);

        if ($product && !empty($product['file_path'])) {
            $filePath = APP_ROOT . $product['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $sql = 'DELETE FROM product_test
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function updateFile(int $id, array $fileData): int
    {
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

        return $stmt->rowCount();
    }
}