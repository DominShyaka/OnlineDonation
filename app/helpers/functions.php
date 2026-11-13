<?php

// Escape output to prevent XSS attacks
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Redirect to another page
function redirect($path)
{
    header("Location: " . BASE_URL . $path);
    exit;
}

// Store a one-time session message
function setFlash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}

// Retrieve and remove a session message
function getFlash($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    return null;
}
