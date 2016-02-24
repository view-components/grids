<?php

namespace ViewComponents\Grids\Demo;

function create_env_file()
{
    echo PHP_EOL, 'Creating .env file... ';
    $dir = __DIR__;
    copy("$dir/.env.example", "$dir/.env");
    echo 'Done.', PHP_EOL;
}



function db_seed()
{
    echo PHP_EOL, 'Seeding db... ';

    $pdo = db_connection();
    $sql = file_get_contents(FIXTURES_DIR . '/db.sql');
    foreach(explode(';', $sql) as $query) {
        if (!trim($query)) {
            continue;
        }
        // don't drop & create db for sqlite
        if (is_sqlite() && (
                strpos($query, 'DATABASE') !== false
                || strpos($query, 'USE ') !== false
            )) {
            continue;
        }
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
        } catch (\Exception $e) {
            echo $e;
            echo PHP_EOL, 'Query: ', $query;
            die();
        }
    }
    echo 'Done.', PHP_EOL;
}

chdir(__DIR__);
create_env_file();

require __DIR__ . '/bootstrap.php';

db_seed();



