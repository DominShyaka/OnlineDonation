<?php
$success = getFlash('success');
$error = getFlash('error');

$stats = $stats ?? [];
$pendingNgos = $pendingNgos ?? [];
$users = $users ?? [];
$campaigns = $campaigns ?? [];
$recentDonations = $recentDonations ?? [];

function adminNumber($value): string {
    return number_format((float) ($value ?? 0));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | HopeGiving</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin.css">
</head>
<body class="admin-body">

<input type="checkbox" id="admin-menu-toggle" class="admin-menu-toggle">

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="admin-brand-mark">H</div>
            <div>
                <strong>HopeGiving</strong>
                <span>ADMINISTRATION</span>
            </div>
        </div>

        <div class="admin-profile">
            <div class="admin-profile-avatar">A</div>
            <div>
                <strong>Administrator</strong>
                <span>Platform manager</span>
            </div>
            <span class="admin-online-dot"></span>
        </div>

        <nav class="admin-navigation">
            <span class="admin-nav-label">WORKSPACE</span>

            <a class="admin-nav-link active" href="#overview">
                <span class="admin-nav-icon">▦</span>
                Overview
            </a>

            <span class="admin-nav-label">ORGANISATIONS</span>

            <a class="admin-nav-link" href="#approvals">
                <span class="admin-nav-icon">◇</span>
                NGO approvals
                <?php if (count($pendingNgos) > 0): ?>
                    <span class="admin-nav-count"><?= count($pendingNgos) ?></span>
                <?php endif; ?>
            </a>

            <a class="admin-nav-link" href="#campaigns">
                <span class="admin-nav-icon">◎</span>
                Campaigns
            </a>

            <span class="admin-nav-label">PLATFORM</span>

            <a class="admin-nav-link" href="#users">
                <span class="admin-nav-icon">♧</span>
                User management
            </a>

            <a class="admin-nav-link" href="#donations">
                <span class="admin-nav-icon">♡</span>
                Donations
            </a>
        </nav>

        <div class="admin-sidebar-bottom">
            <div class="admin-sidebar-note">
                <span>✦</span>
                <strong>Growing together</strong>
                <p>Every approved organisation creates another opportunity to make a difference.</p>
            </div>

            <a href="<?= BASE_URL ?>/" class="admin-nav-link">
                <span class="admin-nav-icon">↗</span>
                Visit website
            </a>

            <form method="POST" action="<?= BASE_URL ?>/logout">
                <?= csrfField() ?>
                <button type="submit" class="admin-logout">
                    <span class="admin-nav-icon">⇥</span>
                    Sign out
                </button>
            </form>

            <div class="admin-sidebar-footer">HOPEGIVING © <?= date('Y') ?></div>
        </div>
    </aside>

    <label for="admin-menu-toggle" class="admin-mobile-overlay"></label>

    <main class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <label for="admin-menu-toggle" class="admin-mobile-menu">☰</label>
                <div>
                    <span class="admin-breadcrumb">Workspace / Overview</span>
                    <h1>Dashboard</h1>
                </div>
            </div>

            <div class="admin-topbar-right">
                <span class="admin-live-indicator"></span>
                <span>Platform overview</span>
                <div class="admin-topbar-avatar">A</div>
            </div>
        </header>

        <div class="admin-content">

            <?php if ($success): ?>
                <div class="admin-alert admin-alert-success">
                    <span>✓</span><?= e($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="admin-alert admin-alert-error">
                    <span>!</span><?= e($error) ?>
                </div>
            <?php endif; ?>

            <section class="admin-hero" id="overview">
                <div>
                    <span class="admin-eyebrow">YOUR IMPACT STARTS HERE</span>
                    <h2>Welcome to HopeGiving.</h2>
                    <p>Manage your organisations, oversee campaigns and help generosity reach the people who need it most.</p>
                    <a href="#approvals" class="admin-hero-button">Review organisations <span>→</span></a>
                </div>
                <div class="admin-hero-art" aria-hidden="true">
                    <div class="admin-art-circle admin-art-circle-one"></div>
                    <div class="admin-art-circle admin-art-circle-two"></div>
                    <div class="admin-art-heart">♡</div>
                    <span class="admin-art-leaf">✦</span>
                </div>
            </section>

            <section class="admin-section">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">AT A GLANCE</span>
                        <h2>Platform overview</h2>
                    </div>
                    <span class="admin-updated">Live database figures</span>
                </div>

                <div class="admin-stat-grid">
                    <article class="admin-stat-card">
                        <div class="admin-stat-top">
                            <span>Total users</span>
                            <span class="admin-stat-icon green">♧</span>
                        </div>
                        <strong><?= adminNumber($stats['users'] ?? 0) ?></strong>
                        <small>Registered accounts</small>
                    </article>

                    <article class="admin-stat-card">
                        <div class="admin-stat-top">
                            <span>Approved NGOs</span>
                            <span class="admin-stat-icon mint">◇</span>
                        </div>
                        <strong><?= adminNumber($stats['approved_ngos'] ?? 0) ?></strong>
                        <small>Verified organisations</small>
                    </article>

                    <article class="admin-stat-card">
                        <div class="admin-stat-top">
                            <span>Pending approvals</span>
                            <span class="admin-stat-icon amber">◷</span>
                        </div>
                        <strong><?= adminNumber($stats['pending_ngos'] ?? count($pendingNgos)) ?></strong>
                        <small>Awaiting your review</small>
                    </article>

                    <article class="admin-stat-card">
                        <div class="admin-stat-top">
                            <span>Campaigns</span>
                            <span class="admin-stat-icon blue">◎</span>
                        </div>
                        <strong><?= adminNumber($stats['campaigns'] ?? 0) ?></strong>
                        <small>Created on the platform</small>
                    </article>

                    <article class="admin-stat-card admin-stat-featured">
                        <div class="admin-stat-top">
                            <span>Total donations</span>
                            <span class="admin-stat-icon white">♡</span>
                        </div>
                        <strong><?= number_format((float) ($stats['total_donations'] ?? 0), 2) ?></strong>
                        <small><?= adminNumber($stats['donations'] ?? 0) ?> recorded contributions</small>
                    </article>
                </div>
            </section>

            <section class="admin-section" id="approvals">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">ORGANISATION MANAGEMENT</span>
                        <h2>NGO approval requests</h2>
                        <p>Review organisations before they can publish campaigns.</p>
                    </div>
                    <span class="admin-section-count"><?= count($pendingNgos) ?> awaiting review</span>
                </div>

                <?php if (empty($pendingNgos)): ?>
                    <div class="admin-empty">
                        <span>✓</span>
                        <strong>You're all caught up</strong>
                        <p>There are no NGO applications waiting for approval.</p>
                    </div>
                <?php else: ?>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Organisation</th>
                                    <th>Registration number</th>
                                    <th>Contact</th>
                                    <th>Decision</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingNgos as $ngo): ?>
                                    <tr>
                                        <td>
                                            <div class="admin-table-person">
                                                <span class="admin-table-avatar">
                                                    <?= e(strtoupper(substr($ngo['name'] ?? 'N', 0, 1))) ?>
                                                </span>
                                                <div>
                                                    <strong><?= e($ngo['name'] ?? '') ?></strong>
                                                    <small><?= e($ngo['username'] ?? '') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= e($ngo['registration_no'] ?? '—') ?></td>
                                        <td>
                                            <strong><?= e($ngo['contact_email'] ?? '') ?></strong>
                                            <small class="admin-secondary-text"><?= e($ngo['phone'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <div class="admin-decision-actions">
                                                <form method="POST" action="<?= BASE_URL ?>/admin/ngo-review">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="ngo_id" value="<?= (int) $ngo['ngo_id'] ?>">
                                                    <input type="hidden" name="decision" value="approved">
                                                    <button class="admin-button admin-button-approve" type="submit">Approve</button>
                                                </form>

                                                <form method="POST" action="<?= BASE_URL ?>/admin/ngo-review">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="ngo_id" value="<?= (int) $ngo['ngo_id'] ?>">
                                                    <input type="hidden" name="decision" value="rejected">
                                                    <input type="text" name="reason" placeholder="Rejection reason" required aria-label="Rejection reason">
                                                    <button class="admin-button admin-button-reject" type="submit">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="admin-section" id="users">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">ACCOUNT CONTROL</span>
                        <h2>User management</h2>
                        <p>Monitor registered users and manage account access.</p>
                    </div>
                </div>

                <?php if (empty($users)): ?>
                    <div class="admin-empty">
                        <p>No user records are available.</p>
                    </div>
                <?php else: ?>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Account status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="admin-table-person">
                                                <span class="admin-table-avatar">
                                                    <?= e(strtoupper(substr($user['username'] ?? 'U', 0, 1))) ?>
                                                </span>
                                                <strong><?= e($user['username'] ?? '') ?></strong>
                                            </div>
                                        </td>
                                        <td><?= e($user['email'] ?? '') ?></td>
                                        <td><span class="admin-role"><?= e(ucfirst($user['role'] ?? '')) ?></span></td>
                                        <td>
                                            <span class="admin-status <?= ($user['account_status'] ?? '') === 'active' ? 'is-active' : 'is-suspended' ?>">
                                                <?= e(ucfirst($user['account_status'] ?? 'unknown')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($user['role'] ?? '') !== 'admin'): ?>
                                                <form method="POST" action="<?= BASE_URL ?>/admin/account-status">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                                    <input type="hidden" name="status" value="<?= ($user['account_status'] ?? '') === 'active' ? 'suspended' : 'active' ?>">
                                                    <button type="submit" class="admin-button <?= ($user['account_status'] ?? '') === 'active' ? 'admin-button-reject' : 'admin-button-approve' ?>">
                                                        <?= ($user['account_status'] ?? '') === 'active' ? 'Suspend' : 'Reactivate' ?>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="admin-protected">Protected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="admin-section" id="campaigns">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">FUNDRAISING ACTIVITY</span>
                        <h2>Recent campaigns</h2>
                        <p>See what organisations are raising funds for.</p>
                    </div>
                </div>

                <?php if (empty($campaigns)): ?>
                    <div class="admin-empty"><p>No campaigns have been created yet.</p></div>
                <?php else: ?>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Organisation</th>
                                    <th>Raised</th>
                                    <th>Target</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($campaigns as $campaign): ?>
                                    <?php
                                        $target = (float) ($campaign['target_amount'] ?? 0);
                                        $raised = (float) ($campaign['raised_amount'] ?? 0);
                                        $percentage = $target > 0 ? min(100, ($raised / $target) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?= e($campaign['title'] ?? '') ?></strong></td>
                                        <td><?= e($campaign['ngo_name'] ?? '') ?></td>
                                        <td><?= number_format($raised, 2) ?></td>
                                        <td><?= number_format($target, 2) ?></td>
                                        <td>
                                            <div class="admin-progress">
                                                <div style="width: <?= e((string) $percentage) ?>%"></div>
                                            </div>
                                            <small class="admin-secondary-text"><?= number_format($percentage, 1) ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="admin-section" id="donations">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">COMMUNITY GENEROSITY</span>
                        <h2>Recent donations</h2>
                        <p>Recent contributions made through HopeGiving.</p>
                    </div>
                </div>

                <?php if (empty($recentDonations)): ?>
                    <div class="admin-empty"><p>No donations have been recorded yet.</p></div>
                <?php else: ?>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Donor</th>
                                    <th>Campaign</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentDonations as $donation): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($donation['donor_name'] ?? '') ?></strong>
                                            <small class="admin-secondary-text"><?= e($donation['donor_email'] ?? '') ?></small>
                                        </td>
                                        <td><?= e($donation['campaign_title'] ?? '') ?></td>
                                        <td class="admin-money"><?= number_format((float) ($donation['amount'] ?? 0), 2) ?></td>
                                        <td><?= e(isset($donation['donated_at']) ? date('M j, Y', strtotime($donation['donated_at'])) : '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <footer class="admin-page-footer">
                <span>HopeGiving Administration</span>
                <span>Built on generosity. Powered by community.</span>
            </footer>

        </div>
    </main>
</div>
</body>
</html>
