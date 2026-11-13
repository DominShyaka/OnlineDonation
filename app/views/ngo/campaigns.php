<?php
$ngo = $ngo ?? [];
$campaigns = $campaigns ?? [];
$ngoName = $ngo['name'] ?? 'Your Organisation';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Campaigns | HopeGiving</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/ngo.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/ngo-campaigns.css">
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
            <a href="<?= BASE_URL ?>/ngo/dashboard" class="ngo-nav-link">
                <span>▦</span> Overview
            </a>
            <a href="<?= BASE_URL ?>/ngo/campaigns" class="ngo-nav-link active">
                <span>◈</span> My campaigns
            </a>
            <a href="<?= BASE_URL ?>/ngo/dashboard#donations" class="ngo-nav-link">
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
                <span class="topbar-label">FUNDRAISING MANAGEMENT</span>
                <h2>My campaigns</h2>
            </div>

            <div class="ngo-profile">
                <div class="ngo-avatar"><?= e(strtoupper(substr($ngoName, 0, 1))) ?></div>
                <div>
                    <strong><?= e($ngoName) ?></strong>
                    <span>Approved NGO</span>
                </div>
            </div>
        </header>

        <section class="ngo-welcome">
            <div class="welcome-content">
                <span class="welcome-label">YOUR ORGANISATION'S IMPACT</span>
                <h1>Every campaign tells a story.</h1>
                <p>Manage your fundraising activities, monitor progress and inspire more people to support your mission.</p>
                <a href="<?= BASE_URL ?>/ngo/create-campaign" class="create-campaign-button">
                    <span>＋</span> Create new campaign
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

        <section class="ngo-section">
            <div class="ngo-section-heading heading-with-action">
                <div>
                    <span class="section-kicker">CAMPAIGN OVERVIEW</span>
                    <h2>All your campaigns</h2>
                    <p>Track the performance of every campaign created by your organisation.</p>
                </div>
                <span class="campaign-count"><?= count($campaigns) ?> campaigns</span>
            </div>

            <?php if (empty($campaigns)): ?>
                <div class="ngo-empty-state">
                    <div class="empty-icon">◈</div>
                    <h3>No campaigns created yet</h3>
                    <p>Start fundraising by creating your first campaign.</p>
                    <a href="<?= BASE_URL ?>/ngo/create-campaign" class="empty-action">
                        Create your first campaign
                    </a>
                </div>
            <?php else: ?>

                <div class="ngo-all-campaigns">
                    <?php foreach ($campaigns as $campaign): ?>
                        <?php
                        $target = (float)$campaign['target_amount'];
                        $raised = (float)$campaign['raised_amount'];
                        $progress = $target > 0 ? min(100, ($raised / $target) * 100) : 0;

                        if ($raised >= $target) {
                            $status = 'Goal reached';
                            $statusClass = 'campaign-goal';
                        } elseif ($campaign['end_date'] < date('Y-m-d')) {
                            $status = 'Ended';
                            $statusClass = 'campaign-ended';
                        } else {
                            $status = 'Active';
                            $statusClass = 'campaign-active';
                        }
                        ?>

                        <article class="ngo-full-campaign">
                            <div class="full-campaign-main">
                                <div class="full-campaign-top">
                                    <span class="campaign-category">FUNDRAISING CAMPAIGN</span>
                                    <span class="full-campaign-status <?= $statusClass ?>">
                                        <?= e($status) ?>
                                    </span>
                                </div>

                                <h3><?= e($campaign['title']) ?></h3>
                                <p><?= e($campaign['description']) ?></p>

                                <div class="full-campaign-meta">
                                    <span>Created <?= e(date('d M Y', strtotime($campaign['created_at']))) ?></span>
                                    <span>Ends <?= e(date('d M Y', strtotime($campaign['end_date']))) ?></span>
                                </div>
                            </div>

                            <div class="full-campaign-progress">
                                <div class="campaign-numbers">
                                    <strong><?= number_format($raised, 2) ?></strong>
                                    <span>raised of <?= number_format($target, 2) ?></span>
                                </div>

                                <div class="campaign-progress-track">
                                    <div class="campaign-progress-fill" style="width: <?= $progress ?>%"></div>
                                </div>

                                <div class="campaign-progress-footer">
                                    <strong><?= number_format($progress, 1) ?>% funded</strong>
                                    <span><?= number_format(max(0, $target - $raised), 2) ?> remaining</span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </section>

        <footer class="ngo-footer">
            <span>© <?= date('Y') ?> HopeGiving. Empowering organisations to make a difference.</span>
            <span>Built on generosity <strong>♡</strong></span>
        </footer>

    </main>
</div>

</body>
</html>

