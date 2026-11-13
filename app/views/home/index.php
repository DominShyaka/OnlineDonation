<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Online Donation Platform</title>

    <link rel="stylesheet"
          href="/OnlineDonationPlatform/public/assets/css/style.css">
</head>

<body>

<header class="main-header">
    <div class="container">
        <h2>HopeGiving</h2>

        <nav>
            <a href="/OnlineDonationPlatform/">Home</a>

            <?php if (isLoggedIn()): ?>

                <span>Welcome, <?= e($_SESSION['username']) ?></span>

                <form action="/OnlineDonationPlatform/logout"
                      method="POST" class="inline-form">
                    <?= csrfField() ?>
                    <button type="submit">Logout</button>
                </form>

            <?php else: ?>

                <a href="/OnlineDonationPlatform/login">Login</a>
                <a href="/OnlineDonationPlatform/register">Register</a>

            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="hero">
    <div class="container">
        <h1>Together, We Can Make a Difference</h1>

        <p>
            Support meaningful causes, empower communities
            and help create a better future.
        </p>

        <?php if (!isLoggedIn()): ?>

            <a href="/OnlineDonationPlatform/register"
               class="hero-button">
                Get Started
            </a>

        <?php else: ?>

            <p>You are successfully logged in!</p>

        <?php endif; ?>
    </div>
</main>

<section class="about">
    <div class="container">
        <h2>About Our Platform</h2>

        <p>
            Our Online Donation Platform connects generous donors
            with NGOs working to improve lives through fundraising
            campaigns.
        </p>

        <div class="features">
            <div class="feature">
                <h3>For Donors</h3>
                <p>Support campaigns and make contributions.</p>
            </div>

            <div class="feature">
                <h3>For NGOs</h3>
                <p>Create campaigns and raise funds.</p>
            </div>

            <div class="feature">
                <h3>For Administrators</h3>
                <p>Monitor users, campaigns and donations.</p>
            </div>
        </div>
    </div>
</section>

<footer>
    <p>&copy; <?= date('Y') ?> HopeGiving. All rights reserved.</p>
</footer>

</body>
</html>
