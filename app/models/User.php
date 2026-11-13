<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );

        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public function findById(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE user_id = :id LIMIT 1"
        );

        $stmt->execute(['id' => $userId]);

        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT user_id, username, email, role, created_at
             FROM users ORDER BY created_at DESC"
        );

        return $stmt->fetchAll();
    }

    public function create(
        string $username,
        string $email,
        string $password,
        string $role = 'donor'
    ): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash, role)
             VALUES (:username, :email, :password_hash, :role)"
        );

        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $hash,
            'role' => $role
        ]);
    }
}
