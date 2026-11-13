<?php

class NGOController extends Controller
{
    private function getNGO(): ?array
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return null;
        }

        $query = $this->db->prepare(
            "SELECT
                n.ngo_id,
                n.name,
                n.registration_no,
                n.contact_email,
                n.phone,
                n.approval_status,
                u.username,
                u.email,
                u.account_status
             FROM ngos n
             INNER JOIN users u ON u.user_id = n.user_id
             WHERE n.user_id = ?
             LIMIT 1"
        );

        $query->execute([$userId]);

        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function requireActiveNGO(): array
    {
        requireRole('ngo');

        $ngo = $this->getNGO();

        if (!$ngo || $ngo['account_status'] !== 'active') {
            http_response_code(403);
            exit('Your NGO account is unavailable.');
        }

        if ($ngo['approval_status'] !== 'approved') {
            http_response_code(403);
            exit('Your NGO account has not been approved.');
        }

        return $ngo;
    }

    public function dashboard(): void
    {
        $ngo = $this->requireActiveNGO();

        $ngoId = (int)$ngo['ngo_id'];

        // Fundraising statistics.
        $statsQuery = $this->db->prepare(
            "SELECT
                COUNT(*) AS campaign_count,
                COALESCE(SUM(raised_amount), 0) AS total_raised,
                COALESCE(SUM(target_amount), 0) AS total_target,
                SUM(CASE WHEN end_date >= CURDATE()
                    AND raised_amount < target_amount THEN 1 ELSE 0 END) AS active_campaigns
             FROM campaigns
             WHERE ngo_id = ?"
        );

        $statsQuery->execute([$ngoId]);
        $stats = $statsQuery->fetch(PDO::FETCH_ASSOC) ?: [];

        // Total donations received.
        $donationStatsQuery = $this->db->prepare(
            "SELECT
                COUNT(d.donation_id) AS donation_count,
                COUNT(DISTINCT d.donor_email) AS donor_count
             FROM donations d
             INNER JOIN campaigns c ON c.campaign_id = d.campaign_id
             WHERE c.ngo_id = ?"
        );

        $donationStatsQuery->execute([$ngoId]);
        $donationStats = $donationStatsQuery->fetch(PDO::FETCH_ASSOC) ?: [];

        $stats['donation_count'] = $donationStats['donation_count'] ?? 0;
        $stats['donor_count'] = $donationStats['donor_count'] ?? 0;

        // NGO campaigns.
        $campaignQuery = $this->db->prepare(
            "SELECT
                c.campaign_id,
                c.title,
                c.description,
                c.target_amount,
                c.raised_amount,
                c.end_date,
                c.created_at
             FROM campaigns c
             WHERE c.ngo_id = ?
             ORDER BY c.created_at DESC"
        );

        $campaignQuery->execute([$ngoId]);
        $campaigns = $campaignQuery->fetchAll(PDO::FETCH_ASSOC);

        // Recent donations received by this NGO.
        $donationQuery = $this->db->prepare(
            "SELECT
                d.donation_id,
                d.donor_name,
                d.donor_email,
                d.amount,
                d.donated_at,
                c.title AS campaign_title
             FROM donations d
             INNER JOIN campaigns c ON c.campaign_id = d.campaign_id
             WHERE c.ngo_id = ?
             ORDER BY d.donated_at DESC
             LIMIT 10"
        );

        $donationQuery->execute([$ngoId]);
        $donations = $donationQuery->fetchAll(PDO::FETCH_ASSOC);

        $this->view('ngo/dashboard', [
            'ngo' => $ngo,
            'stats' => $stats,
            'campaigns' => $campaigns,
            'donations' => $donations
        ]);
    }

    public function campaigns(): void
    {
        $ngo = $this->requireActiveNGO();

        $query = $this->db->prepare(
            "SELECT *
             FROM campaigns
             WHERE ngo_id = ?
             ORDER BY created_at DESC"
        );

        $query->execute([(int)$ngo['ngo_id']]);
        $campaigns = $query->fetchAll(PDO::FETCH_ASSOC);

        $this->view('ngo/campaigns', [
            'ngo' => $ngo,
            'campaigns' => $campaigns
        ]);
    }

    public function createCampaign(): void
    {
        $ngo = $this->requireActiveNGO();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!verifyCsrfToken($token)) {
                setFlash('error', 'Your session token is invalid. Please try again.');
                redirect('/ngo/create-campaign');
                exit;
            }

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $targetAmount = filter_var(
                $_POST['target_amount'] ?? '',
                FILTER_VALIDATE_FLOAT
            );
            $endDate = trim($_POST['end_date'] ?? '');

            $errors = [];

            if (strlen($title) < 5 || strlen($title) > 150) {
                $errors[] = 'The campaign title must be between 5 and 150 characters.';
            }

            if (strlen($description) < 20) {
                $errors[] = 'The campaign description must contain at least 20 characters.';
            }

            if ($targetAmount === false || $targetAmount <= 0) {
                $errors[] = 'Enter a valid target amount greater than zero.';
            }

            $date = DateTime::createFromFormat('Y-m-d', $endDate);
            if (!$date || $date->format('Y-m-d') !== $endDate || $endDate < date('Y-m-d')) {
                $errors[] = 'Choose a valid campaign end date that is today or later.';
            }

            if ($errors) {
                setFlash('error', implode(' ', $errors));
                redirect('/ngo/create-campaign');
                exit;
            }

            try {
                $insert = $this->db->prepare(
                    "INSERT INTO campaigns
                        (ngo_id, title, description, target_amount, raised_amount, end_date, created_at)
                     VALUES (?, ?, ?, ?, 0, ?, NOW())"
                );

                $insert->execute([
                    (int)$ngo['ngo_id'],
                    $title,
                    $description,
                    $targetAmount,
                    $endDate
                ]);

                setFlash('success', 'Your campaign has been created successfully.');
                redirect('/ngo/dashboard');
                exit;
            } catch (PDOException $exception) {
                error_log('Campaign creation error: ' . $exception->getMessage());
                setFlash('error', 'The campaign could not be created. Please try again.');
                redirect('/ngo/create-campaign');
                exit;
            }
        }

        $this->view('ngo/create-campaign', [
            'ngo' => $ngo
        ]);
    }
}
