<?php
$pageTitle = 'Login - HopeGiving';
require __DIR__ . '/../layouts/header.php';
?>

<section class="auth-container">
    <div class="auth-card">
        <h1>Welcome Back!</h1>
        <p>Log in to your HopeGiving account.</p>

        <?php if ($error = getFlash('error')): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success = getFlash('success')): ?>
            <div class="alert success"><?= e($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login">
            <?= csrfField() ?>

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
                autocomplete="email"
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
            >

            <button type="submit" class="auth-button">
                Login
            </button>
        </form>

        <p class="auth-link">
            Don't have an account?
            <a href="<?= BASE_URL ?>/register">Register here</a>
        </p>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
