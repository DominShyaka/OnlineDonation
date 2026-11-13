<?php

class NGO
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM ngos WHERE user_id = :user_id LIMIT 1"
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch() ?: null;
    }

    public function findById(int $ngoId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM ngos WHERE ngo_id = :ngo_id LIMIT 1"
        );

        $stmt->execute(['ngo_id' => $ngoId]);

        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT ngos.*, users.username, users.email
             FROM ngos
             INNER JOIN users ON ngos.user_id = users.user_id
             ORDER BY ngos.ngo_id DESC"
        );

        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ngos
             (user_id, name, registration_no, contact_email, phone)
             VALUES
             (:user_id, :name, :registration_no, :contact_email, :phone)"
        );

        return $stmt->execute([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'registration_no' => $data['registration_no'],
            'contact_email' => $data['contact_email'],
            'phone' => $data['phone'] ?? null
        ]);
    }
}
