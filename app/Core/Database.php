<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;
use InvalidArgumentException;

/**
 * Thin PDO wrapper. All values are bound; identifiers passed to insert()/update()
 * come from code (never user input) and are additionally validated.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $c = config('database');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $c['host'], $c['port'], $c['database'], $c['charset']);
            self::$pdo = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
            self::$pdo->exec("SET time_zone = '+04:00'");
        }
        return self::$pdo;
    }

    public static function setPdo(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /** @param array<string|int, mixed> $params */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        foreach ($params as $key => $value) {
            $name = is_int($key) ? $key + 1 : (str_starts_with($key, ':') ? $key : ':' . $key);
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                $value === null => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };
            $stmt->bindValue($name, $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    /** @return array<string, mixed>|null */
    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** @return list<array<string, mixed>> */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $v = self::run($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    /** @param array<string, mixed> $data */
    public static function insert(string $table, array $data): int
    {
        self::assertIdentifier($table);
        $cols = array_keys($data);
        array_walk($cols, [self::class, 'assertIdentifier']);
        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`, `', $cols),
            implode(', ', array_map(static fn (string $c): string => ':' . $c, $cols))
        );
        self::run($sql, $data);
        return (int) self::pdo()->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public static function update(string $table, int $id, array $data): void
    {
        if ($data === []) {
            return;
        }
        self::assertIdentifier($table);
        $sets = [];
        foreach (array_keys($data) as $col) {
            self::assertIdentifier($col);
            $sets[] = "`{$col}` = :{$col}";
        }
        $data['__id'] = $id;
        self::run(sprintf('UPDATE `%s` SET %s WHERE `id` = :__id', $table, implode(', ', $sets)), $data);
    }

    public static function delete(string $table, int $id): void
    {
        self::assertIdentifier($table);
        self::run("DELETE FROM `{$table}` WHERE `id` = :id", ['id' => $id]);
    }

    public static function transaction(callable $fn): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $fn();
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function assertIdentifier(string $name): void
    {
        if (!preg_match('/^[a-z_][a-z0-9_]{0,63}$/', $name)) {
            throw new InvalidArgumentException('Invalid SQL identifier.');
        }
    }
}
