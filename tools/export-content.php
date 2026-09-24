<?php

declare(strict_types=1);

/*
 * Dumps the editable content tables to a SQL file so a local database can be copied onto the
 * server (or the other way round). Accounts, logs, leads and throttling data are never exported:
 * those belong to the environment they were created in.
 *
 * Reviews are deliberately excluded too. Real customer reviews only ever exist in production
 * (CLAUDE.md §11), and a development database collects test rows from tests/admin-e2e.php that
 * must never reach the live site.
 *
 * Usage: php tools/export-content.php [output.sql]
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

// Parents before children: the import replays this order with foreign key checks switched off,
// but keeping it correct means the file also works when replayed by hand.
const TABLES = [
    'media',
    'services',
    'areas',
    'service_area',
    'projects',
    'project_images',
    'faqs',
    'posts',
    'seo_metadata',
    'settings',
];

$out = $argv[1] ?? dirname(__DIR__) . '/deploy/content.sql';
@mkdir(dirname($out), 0775, true);

$pdo = Database::pdo();
$sql = "-- Dubai Towing Experts content export\n"
     . '-- generated ' . date('Y-m-d H:i:s') . "\n"
     . "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

$rowTotal = 0;
foreach (TABLES as $table) {
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    $sql .= "-- $table (" . count($rows) . " rows)\nDELETE FROM `$table`;\n";
    foreach ($rows as $row) {
        $cols = array_map(static fn(string $c): string => "`$c`", array_keys($row));
        $vals = array_map(
            static fn($v): string => $v === null ? 'NULL' : $pdo->quote((string) $v),
            array_values($row)
        );
        $sql .= "INSERT INTO `$table` (" . implode(', ', $cols) . ') VALUES ('
              . implode(', ', $vals) . ");\n";
    }
    $sql .= "\n";
    $rowTotal += count($rows);
    printf("  %-16s %4d rows\n", $table, count($rows));
}

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
file_put_contents($out, $sql);

printf("\n%d rows across %d tables -> %s (%.1f KB)\n", $rowTotal, count(TABLES), $out, filesize($out) / 1024);
echo "Import with: mysql -u<user> -p <db> < " . basename($out) . "\n";
