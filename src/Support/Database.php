<?php

declare(strict_types=1);

namespace SqliLab\Support;

use PDO;
use Throwable;

final class Database
{
    public static function tryConnection(): ?PDO
    {
        try {
            $host = Env::get('DB_HOST', 'mysql');
            $port = Env::get('DB_PORT', '3306');
            $database = Env::get('DB_DATABASE', 'sqli_lab');
            $username = Env::get('DB_USERNAME', 'sqli_lab');
            $password = Env::get('DB_PASSWORD', 'sqli_lab_dev_password');
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

            return new PDO($dsn, (string) $username, (string) $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (Throwable) {
            return null;
        }
    }
}
