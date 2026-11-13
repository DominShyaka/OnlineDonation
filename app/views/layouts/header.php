<?php
$pageTitle = $pageTitle ?? 'HopeGiving - Online Donation Platform';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>
<header class="main-header">
    <div class="container">
        <nav class="navbar">
            <a href="<?= BASE_URL ?>/" class="logo">HopeGiving</a>

            <div class="nav-links">
                <a href="<?= BASE_URL ?>/">Home</a>

                <?php if (isLoggedIn()): ?>

                    <?php if (currentUserRole() === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/admin/dashboard">Admin Dashboard</a>
                        <a href="<?= BASE_URL ?>/admin/dashboard#ngo-approvals">NGO Approvals</a>
                        <a href="<?= BASE_URL ?>/admin/dashboard#user-management">Users</a>

                    <?php elseif (currentUserRole() === 'ngo'): ?>
                        <a href="<?= BASE_URL ?>/ngo/dashboard">Dashboard</a>
                        <a href="<?= BASE_URL ?>/ngo/campaigns">My Campaigns</a>

                    <?php elseif (currentUserRole() === 'donor'): ?>
                        <a href="<?= BASE_URL ?>/donor/dashboard">My Dashboard</a>
                    <?php endif; ?>

                    <span>Hello, <?= e($_SESSION['username'] ?? 'User') ?></span>

                    <form method="POST" action="<?= BASE_URL ?>/logout" class="inline-form">
                        <?= csrfField() ?>
                        <button type="submit" class="auth-button">Logout</button>
                    </form>

                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login">Login</a>
                    <a href="<?= BASE_URL ?>/register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<main>
