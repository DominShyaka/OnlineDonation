<?php

require_once __DIR__ . '/../../config/database.php';

class Database
{
    private $host = DB_HOST;
    private $database = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;

    public function connect()
    {
        try {
            $pdo = new PDO(
                "mysql:host=" . $this->host .
                ";dbname=" . $this->database .
                ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            $pdo->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );

            return $pdo;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
}
