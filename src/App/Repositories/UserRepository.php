<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database;

class UserRepository
{
    public function __construct(private Database $database)
    {
    }



// Update the create method to handle Google users
    public function create(array $data): void
    {
        $sql = 'INSERT INTO user (name, email, password_hash, api_key, api_key_hash, google_id, avatar)
            VALUES (:name, :email, :password_hash, :api_key, :api_key_hash, :google_id, :avatar)';

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':password_hash', $data['password_hash'] ?? '');
        $stmt->bindValue(':api_key', $data['api_key']);
        $stmt->bindValue(':api_key_hash', $data['api_key_hash']);
        $stmt->bindValue(':google_id', $data['google_id'] ?? null);
        $stmt->bindValue(':avatar', $data['avatar'] ?? null);

        $stmt->execute();
    }

    public function find(string $column, $value): array|bool
    {
        $sql = "SELECT *
                FROM user
                WHERE $column = :value";

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':value', $value);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function updateGoogleId(int $userId, string $googleId): void
    {
        $sql = 'UPDATE user SET google_id = :google_id WHERE id = :id';

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':google_id', $googleId);
        $stmt->bindValue(':id', $userId,PDO::PARAM_INT);
        $stmt->execute();
    }
}