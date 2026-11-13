<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ini_set('session.use_strict_mode', '1');

require_once __DIR__ . '/config/database.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineDonationPlatform/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Controller.php';

require_once __DIR__ . '/app/helpers/functions.php';
require_once __DIR__ . '/app/helpers/csrf.php';
require_once __DIR__ . '/app/helpers/auth.php';

require_once __DIR__ . '/app/models/User.php';
require_once __DIR__ . '/app/models/NGO.php';
require_once __DIR__ . '/app/models/Campaign.php';
require_once __DIR__ . '/app/models/Donation.php';

require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/NGOController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';
require_once __DIR__ . '/app/controllers/DonorController.php';

try {
    $database = new Database();
    $db = $database->connect();

    $authController = new AuthController($db);
    $ngoController = new NGOController($db);
    $adminController = new AdminController($db);
    $donorController = new DonorController($db);

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('The application could not connect to the database.');
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = BASE_URL;

if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$path = '/' . trim($requestUri, '/');


/* Prevent authenticated users from returning to public/auth pages */
if (isLoggedIn()) {

    // Prevent browsers from restoring a cached landing page
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    if (in_array($path, ['/', '/login', '/register'], true)) {

        $role = currentUserRole();

        if ($role === 'admin') {
            redirect('/admin/dashboard');
        } elseif ($role === 'ngo') {
            redirect('/ngo/dashboard');
        } elseif ($role === 'donor') {
            redirect('/donor/dashboard');
        } else {
            redirect('/logout');
        }

        exit;
    }
}


if ($path === '//') {
    $path = '/';
}

switch ($path) {
    case '/':
        require __DIR__ . '/app/views/home/index.php';
        break;

    case '/register':
        $authController->register();
        break;

    case '/login':
        $authController->login();
        break;

    case '/logout':
        $authController->logout();
        break;

    case '/ngo/dashboard':
        $ngoController->dashboard();
        break;

    case '/ngo/campaigns':
        $ngoController->campaigns();
        break;

    case '/ngo/create-campaign':
        $ngoController->createCampaign();
        break;

    case '/admin/dashboard':
        $adminController->dashboard();
        break;

    case '/admin/ngo-review':
        $adminController->reviewNgo();
        break;

    case '/admin/account-status':
        $adminController->updateAccountStatus();
        break;
	case '/donor/dashboard':
    	$donorController->dashboard();
   	break;

    default:
        http_response_code(404);
        require __DIR__ . '/app/views/errors/404.php';
        break;
}

