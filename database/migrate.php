<?php

declare(strict_types=1);

/*
 * Usage:
 *   php database/migrate.php            Apply pending migrations
 *   php database/migrate.php --seed     Apply migrations, then seed content into empty tables
 *   php database/migrate.php --status   List migrations and whether they are applied
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$args = array_slice($argv, 1);
$pdo = Database::pdo();
$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (name VARCHAR(191) NOT NULL PRIMARY KEY, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

$applied = array_column(Database::all('SELECT name FROM migrations'), 'name');
$files = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($files);

if (in_array('--status', $args, true)) {
    foreach ($files as $file) {
        $name = basename($file);
        echo (in_array($name, $applied, true) ? '[x] ' : '[ ] ') . $name . PHP_EOL;
    }
    exit(0);
}

foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        continue;
    }
    echo "Applying {$name} ... ";
    $sql = (string) file_get_contents($file);
    // Split on semicolons at line ends (migrations must not contain procedures/triggers with inner semicolons).
    $statements = array_filter(array_map('trim', preg_split('/;\s*$/m', $sql) ?: []), static function (string $s): bool {
        $s = trim((string) preg_replace('/^\s*--.*$/m', '', $s));
        return $s !== '';
    });
    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
    Database::run('INSERT INTO migrations (name) VALUES (:n)', ['n' => $name]);
    echo "done\n";
}
echo "Migrations up to date.\n";

if (in_array('--seed', $args, true)) {
    require __DIR__ . '/seed.php';
}
