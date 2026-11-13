<?php

class DonorController extends Controller
{
    public function dashboard(): void
    {
        requireRole('donor');

        $donorEmail = $_SESSION['email'] ?? '';
        $username = $_SESSION['username'] ?? 'Donor';

        if ($donorEmail === '') {
            redirect('/login');
            exit;
        }

        // Process a new donation.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processDonation($donorEmail, $username);
            return;
        }

        // Donor statistics.
        $statsQuery = $this->db->prepare(
            "SELECT
                COALESCE(SUM(amount), 0) AS total_donated,
                COUNT(*) AS donation_count,
                COUNT(DISTINCT campaign_id) AS campaigns_supported
             FROM donations
             WHERE donor_email = ?"
        );
        $statsQuery->execute([$donorEmail]);
        $stats = $statsQuery->fetch(PDO::FETCH_ASSOC) ?: [
            'total_donated' => 0,
            'donation_count' => 0,
            'campaigns_supported' => 0
        ];

        // Campaigns available for donations.
        $campaignQuery = $this->db->prepare(
            "SELECT
                c.campaign_id,
                c.title,
                c.description,
                c.target_amount,
                c.raised_amount,
                c.end_date,
                n.name AS ngo_name
             FROM campaigns c
             INNER JOIN ngos n ON n.ngo_id = c.ngo_id
             INNER JOIN users u ON u.user_id = n.user_id
             WHERE n.approval_status = 'approved'
               AND u.account_status = 'active'
               AND c.end_date >= CURDATE()
               AND c.raised_amount < c.target_amount
             ORDER BY c.created_at DESC"
        );
        $campaignQuery->execute();
        $campaigns = $campaignQuery->fetchAll(PDO::FETCH_ASSOC);

        // This donor's donation history.
        $donationQuery = $this->db->prepare(
            "SELECT
                d.donation_id,
                d.amount,
                d.donated_at,
                c.title AS campaign_title,
                n.name AS ngo_name
             FROM donations d
             INNER JOIN campaigns c ON c.campaign_id = d.campaign_id
             INNER JOIN ngos n ON n.ngo_id = c.ngo_id
             WHERE d.donor_email = ?
             ORDER BY d.donated_at DESC"
        );
        $donationQuery->execute([$donorEmail]);
        $donations = $donationQuery->fetchAll(PDO::FETCH_ASSOC);

        $this->view('donor/dashboard', [
            'username' => $username,
            'stats' => $stats,
            'campaigns' => $campaigns,
            'donations' => $donations
        ]);
    }

    private function processDonation(string $donorEmail, string $username): void
    {
        $token = $_POST['csrf_token'] ?? '';

        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Your session token is invalid. Please try again.');
            redirect('/donor/dashboard');
            exit;
        }

        $campaignId = filter_input(INPUT_POST, 'campaign_id', FILTER_VALIDATE_INT);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

        if (!$campaignId || !$amount || $amount <= 0) {
            setFlash('error', 'Please select a campaign and enter a valid donation amount.');
            redirect('/donor/dashboard');
            exit;
        }

        try {
            $this->db->beginTransaction();

            // Lock the campaign row while checking and recording the donation.
            $campaignQuery = $this->db->prepare(
                "SELECT
                    c.campaign_id,
                    c.target_amount,
                    c.raised_amount,
                    c.end_date,
                    n.approval_status,
                    u.account_status
                 FROM campaigns c
                 INNER JOIN ngos n ON n.ngo_id = c.ngo_id
                 INNER JOIN users u ON u.user_id = n.user_id
                 WHERE c.campaign_id = ?
                 FOR UPDATE"
            );
            $campaignQuery->execute([$campaignId]);
            $campaign = $campaignQuery->fetch(PDO::FETCH_ASSOC);

            if (
                !$campaign ||
                $campaign['approval_status'] !== 'approved' ||
                $campaign['account_status'] !== 'active' ||
                $campaign['end_date'] < date('Y-m-d') ||
                (float)$campaign['raised_amount'] >= (float)$campaign['target_amount']
            ) {
                $this->db->rollBack();
                setFlash('error', 'This campaign is no longer accepting donations.');
                redirect('/donor/dashboard');
                exit;
            }

            $remaining = (float)$campaign['target_amount'] - (float)$campaign['raised_amount'];

            if ($amount > $remaining) {
                $this->db->rollBack();
                setFlash('error', 'Your donation cannot exceed the campaign amount still needed.');
                redirect('/donor/dashboard');
                exit;
            }

            $insert = $this->db->prepare(
                "INSERT INTO donations
                    (campaign_id, donor_name, donor_email, amount, donated_at)
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $insert->execute([
                $campaignId,
                $username,
                $donorEmail,
                $amount
            ]);

            $update = $this->db->prepare(
                "UPDATE campaigns
                 SET raised_amount = raised_amount + ?
                 WHERE campaign_id = ?"
            );
            $update->execute([$amount, $campaignId]);

            $this->db->commit();

            setFlash('success', 'Thank you! Your donation has been recorded successfully.');
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log('Donation processing error: ' . $exception->getMessage());
            setFlash('error', 'We could not process your donation. Please try again.');
        }

        redirect('/donor/dashboard');
        exit;
    }
}
