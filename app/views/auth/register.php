<?php
$pageTitle = 'Create Account - HopeGiving';
require __DIR__ . '/../layouts/header.php';
?>

<section class="auth-container">
    <div class="auth-card">
        <h1>Create Your Account</h1>
        <p>Join HopeGiving and become part of a community making a difference.</p>

        <?php if ($error = getFlash('error')): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/register" id="registrationForm">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="username">Full Name</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    minlength="3"
                    maxlength="100"
                    required
                    placeholder="Enter your full name"
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    placeholder="you@example.com"
                >
            </div>

            <div class="form-group">
                <label for="role">Account Type</label>
                <select id="role" name="role" required>
                    <option value="donor">Donor - I want to support campaigns</option>
                    <option value="ngo">NGO - I want to manage fundraising campaigns</option>
                </select>
            </div>

            <div id="ngoFields" hidden>
                <h3>NGO Information</h3>
                <p>NGO accounts require administrator approval before login is allowed.</p>

                <div class="form-group">
                    <label for="ngo_name">Organisation Name</label>
                    <input
                        type="text"
                        id="ngo_name"
                        name="ngo_name"
                        maxlength="150"
                        placeholder="Your organisation's registered name"
                    >
                </div>

                <div class="form-group">
                    <label for="registration_no">NGO Registration Number</label>
                    <input
                        type="text"
                        id="registration_no"
                        name="registration_no"
                        maxlength="100"
                        placeholder="Official registration number"
                    >
                </div>

                <div class="form-group">
                    <label for="contact_email">NGO Contact Email</label>
                    <input
                        type="email"
                        id="contact_email"
                        name="contact_email"
                        placeholder="organisation@example.com"
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        maxlength="30"
                        placeholder="Organisation phone number"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    required
                    placeholder="At least 8 characters"
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    minlength="8"
                    required
                    placeholder="Enter your password again"
                >
            </div>

            <button type="submit" class="auth-button">Create Account</button>
        </form>

        <p class="auth-link">
            Already have an account?
            <a href="<?= BASE_URL ?>/login">Log in here</a>
        </p>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const ngoFields = document.getElementById('ngoFields');
    const ngoInputs = ngoFields.querySelectorAll('input');

    function updateNgoFields() {
        const isNgo = roleSelect.value === 'ngo';

        ngoFields.hidden = !isNgo;

        ngoInputs.forEach(function (input) {
            input.required = isNgo;
        });
    }

    roleSelect.addEventListener('change', updateNgoFields);
    updateNgoFields();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
