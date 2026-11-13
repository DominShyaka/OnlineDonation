<?php
$ngo = $ngo ?? [];
$ngoName = $ngo['name'] ?? 'Your Organisation';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign | HopeGiving</title>
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
            <a href="<?= BASE_URL ?>/ngo/campaigns" class="ngo-nav-link">
                <span>◈</span> My campaigns
            </a>
            <a href="<?= BASE_URL ?>/ngo/dashboard#donations" class="ngo-nav-link">
                <span>↗</span> Donations received
            </a>
            <a href="<?= BASE_URL ?>/ngo/create-campaign" class="ngo-nav-link active">
                <span>＋</span> Create campaign
            </a>
        </nav>

        <div class="ngo-sidebar-bottom">
            <div class="ngo-help-card">
                <div class="help-icon">♡</div>
                <strong>Your mission matters</strong>
                <p>Give supporters a meaningful cause to believe in.</p>
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
                <h2>Create a campaign</h2>
            </div>

            <div class="ngo-profile">
                <div class="ngo-avatar"><?= e(strtoupper(substr($ngoName, 0, 1))) ?></div>
                <div>
                    <strong><?= e($ngoName) ?></strong>
                    <span>Approved NGO</span>
                </div>
            </div>
        </header>

        <section class="ngo-form-hero">
            <span class="welcome-label">TURN YOUR MISSION INTO ACTION</span>
            <h1>Start something meaningful.</h1>
            <p>Tell your story, set your fundraising goal and invite people to become part of your impact.</p>
        </section>

        <?php if ($success = getFlash('success')): ?>
            <div class="ngo-alert success-alert"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = getFlash('error')): ?>
            <div class="ngo-alert error-alert"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="campaign-form-layout">

            <section class="campaign-form-card">
                <div class="form-card-heading">
                    <div>
                        <span class="section-kicker">CAMPAIGN DETAILS</span>
                        <h2>Tell your story</h2>
                        <p>Provide clear information so donors understand your cause.</p>
                    </div>
                    <span class="form-step">01 / 01</span>
                </div>

                <form method="POST" action="<?= BASE_URL ?>/ngo/create-campaign" class="create-campaign-form">
                    <?= csrfField() ?>

                    <div class="form-group">
                        <label for="title">Campaign title <span>*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            minlength="5"
                            maxlength="150"
                            placeholder="e.g. Help children access quality education"
                            required
                        >
                        <small>Choose a clear title that explains your campaign.</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Campaign description <span>*</span></label>
                        <textarea
                            id="description"
                            name="description"
                            rows="7"
                            minlength="20"
                            maxlength="5000"
                            placeholder="Explain the problem, who will benefit, how donations will be used and the difference this campaign will make..."
                            required
                        ></textarea>
                        <div class="field-bottom">
                            <small>Write at least 20 characters.</small>
                            <small id="description-count">0 / 5000</small>
                        </div>
                    </div>

                    <div class="form-two-columns">
                        <div class="form-group">
                            <label for="target_amount">Fundraising target <span>*</span></label>
                            <input
                                type="number"
                                id="target_amount"
                                name="target_amount"
                                min="0.01"
                                step="0.01"
                                placeholder="e.g. 500000"
                                required
                            >
                            <small>Enter your target amount.</small>
                        </div>

                        <div class="form-group">
                            <label for="end_date">Campaign end date <span>*</span></label>
                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                min="<?= date('Y-m-d') ?>"
                                required
                            >
                            <small>Choose the final fundraising date.</small>
                        </div>
                    </div>

                    <div class="campaign-form-note">
                        <span>ⓘ</span>
                        <p>Your campaign will be available to donors once it is created. Make sure your details are accurate and your fundraising target is realistic.</p>
                    </div>

                    <div class="form-actions">
                        <a href="<?= BASE_URL ?>/ngo/campaigns" class="cancel-form-button">Cancel</a>
                        <button type="submit" class="submit-campaign-button">
                            Publish campaign <span>→</span>
                        </button>
                    </div>

                </form>
            </section>

            <aside class="campaign-tips-card">
                <div class="tips-icon">✦</div>
                <span class="section-kicker">A LITTLE GUIDANCE</span>
                <h3>Make your campaign stand out.</h3>
                <p>Great campaigns help supporters understand why their contribution matters.</p>

                <div class="campaign-tip">
                    <span>01</span>
                    <div>
                        <strong>Tell a real story</strong>
                        <p>Explain the challenge and who needs help.</p>
                    </div>
                </div>

                <div class="campaign-tip">
                    <span>02</span>
                    <div>
                        <strong>Be transparent</strong>
                        <p>Describe exactly how the funds will be used.</p>
                    </div>
                </div>

                <div class="campaign-tip">
                    <span>03</span>
                    <div>
                        <strong>Set a realistic goal</strong>
                        <p>Choose a target that matches your project's needs.</p>
                    </div>
                </div>

                <div class="campaign-tip">
                    <span>04</span>
                    <div>
                        <strong>Choose a clear deadline</strong>
                        <p>Give supporters a clear fundraising timeframe.</p>
                    </div>
                </div>

                <div class="tips-bottom">
                    <strong>Every contribution counts.</strong>
                    <span>HopeGiving <b>♡</b></span>
                </div>
            </aside>

        </div>

        <footer class="ngo-footer">
            <span>© <?= date('Y') ?> HopeGiving. Empowering organisations to make a difference.</span>
            <span>Built on generosity <strong>♡</strong></span>
        </footer>

    </main>
</div>

<script>
(function () {
    const description = document.getElementById('description');
    const counter = document.getElementById('description-count');

    if (description && counter) {
        const updateCounter = function () {
            counter.textContent = description.value.length + ' / 5000';
        };

        description.addEventListener('input', updateCounter);
        updateCounter();
    }
})();
</script>

</body>
</html>

