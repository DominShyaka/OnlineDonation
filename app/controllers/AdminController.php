<?php

class AdminController extends Controller
{
    public function dashboard(): void
    {
        requireRole('admin');

        $stats = [
            'users' => (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'donors' => (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'donor'")->fetchColumn(),
            'ngos' => (int) $this->db->query("SELECT COUNT(*) FROM ngos WHERE approval_status = 'approved'")->fetchColumn(),
            'pending' => (int) $this->db->query("SELECT COUNT(*) FROM ngos WHERE approval_status = 'pending'")->fetchColumn(),
            'campaigns' => (int) $this->db->query("SELECT COUNT(*) FROM campaigns")->fetchColumn(),
            'donations' => (int) $this->db->query("SELECT COUNT(*) FROM donations")->fetchColumn(),
            'total_raised' => (float) $this->db->query("SELECT COALESCE(SUM(amount), 0) FROM donations")->fetchColumn()
        ];

        $pendingStmt = $this->db->query(
            "SELECT n.*, u.username, u.email, u.created_at
             FROM ngos n
             INNER JOIN users u ON u.user_id = n.user_id
             WHERE n.approval_status = 'pending'
             ORDER BY u.created_at ASC"
        );
        $pendingNgos = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

        $usersStmt = $this->db->query(
            "SELECT user_id, username, email, role, account_status, created_at
             FROM users
             ORDER BY created_at DESC"
        );
        $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

        $campaignsStmt = $this->db->query(
            "SELECT c.*, n.name AS ngo_name
             FROM campaigns c
             INNER JOIN ngos n ON n.ngo_id = c.ngo_id
             ORDER BY c.created_at DESC
             LIMIT 10"
        );
        $campaigns = $campaignsStmt->fetchAll(PDO::FETCH_ASSOC);

        $donationsStmt = $this->db->query(
            "SELECT d.*, c.title AS campaign_title
             FROM donations d
             INNER JOIN campaigns c ON c.campaign_id = d.campaign_id
             ORDER BY d.donated_at DESC
             LIMIT 10"
        );
        $donations = $donationsStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/dashboard', compact(
            'stats',
            'pendingNgos',
            'users',
            'campaigns',
            'donations'
        ));
    }

    public function reviewNgo(): void
    {
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/dashboard');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid security token.');
            redirect('/admin/dashboard');
        }

        $ngoId = filter_input(INPUT_POST, 'ngo_id', FILTER_VALIDATE_INT);
        $decision = $_POST['decision'] ?? '';
        $reason = trim($_POST['rejection_reason'] ?? '');

        if (!$ngoId || !in_array($decision, ['approved', 'rejected'], true)) {
            setFlash('error', 'Invalid NGO review request.');
            redirect('/admin/dashboard');
        }

        if ($decision === 'rejected' && $reason === '') {
            setFlash('error', 'Please provide a reason when rejecting an NGO.');
            redirect('/admin/dashboard');
        }

        $stmt = $this->db->prepare(
            "UPDATE ngos
             SET approval_status = :status,
                 reviewed_at = NOW(),
                 rejection_reason = :reason
             WHERE ngo_id = :ngo_id
               AND approval_status = 'pending'"
        );

        $stmt->execute([
            'status' => $decision,
            'reason' => $decision === 'rejected' ? $reason : null,
            'ngo_id' => $ngoId
        ]);

        if ($stmt->rowCount() > 0) {
            setFlash('success', $decision === 'approved'
                ? 'NGO approved successfully. Its account can now log in.'
                : 'NGO application rejected.');
        } else {
            setFlash('error', 'The NGO application was not found or has already been reviewed.');
        }

        redirect('/admin/dashboard');
    }

    public function updateAccountStatus(): void
    {
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/dashboard');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid security token.');
            redirect('/admin/dashboard');
        }

        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $status = $_POST['account_status'] ?? '';

        if (!$userId || !in_array($status, ['active', 'suspended'], true)) {
            setFlash('error', 'Invalid account status request.');
            redirect('/admin/dashboard');
        }

        if ($userId === (int) $_SESSION['user_id']) {
            setFlash('error', 'You cannot suspend your own administrator account.');
            redirect('/admin/dashboard');
        }

        $stmt = $this->db->prepare(
            "UPDATE users
             SET account_status = :status
             WHERE user_id = :user_id
               AND role <> 'admin'"
        );

        $stmt->execute([
            'status' => $status,
            'user_id' => $userId
        ]);

        if ($stmt->rowCount() > 0) {
            setFlash('success', 'Account status updated successfully.');
        } else {
            setFlash('error', 'Account not found, unchanged, or protected.');
        }

        redirect('/admin/dashboard');
    }
}
