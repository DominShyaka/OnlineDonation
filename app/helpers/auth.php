<?php

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function currentUserRole()
{
    return $_SESSION['role'] ?? null;
}

function requireLogin()
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to continue.');
        redirect('/login');
    }
}

function requireRole($role)
{
    requireLogin();

    if (currentUserRole() !== $role) {
        http_response_code(403);
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
}
