<?php

class Campaign
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT campaigns.*, ngos.name AS ngo_name
             FROM campaigns
             INNER JOIN ngos ON campaigns.ngo_id = ngos.ngo_id
             ORDER BY campaigns.created_at DESC"
        );

        return $stmt->fetchAll();
    }

    public function findById(int $campaignId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT campaigns.*, ngos.name AS ngo_name
             FROM campaigns
             INNER JOIN ngos ON campaigns.ngo_id = ngos.ngo_id
             WHERE campaigns.campaign_id = :id
             LIMIT 1"
        );

        $stmt->execute(['id' => $campaignId]);

        return $stmt->fetch() ?: null;
    }

    public function getByNGO(int $ngoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM campaigns
             WHERE ngo_id = :ngo_id
             ORDER BY created_at DESC"
        );

        $stmt->execute(['ngo_id' => $ngoId]);

        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO campaigns
             (ngo_id, title, description, target_amount, end_date)
             VALUES
             (:ngo_id, :title, :description, :target_amount, :end_date)"
        );

        return $stmt->execute([
            'ngo_id' => $data['ngo_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'target_amount' => $data['target_amount'],
            'end_date' => $data['end_date']
        ]);
    }

    public function update(int $campaignId, int $ngoId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE campaigns
             SET title = :title,
                 description = :description,
                 target_amount = :target_amount,
                 end_date = :end_date
             WHERE campaign_id = :id AND ngo_id = :ngo_id"
        );

        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'target_amount' => $data['target_amount'],
            'end_date' => $data['end_date'],
            'id' => $campaignId,
            'ngo_id' => $ngoId
        ]);
    }

    public function delete(int $campaignId, int $ngoId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM campaigns
             WHERE campaign_id = :id AND ngo_id = :ngo_id"
        );

        return $stmt->execute([
            'id' => $campaignId,
            'ngo_id' => $ngoId
        ]);
    }
}
