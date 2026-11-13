<?php
$ngo = $ngo ?? [];
$stats = $stats ?? [];
$campaigns = $campaigns ?? [];
$donations = $donations ?? [];

$ngoName = $ngo['name'] ?? 'Your Organisation';
$initial = strtoupper(substr($ngoName, 0, 1));

$totalRaised = (float)($stats['total_raised'] ?? 0);
$totalTarget = (float)($stats['total_target'] ?? 0);
$campaignCount = (int)($stats['campaign_count'] ?? 0);
$activeCampaigns = (int)($stats['active_campaigns'] ?? 0);
$donationCount = (int)($stats['donation_count'] ?? 0);
$donorCount = (int)($stats['donor_count'] ?? 0);
$overallProgress = $totalTarget > 0
    ? min(100, ($totalRaised / $totalTarget) * 100)
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Dashboard | HopeGiving</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/ngo.css">
</head>
<body class="ngo-body">

<div class="ngo-layout">

    <aside class="ngo-sidebar">
        <a href="<?= BASE_URL ?>/ngo/dashboard" class="ngo-brand">
            <span class="brand-mark">H</span>
            <span>Hope<span>Giving</span></span>
        </a>

        <div class="sidebar-caption">ORGANISATION WORKSPACE</div>

        <nav class="ngo-navigation">
            <a href="<?= BASE_URL ?>/ngo/dashboard" class="ngo-nav-link active">
                <span>▦</span> Overview
            </a>
            <a href="<?= BASE_URL ?>/ngo/campaigns" class="ngo-nav-link">
                <span>◈</span> My campaigns
            </a>
            <a href="#donations" class="ngo-nav-link">
                <span>↗</span> Donations received
            </a>
            <a href="<?= BASE_URL ?>/ngo/create-campaign" class="ngo-nav-link">
                <span>＋</span> Create campaign
            </a>
        </nav>

        <div class="ngo-sidebar-bottom">
            <div class="ngo-help-card">
                <div class="help-icon">♡</div>
                <strong>Your mission matters</strong>
                <p>Every campaign brings your organisation closer to its goals.</p>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/logout">
                <?= csrfField() ?>
                <button type="submit" class="ngo-logout">
                    <span>↪</span> Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="ngo-main">

        <header class="ngo-topbar">
            <div>
                <span class="topbar-label">ORGANISATION PORTAL</span>
                <h2>Organisation overview</h2>
            </div>

            <div class="ngo-profile">
                <div class="ngo-avatar"><?= e($initial) ?></div>
                <div>
                    <strong><?= e($ngoName) ?></strong>
                    <span>Approved NGO</span>
                </div>
            </div>
        </header>

        <section class="ngo-welcome">
            <div class="welcome-content">
                <span class="welcome-label">YOUR IMPACT STARTS HERE</span>
                <h1>Welcome, <?= e($ngoName) ?>.</h1>
                <p>Manage your campaigns, follow your fundraising progress and see the people supporting your mission.</p>
                <a href="<?= BASE_URL ?>/ngo/create-campaign" class="create-campaign-button">
                    <span>＋</span> Create a campaign
                </a>
            </div>
            <div class="welcome-decoration" aria-hidden="true">
                <div class="welcome-ring ring-one"></div>
                <div class="welcome-ring ring-two"></div>
                <div class="welcome-symbol">✦</div>
            </div>
        </section>

        <?php if ($success = getFlash('success')): ?>
            <div class="ngo-alert success-alert"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = getFlash('error')): ?>
            <div class="ngo-alert error-alert"><?= e($error) ?></div>
        <?php endif; ?>

        <section class="ngo-stats-grid">
            <article class="ngo-stat-card">
                <div class="ngo-stat-icon green-stat">↗</div>
                <div>
                    <span>Total funds raised</span>
                    <strong><?= number_format($totalRaised, 2) ?></strong>
                    <small>Across all your campaigns</small>
                </div>
            </article>

            <article class="ngo-stat-card">
                <div class="ngo-stat-icon orange-stat">◈</div>
                <div>
                    <span>Total campaigns</span>
                    <strong><?= number_format($campaignCount) ?></strong>
                    <small><?= number_format($activeCampaigns) ?> currently active</small>
                </div>
            </article>

            <article class="ngo-stat-card">
                <div class="ngo-stat-icon purple-stat">♡</div>
                <div>
                    <span>Donors supporting you</span>
                    <strong><?= number_format($donorCount) ?></strong>
                    <small><?= number_format($donationCount) ?> donations received</small>
                </div>
            </article>
        </section>

        <section class="ngo-section">
            <div class="ngo-section-heading">
                <div>
                    <span class="section-kicker">FUNDRAISING PERFORMANCE</span>
                    <h2>Your overall progress</h2>
                    <p>See how your organisation is progressing towards its fundraising targets.</p>
                </div>
            </div>

            <div class="overall-progress-card">
                <div class="overall-progress-top">
                    <div>
                        <span>Total raised</span>
                        <strong><?= number_format($totalRaised, 2) ?></strong>
                    </div>
                    <div class="progress-percentage">
                        <?= number_format($overallProgress, 1) ?>%
                    </div>
                </div>

                <div class="overall-progress-track">
                    <div class="overall-progress-fill" style="width: <?= $overallProgress ?>%"></div>
                </div>

                <div class="overall-progress-bottom">
                    <span>Target: <?= number_format($totalTarget, 2) ?></span>
                    <span><?= number_format(max(0, $totalTarget - $totalRaised), 2) ?> remaining</span>
                </div>
            </div>
        </section>

        <section class="ngo-section" id="campaigns">
            <div class="ngo-section-heading heading-with-action">
                <div>
                    <span class="section-kicker">YOUR FUNDRAISING</span>
                    <h2>Campaigns</h2>
                    <p>Monitor the performance of your organisation's campaigns.</p>
                </div>
                <a href="<?= BASE_URL ?>/ngo/campaigns" class="view-all-link">View all campaigns →</a>
            </div>

            <?php if (empty($campaigns)): ?>
                <div class="ngo-empty-state">
                    <div class="empty-icon">◈</div>
                    <h3>You haven't created a campaign yet</h3>
                    <p>Start your first campaign and give supporters a way to contribute.</p>
                    <a href="<?= BASE_URL ?>/ngo/create-campaign" class="empty-action">Create your first campaign</a>
                </div>
            <?php else: ?>
                <div class="ngo-campaign-grid">
                    <?php foreach (array_slice($campaigns, 0, 4) as $campaign): ?>
                        <?php
                        $target = (float)$campaign['target_amount'];
                        $raised = (float)$campaign['raised_amount'];
                        $progress = $target > 0 ? min(100, ($raised / $target) * 100) : 0;
                        $isActive = $campaign['end_date'] >= date('Y-m-d') && $raised < $target;
                        ?>
                        <article class="ngo-campaign-card">
                            <div class="campaign-card-header">
                                <span class="campaign-category">FUNDRAISING</span>
                                <span class="campaign-status <?= $isActive ? 'status-active' : 'status-closed' ?>">
                                    <?= $isActive ? 'Active' : 'Closed' ?>
                                </span>
                            </div>

                            <h3><?= e($campaign['title']) ?></h3>
                            <p class="campaign-description"><?= e($campaign['description']) ?></p>

                            <div class="campaign-numbers">
                                <strong><?= number_format($raised, 2) ?></strong>
                                <span>of <?= number_format($target, 2) ?></span>
                            </div>

                            <div class="campaign-progress-track">
                                <div class="campaign-progress-fill" style="width: <?= $progress ?>%"></div>
                            </div>

                            <div class="campaign-progress-footer">
                                <span><?= number_format($progress, 1) ?>% funded</span>
                                <span>Ends <?= e(date('d M Y', strtotime($campaign['end_date']))) ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="ngo-section" id="donations">
            <div class="ngo-section-heading">
                <div>
                    <span class="section-kicker">COMMUNITY SUPPORT</span>
                    <h2>Recent donations</h2>
                    <p>See the latest contributions made to your campaigns.</p>
                </div>
            </div>

            <div class="ngo-history-card">
                <?php if (empty($donations)): ?>
                    <div class="ngo-empty-state compact-empty">
                        <div class="empty-icon">♡</div>
                        <h3>No donations received yet</h3>
                        <p>Donations from supporters will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="ngo-table-scroll">
                        <table class="ngo-donation-table">
                            <thead>
                                <tr>
                                    <th>Donor</th>
                                    <th>Campaign</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donations as $donation): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($donation['donor_name']) ?></strong>
                                            <small><?= e($donation['donor_email']) ?></small>
                                        </td>
                                        <td><?= e($donation['campaign_title']) ?></td>
                                        <td><?= e(date('d M Y', strtotime($donation['donated_at']))) ?></td>
                                        <td class="received-amount">
                                            <?= number_format((float)$donation['amount'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <footer class="ngo-footer">
            <span>© <?= date('Y') ?> HopeGiving. Empowering organisations to make a difference.</span>
            <span>Built on generosity <strong>♡</strong></span>
        </footer>

    </main>
</div>

</body>
</html>
