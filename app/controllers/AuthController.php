<?php

class AuthController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/register');
        }

        $username = trim($_POST['username'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'donor';

        $ngoName = trim($_POST['ngo_name'] ?? '');
        $registrationNo = trim($_POST['registration_no'] ?? '');
        $contactEmail = strtolower(trim($_POST['contact_email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');

        if (strlen($username) < 3 || strlen($username) > 100) {
            setFlash('error', 'Your name must be between 3 and 100 characters.');
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            redirect('/register');
        }

        if (strlen($password) < 8) {
            setFlash('error', 'Password must contain at least 8 characters.');
            redirect('/register');
        }

        if ($password !== $confirmPassword) {
            setFlash('error', 'The passwords do not match.');
            redirect('/register');
        }

        if (!in_array($role, ['donor', 'ngo'], true)) {
            setFlash('error', 'Please select a valid account type.');
            redirect('/register');
        }

        if ($role === 'ngo') {
            if ($ngoName === '' || $registrationNo === '') {
                setFlash('error', 'NGO name and registration number are required.');
                redirect('/register');
            }

            if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                setFlash('error', 'Please enter a valid NGO contact email.');
                redirect('/register');
            }
        }

        try {
            $this->db->beginTransaction();

            $check = $this->db->prepare(
                'SELECT user_id FROM users WHERE email = :email LIMIT 1'
            );
            $check->execute(['email' => $email]);

            if ($check->fetch()) {
                $this->db->rollBack();
                setFlash('error', 'An account with this email already exists.');
                redirect('/register');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                'INSERT INTO users (username, email, password_hash, role, account_status)
                 VALUES (:username, :email, :password_hash, :role, :account_status)'
            );

            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
                'role' => $role,
                'account_status' => 'active'
            ]);

            $userId = (int) $this->db->lastInsertId();

            if ($role === 'ngo') {
                $ngoStmt = $this->db->prepare(
                    'INSERT INTO ngos
                    (user_id, name, registration_no, contact_email, phone, approval_status)
                    VALUES
                    (:user_id, :name, :registration_no, :contact_email, :phone, :approval_status)'
                );

                $ngoStmt->execute([
                    'user_id' => $userId,
                    'name' => $ngoName,
                    'registration_no' => $registrationNo,
                    'contact_email' => $contactEmail,
                    'phone' => $phone !== '' ? $phone : null,
                    'approval_status' => 'pending'
                ]);
            }

            $this->db->commit();

            if ($role === 'ngo') {
                setFlash(
                    'success',
                    'Your NGO application has been submitted. You can log in after an administrator approves it.'
                );
            } else {
                setFlash('success', 'Your donor account has been created. You can now log in.');
            }

            redirect('/login');

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log('Registration error: ' . $e->getMessage());
            setFlash('error', 'Registration could not be completed. Check that the email and NGO registration number are not already in use.');
            redirect('/register');
        }
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/login');
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            setFlash('error', 'Enter a valid email address and password.');
            redirect('/login');
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            setFlash('error', 'Incorrect email or password.');
            redirect('/login');
        }

        if (($user['account_status'] ?? 'active') !== 'active') {
            setFlash('error', 'Your account is suspended. Please contact the administrator.');
            redirect('/login');
        }

        if ($user['role'] === 'ngo') {
            $ngoStmt = $this->db->prepare(
                'SELECT approval_status FROM ngos WHERE user_id = :user_id LIMIT 1'
            );
            $ngoStmt->execute(['user_id' => $user['user_id']]);
            $ngo = $ngoStmt->fetch(PDO::FETCH_ASSOC);

            if (!$ngo || $ngo['approval_status'] !== 'approved') {
                if ($ngo && $ngo['approval_status'] === 'rejected') {
                    setFlash('error', 'Your NGO application was rejected. Please contact the administrator.');
                } else {
                    setFlash('error', 'Your NGO account is awaiting administrator approval.');
                }

                redirect('/login');
            }
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            redirect('/admin/dashboard');
        }

        if ($user['role'] === 'ngo') {
            redirect('/ngo/dashboard');
        }

        redirect('/donor/dashboard');
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/');
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid security token.');
            redirect('/');
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
