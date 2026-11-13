<?php

class Controller
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    protected function view(string $view, array $data = []): void
    {
        $file = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        extract($data, EXTR_SKIP);
        require $file;
    }
}
