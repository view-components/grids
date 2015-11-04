<?php

namespace Presentation\Framework\Demo;

require __DIR__ . '/../vendor/autoload.php';

define('FIXTURES_DIR', __DIR__ . '/fixtures');
define('GRIDS_LIB_DIR', dirname(__DIR__));

use Dotenv;
use PDO;

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

Dotenv::load(__DIR__);
Dotenv::required([
    'DB_DSN',
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD'
]);

function is_sqlite()
{
    return strpos(getenv('DB_DSN'), 'sqlite:') !== false;
}

function db_connection() {
    static $db;
    if ($db === null) {
        $dsn = getenv('DB_DSN');

        $selectDb = !is_sqlite();
        if ($selectDb) {
            $dbName = getenv('DB_NAME');
            $dsn.=";dbname=$dbName";
        }
        $db = new PDO(
            $dsn,
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => 1
            ]
        );
    }
    return $db;
}
chdir(__DIR__);