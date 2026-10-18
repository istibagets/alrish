<?php
// CLI only: php set_password.php <admin|user> <new-password>
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/db.php';
$role = $argv[1] ?? '';
$pass = $argv[2] ?? '';
if (!in_array($role, ['admin', 'user'], true) || strlen($pass) < 8) {
    fwrite(STDERR, "Usage: php set_password.php <admin|user> <password (min 8 chars)>\n");
    exit(1);
}
$pdo = getDb();
ensureUsersTable($pdo);
$hash = password_hash($pass, PASSWORD_DEFAULT);
$upd = $pdo->prepare("UPDATE inventory_users SET password_hash = ? WHERE role = ?");
$upd->execute([$hash, $role]);
if ($upd->rowCount() === 0) {
    $pdo->prepare("INSERT INTO inventory_users (role, password_hash) VALUES (?, ?)")->execute([$role, $hash]);
}
echo "Password for '$role' updated.\n";
