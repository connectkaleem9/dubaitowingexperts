<?php

declare(strict_types=1);

/*
 * Create an admin account (no default credentials are ever shipped).
 * Usage: php database/create-admin.php "Full Name" email@example.com [owner|editor]
 * The password is read from the terminal (not echoed on macOS/Linux).
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Models\Admin;

[$script, $name, $email, $role] = array_pad($argv, 4, null);
$role ??= 'owner';

if (!$name || !$email || filter_var($email, FILTER_VALIDATE_EMAIL) === false || !array_key_exists($role, Admin::ROLES)) {
    fwrite(STDERR, "Usage: php database/create-admin.php \"Full Name\" email@example.com [owner|editor]\n");
    exit(1);
}
if (Admin::findByEmail($email)) {
    fwrite(STDERR, "An admin with that email already exists.\n");
    exit(1);
}

$readHidden = static function (string $prompt): string {
    echo $prompt;
    // Hiding the input needs stty, which many shared hosts disable. Without it the password is
    // still read correctly, it is simply echoed to the terminal.
    $canHide = stripos(PHP_OS_FAMILY, 'Windows') !== 0 && function_exists('shell_exec');
    if ($canHide) {
        shell_exec('stty -echo');
    }
    $value = rtrim((string) fgets(STDIN), "\r\n");
    if ($canHide) {
        shell_exec('stty echo');
    }
    echo PHP_EOL;
    return $value;
};

$password = $readHidden('Password (min 12 characters): ');
if (mb_strlen($password) < 12) {
    fwrite(STDERR, "Password must be at least 12 characters.\n");
    exit(1);
}
if ($readHidden('Confirm password: ') !== $password) {
    fwrite(STDERR, "Passwords do not match.\n");
    exit(1);
}

$id = Admin::create($name, $email, $password, $role);
echo "Admin #{$id} created ({$role}). Sign in at /admin/login/\n";
