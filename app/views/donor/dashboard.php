<?php
$username = $username ?? 'Donor';
$stats = $stats ?? [];
$campaigns = $campaigns ?? [];
$donations = $donations ?? [];

$totalDonated = (float)($stats['total_donated'] ?? 0);
$donationCount = (int)($stats['donation_count'] ?? 0);
$campaignsSupported = (int)($stats['campaigns_supported'] ?? 0);
$initial = strtoupper(substr($username, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard | HopeGiving</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/donor.css">
</head>
<body class="donor-body">

<div class="donor-layout">

    <aside class="donor-sidebar">
        <a href="<?= BASE_URL ?>/donor/dashboard" class="donor-brand">
            <span class="brand-mark">H</span>
            <span>Hope<span>Giving</span></span>
        </a>

        <div class="sidebar-caption">YOUR WORKSPACE</div>

        <nav class="donor-navigation">
            <a class="nav-link active" href="<?= BASE_URL ?>/donor/dashboard">
                <span>▦</span> Dashboard
            </a>
            <a class="nav-link" href="#campaigns">
                <span>♡</span> Explore campaigns
            </a>
            <a class="nav-link" href="#donation-history">
                <span>↗</span> My donations
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="sidebar-help">
                <div class="help-icon">?</div>
                <strong>Every act matters</strong>
                <p>Your generosity helps communities move forward.</p>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/logout">
                <?= csrfField() ?>
                <button type="submit" class="logout-button">
                    <span>↪</span> Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="donor-main">

        <header class="donor-topbar">
            <div>
                <span class="topbar-label">DONOR PORTAL</span>
                <p>Welcome back, <?= e($username) ?></p>
            </div>

            <div class="donor-profile">
                <div class="profile-avatar"><?= e($initial) ?></div>
                <div>
                    <strong><?= e($username) ?></strong>
                    <span>Donor account</span>
                </div>
            </div>
        </header>

        <section class="donor-hero">
            <div class="hero-content">
                <span class="hero-eyebrow">MAKE A DIFFERENCE TODAY</span>
                <h1>Your kindness can<br>change someone's world.</h1>
                <p>Discover meaningful causes and support the communities that need it most.</p>
                <a href="#campaigns" class="hero-button">Explore campaigns <span>→</span></a>
            </div>
            <div class="hero-decoration" aria-hidden="true">
                <div class="hero-circle circle-one"></div>
                <div class="hero-circle circle-two"></div>
                <div class="hero-heart">♡</div>
            </div>
        </section>

        <?php if ($success = getFlash('success')): ?>
            <div class="donor-alert success-alert"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = getFlash('error')): ?>
            <div class="donor-alert error-alert"><?= e($error) ?></div>
        <?php endif; ?>

        <section class="stats-grid">
            <article class="stat-card">
                <div class="stat-icon green-icon">↗</div>
                <div>
                    <span>Total donated</span>
                    <strong><?= number_format($totalDonated, 2) ?></strong>
                    <small>Your total contribution</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon orange-icon">♡</div>
                <div>
                    <span>Donations made</span>
                    <strong><?= number_format($donationCount) ?></strong>
                    <small>Times you have given</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon purple-icon">◎</div>
                <div>
                    <span>Campaigns supported</span>
                    <strong><?= number_format($campaignsSupported) ?></strong>
                    <small>Causes you've supported</small>
                </div>
            </article>
        </section>

        <section class="section-block" id="campaigns">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">MAKE AN IMPACT</span>
                    <h2>Discover campaigns</h2>
                    <p>Find a cause that matters to you and help make it happen.</p>
                </div>
                <span class="result-count"><?= count($campaigns) ?> available</span>
            </div>

            <?php if (empty($campaigns)): ?>
                <div class="empty-state">
                    <div class="empty-icon">♡</div>
                    <h3>No campaigns available right now</h3>
                    <p>Approved campaigns will appear here when NGOs publish them.</p>
                </div>
            <?php else: ?>
                <div class="campaign-grid">
                    <?php foreach ($campaigns as $campaign): ?>
                        <?php
                        $target = (float)$campaign['target_amount'];
                        $raised = (float)$campaign['raised_amount'];
                        $progress = $target > 0 ? min(100, ($raised / $target) * 100) : 0;
                        ?>
                        <article class="campaign-card">
                            <div class="campaign-card-top">
                                <span class="campaign-tag">COMMUNITY CAMPAIGN</span>
                                <span class="campaign-heart">♡</span>
                            </div>

                            <h3><?= e($campaign['title']) ?></h3>
                            <p class="campaign-description">
                                <?= e($campaign['description']) ?>
                            </p>

                            <div class="campaign-ngo">
                                <span class="ngo-avatar">N</span>
                                <span>
                                    <small>Organised by</small>
                                    <strong><?= e($campaign['ngo_name']) ?></strong>
                                </span>
                            </div>

                            <div class="progress-details">
                                <strong><?= number_format($raised, 2) ?></strong>
                                <span>of <?= number_format($target, 2) ?></span>
                            </div>

                            <div class="progress-track">
                                <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                            </div>

                            <div class="campaign-footer">
                                <span><?= number_format($progress, 1) ?>% funded</span>
                                <span>Ends <?= e(date('d M Y', strtotime($campaign['end_date']))) ?></span>
                            </div>

                            <form method="POST" action="<?= BASE_URL ?>/donor/dashboard" class="donation-form">
                                <?= csrfField() ?>
                                <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id'] ?>">

                                <label for="amount-<?= (int)$campaign['campaign_id'] ?>">Donation amount</label>
                                <div class="amount-input-wrap">
                                    <input
                                        type="number"
                                        id="amount-<?= (int)$campaign['campaign_id'] ?>"
                                        name="amount"
                                        min="0.01"
                                        step="0.01"
                                        max="<?= max(0, $target - $raised) ?>"
                                        placeholder="Enter amount"
                                        required
                                    >
                                </div>

                                <button type="submit" class="donate-button">
                                    Donate to this campaign <span>→</span>
                                </button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-block history-section" id="donation-history">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">YOUR GENEROSITY</span>
                    <h2>Donation history</h2>
                    <p>Keep track of the causes you have helped.</p>
                </div>
            </div>

            <div class="history-card">
                <?php if (empty($donations)): ?>
                    <div class="empty-state compact-empty">
                        <div class="empty-icon">↗</div>
                        <h3>Your donation history is empty</h3>
                        <p>Your contributions will appear here after your first donation.</p>
                    </div>
                <?php else: ?>
                    <div class="table-scroll">
                        <table class="donation-table">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Organisation</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donations as $donation): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($donation['campaign_title']) ?></strong>
                                        </td>
                                        <td><?= e($donation['ngo_name']) ?></td>
                                        <td><?= e(date('d M Y', strtotime($donation['donated_at']))) ?></td>
                                        <td class="donation-amount">
                                            <?= number_format((float)$donation['amount'], 2) ?>
                                        </td>
                                        <td><span class="recorded-status">Recorded</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <footer class="donor-footer">
            <span>© <?= date('Y') ?> HopeGiving. Together, we make a difference.</span>
            <span>Giving with purpose <strong>♡</strong></span>
        </footer>

    </main>
</div>

</body>
</html>
