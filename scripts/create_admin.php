<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This setup script can only run from the command line.\n");
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = (new Database())->connect();

    $adminCount = (int) $db->query(
        "SELECT COUNT(*) FROM users WHERE role = 'admin'"
    )->fetchColumn();

    if ($adminCount > 0) {
        exit("An administrator account already exists. No new admin was created.\n");
    }

    echo "HOPEGIVING - INITIAL ADMINISTRATOR SETUP\n";
    echo "Administrator username: ";
    $username = trim(fgets(STDIN));

    echo "Administrator email: ";
    $email = strtolower(trim(fgets(STDIN)));

    echo "Administrator password: ";
    $password = trim(fgets(STDIN));

    if (strlen($username) < 3 || strlen($username) > 100) {
        exit("Username must contain 3 to 100 characters.\n");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit("A valid email address is required.\n");
    }

    if (strlen($password) < 12) {
        exit("Administrator password must contain at least 12 characters.\n");
    }

    $stmt = $db->prepare(
        "INSERT INTO users (username, email, password_hash, role, account_status)
         VALUES (:username, :email, :password_hash, 'admin', 'active')"
    );

    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT)
    ]);

    echo "Administrator account created successfully.\n";
    echo "You can now log in using the administrator email and password.\n";

} catch (PDOException $e) {
    fwrite(STDERR, "Admin setup failed: " . $e->getMessage() . "\n");
    exit(1);
}
