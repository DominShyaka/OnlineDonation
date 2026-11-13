<?php

class Donation
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "SELECT campaign_id, target_amount, raised_amount, end_date
                 FROM campaigns
                 WHERE campaign_id = :id
                 FOR UPDATE"
            );

            $stmt->execute(['id' => $data['campaign_id']]);
            $campaign = $stmt->fetch();

            if (!$campaign) {
                throw new RuntimeException('Campaign not found.');
            }

            if ($campaign['end_date'] < date('Y-m-d')) {
                throw new RuntimeException('This campaign has ended.');
            }

            $amount = (float) $data['amount'];

            if (!is_finite($amount) || $amount <= 0) {
                throw new RuntimeException('Invalid donation amount.');
            }

            $donationStmt = $this->db->prepare(
                "INSERT INTO donations
                 (campaign_id, donor_name, donor_email, amount)
                 VALUES
                 (:campaign_id, :donor_name, :donor_email, :amount)"
            );

            $donationStmt->execute([
                'campaign_id' => $data['campaign_id'],
                'donor_name' => $data['donor_name'],
                'donor_email' => $data['donor_email'],
                'amount' => $amount
            ]);

            $updateStmt = $this->db->prepare(
                "UPDATE campaigns
                 SET raised_amount = raised_amount + :amount
                 WHERE campaign_id = :id"
            );

            $updateStmt->execute([
                'amount' => $amount,
                'id' => $data['campaign_id']
            ]);

            $this->db->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function getByCampaign(int $campaignId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM donations
             WHERE campaign_id = :id
             ORDER BY donated_at DESC"
        );

        $stmt->execute(['id' => $campaignId]);

        return $stmt->fetchAll();
    }

    public function getByEmail(string $email): array
    {
        $stmt = $this->db->prepare(
            "SELECT donations.*, campaigns.title AS campaign_title
             FROM donations
             INNER JOIN campaigns
                 ON donations.campaign_id = campaigns.campaign_id
             WHERE donations.donor_email = :email
             ORDER BY donations.donated_at DESC"
        );

        $stmt->execute(['email' => $email]);

        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT donations.*, campaigns.title AS campaign_title
             FROM donations
             INNER JOIN campaigns
                 ON donations.campaign_id = campaigns.campaign_id
             ORDER BY donations.donated_at DESC"
        );

        return $stmt->fetchAll();
    }
}
