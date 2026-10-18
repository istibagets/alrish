<?php
/**
 * Al Rish Al Khaleeji - Database Abstraction & Auto-Migration Layer
 * Supports MySQL / MariaDB (Adminer accessible) & SQLite fallback
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/icons.php';

class Database {
    private static ?PDO $instance = null;
    private static string $activeDriver = 'mysql';

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $driver = DB_DRIVER;

        if ($driver === 'mysql') {
            try {
                // First try connecting to MySQL server to ensure DB exists
                $dsnServer = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
                $serverPdo = new PDO($dsnServer, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 3
                ]);
                $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                // Connect to the specific database
                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                self::$activeDriver = 'mysql';
            } catch (Exception $e) {
                // If MySQL connection fails (e.g. MySQL server not started locally), fallback to SQLite
                error_log("MySQL connection failed (" . $e->getMessage() . "). Falling back to SQLite.");
                self::$instance = self::createSqliteConnection();
                self::$activeDriver = 'sqlite';
            }
        } else {
            self::$instance = self::createSqliteConnection();
            self::$activeDriver = 'sqlite';
        }

        self::initializeSchema(self::$instance, self::$activeDriver);
        self::autoMigrateFromSqlite(self::$instance, self::$activeDriver);

        return self::$instance;
    }

    private static function createSqliteConnection(): PDO {
        $sqlitePath = SQLITE_FILE;
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $pdo->exec('PRAGMA journal_mode = WAL;');
        $pdo->exec('PRAGMA busy_timeout = 5000;');
        return $pdo;
    }

    public static function getActiveDriver(): string {
        return self::$activeDriver;
    }

    private static function initializeSchema(PDO $pdo, string $driver): void {
        if ($driver === 'mysql') {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `inventory_categories` (
                    `slug` VARCHAR(64) PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `icon` VARCHAR(64) DEFAULT 'box',
                    `is_machine` TINYINT(1) DEFAULT 1
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            // Migration safety: check if is_machine column exists
            try {
                $chk = $pdo->query("SHOW COLUMNS FROM `inventory_categories` LIKE 'is_machine'");
                if ($chk && $chk->rowCount() === 0) {
                    $pdo->exec("ALTER TABLE `inventory_categories` ADD COLUMN `is_machine` TINYINT(1) DEFAULT 1;");
                }
            } catch (Exception $e) {}

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `inventory_items` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `category` VARCHAR(64) NOT NULL,
                    `item_name` VARCHAR(255) NOT NULL,
                    UNIQUE KEY `uniq_cat_item` (`category`, `item_name`),
                    INDEX `idx_category` (`category`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `inventory_logs` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `category` VARCHAR(64) NOT NULL,
                    `type` VARCHAR(32) NOT NULL,
                    `log_date` VARCHAR(32) NOT NULL,
                    `invoice` VARCHAR(128) DEFAULT '',
                    `client_supplier` VARCHAR(255) DEFAULT '',
                    `item_desc` VARCHAR(255) NOT NULL,
                    `quantity` INT NOT NULL DEFAULT 0,
                    `warranty` INT NOT NULL DEFAULT 0,
                    `created_at` VARCHAR(64) DEFAULT '',
                    INDEX `idx_cat_item` (`category`, `item_desc`),
                    INDEX `idx_log_date` (`log_date`),
                    INDEX `idx_type` (`type`),
                    INDEX `idx_invoice` (`invoice`),
                    INDEX `idx_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } else {
            // SQLite schema
            $pdo->exec("CREATE TABLE IF NOT EXISTS inventory_categories (
                slug TEXT PRIMARY KEY,
                name TEXT,
                icon TEXT,
                is_machine INTEGER DEFAULT 1
            )");

            // SQLite migration safety
            try {
                $cols = $pdo->query("PRAGMA table_info(inventory_categories)")->fetchAll(PDO::FETCH_ASSOC);
                $hasMachine = false;
                foreach ($cols as $col) {
                    if (($col['name'] ?? '') === 'is_machine') {
                        $hasMachine = true;
                        break;
                    }
                }
                if (!$hasMachine) {
                    $pdo->exec("ALTER TABLE inventory_categories ADD COLUMN is_machine INTEGER DEFAULT 1");
                }
            } catch (Exception $e) {}

            $pdo->exec("CREATE TABLE IF NOT EXISTS inventory_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT,
                item_name TEXT,
                UNIQUE(category, item_name)
            )");

            $pdo->exec("CREATE TABLE IF NOT EXISTS inventory_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT,
                type TEXT,
                log_date TEXT,
                invoice TEXT,
                client_supplier TEXT,
                item_desc TEXT,
                quantity INTEGER,
                warranty INTEGER DEFAULT 0,
                created_at TEXT DEFAULT ''
            )");
        }
    }

    /**
     * Automatically migrates data from SQLite inventory.db into MySQL if MySQL tables are empty.
     */
    private static function autoMigrateFromSqlite(PDO $pdo, string $driver): void {
        if ($driver !== 'mysql') {
            return;
        }

        $sqliteFile = SQLITE_FILE;
        if (!file_exists($sqliteFile)) {
            return;
        }

        // Check if MySQL already has categories
        $count = (int)$pdo->query("SELECT COUNT(*) FROM inventory_categories")->fetchColumn();
        if ($count > 0) {
            return; // Already populated
        }

        try {
            $sqlitePdo = new PDO('sqlite:' . $sqliteFile, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // Migrate Categories
            $catStmt = $sqlitePdo->query("SELECT slug, name, icon FROM inventory_categories");
            $insertCat = $pdo->prepare("INSERT IGNORE INTO inventory_categories (slug, name, icon) VALUES (:slug, :name, :icon)");
            while ($row = $catStmt->fetch()) {
                $insertCat->execute([
                    ':slug' => $row['slug'],
                    ':name' => $row['name'],
                    ':icon' => $row['icon'] ?? 'box'
                ]);
            }

            // Migrate Items
            $itemStmt = $sqlitePdo->query("SELECT category, item_name FROM inventory_items");
            $insertItem = $pdo->prepare("INSERT IGNORE INTO inventory_items (category, item_name) VALUES (:category, :item_name)");
            while ($row = $itemStmt->fetch()) {
                $insertItem->execute([
                    ':category' => $row['category'],
                    ':item_name' => $row['item_name']
                ]);
            }

            // Migrate Logs
            $logStmt = $sqlitePdo->query("SELECT id, category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at FROM inventory_logs ORDER BY id ASC");
            $insertLog = $pdo->prepare("INSERT INTO inventory_logs (id, category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at) VALUES (:id, :category, :type, :log_date, :invoice, :client_supplier, :item_desc, :quantity, :warranty, :created_at) ON DUPLICATE KEY UPDATE quantity=VALUES(quantity)");
            while ($row = $logStmt->fetch()) {
                $insertLog->execute([
                    ':id' => $row['id'],
                    ':category' => $row['category'],
                    ':type' => $row['type'],
                    ':log_date' => $row['log_date'] ?? date('Y-m-d'),
                    ':invoice' => $row['invoice'] ?? '',
                    ':client_supplier' => $row['client_supplier'] ?? '',
                    ':item_desc' => $row['item_desc'],
                    ':quantity' => (int)($row['quantity'] ?? 0),
                    ':warranty' => (int)($row['warranty'] ?? 0),
                    ':created_at' => $row['created_at'] ?? ''
                ]);
            }

            error_log("Successfully auto-migrated SQLite inventory.db to MySQL alrish_inventory!");
        } catch (Exception $e) {
            error_log("Auto-migration notice: " . $e->getMessage());
        }
    }
}

/**
 * Data Retrieval Helper Functions
 */

function getDb(): PDO {
    return Database::getConnection();
}

function fetchCategories(PDO $pdo): array {
    $categories = [];
    $stmt = $pdo->query("SELECT * FROM inventory_categories ORDER BY slug ASC");
    while ($row = $stmt->fetch()) {
        $row['icon_key'] = resolveCategoryIconKey((string)($row['icon'] ?? ''), (string)($row['name'] ?? ''));
        $categories[$row['slug']] = $row;
    }
    return $categories;
}

function fetchCatalog(PDO $pdo, array $categories): array {
    $catalog = [];
    $stmt = $pdo->prepare("SELECT item_name FROM inventory_items WHERE category = :cat ORDER BY id ASC");
    foreach ($categories as $slug => $catInfo) {
        $catalog[$slug] = [];
        $stmt->execute([':cat' => $slug]);
        while ($row = $stmt->fetch()) {
            $catalog[$slug][] = $row['item_name'];
        }
    }
    return $catalog;
}

function getStockData(PDO $pdo, string $category, string $item): array {
    // POS Inbound orders only (excluding admin system adjustments / initial stocks)
    $stmtIn = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE category = :cat AND item_desc = :item AND type = 'ORDER' AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') AND invoice NOT LIKE 'ADJ-%'");
    $stmtIn->execute([':cat' => $category, ':item' => $item]);
    $totalIn = (int)$stmtIn->fetchColumn();

    // POS Outbound sales, warranty, free
    $stmtOut = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE category = :cat AND item_desc = :item AND type IN ('SALE', 'WARRANTY', 'FREE') AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') AND invoice NOT LIKE 'ADJ-%'");
    $stmtOut->execute([':cat' => $category, ':item' => $item]);
    $totalOut = (int)$stmtOut->fetchColumn();

    // System Adjustments & Admin stock modifications
    $stmtAdj = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE category = :cat AND item_desc = :item AND (type = 'SYS-ADJUST' OR client_supplier IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') OR invoice LIKE 'ADJ-%')");
    $stmtAdj->execute([':cat' => $category, ':item' => $item]);
    $totalAdj = (int)$stmtAdj->fetchColumn();

    return [
        'in' => $totalIn,
        'out' => $totalOut,
        'balance' => $totalIn - $totalOut + $totalAdj
    ];
}

function fetchAllStocks(PDO $pdo, array $categories, array $catalog): array {
    $stocks = [];
    foreach ($categories as $slug => $c) {
        $stocks[$slug] = [];
        $items = $catalog[$slug] ?? [];
        foreach ($items as $item) {
            $stocks[$slug][$item] = getStockData($pdo, $slug, $item);
        }
    }
    return $stocks;
}

function fetchGroupedLogs(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM inventory_logs ORDER BY id DESC");
    $grouped = [];

    while ($row = $stmt->fetch()) {
        $inv = trim($row['invoice'] ?? '');
        $type = $row['type'];
        $client = trim($row['client_supplier'] ?? '');
        $createdAt = trim($row['created_at'] ?? '');
        $logDate = trim($row['log_date'] ?? '');

        $isSys = ($type === 'SYS-ADJUST' || strpos($inv, 'ADJ-') === 0 || $inv === 'SYS-ADJUST' || empty($inv) || $client === 'Inventory Correction' || $client === 'Initial Stock' || $client === 'Item Deleted');

        if (!empty($createdAt)) {
            $groupKey = $createdAt . '|' . $logDate;
        } elseif ($isSys) {
            $groupKey = 'SYS-ADJUST|' . $logDate . '|' . $row['id'];
        } else {
            $groupKey = $inv . '|' . $logDate . '|' . $client;
        }

        if (!isset($grouped[$groupKey])) {
            $grouped[$groupKey] = [
                'invoice' => $isSys ? 'SYS-ADJUST' : $inv,
                'client_supplier' => $isSys ? 'System Adjustment' : $row['client_supplier'],
                'log_date' => $row['log_date'],
                'created_at' => $createdAt,
                'type' => $isSys ? 'SYS-ADJUST' : $type,
                'items' => []
            ];
        }
        $grouped[$groupKey]['items'][] = $row;
    }

    // Determine parent group type based on children
    foreach ($grouped as $key => &$group) {
        $hasSale = false;
        $hasOrder = false;
        $hasSys = false;

        foreach ($group['items'] as $it) {
            if ($it['type'] === 'SALE' || $it['type'] === 'WARRANTY') {
                $hasSale = true;
            } elseif ($it['type'] === 'ORDER') {
                $hasOrder = true;
            } elseif ($it['type'] === 'SYS-ADJUST') {
                $hasSys = true;
            }
        }

        if ($group['invoice'] === 'SYS-ADJUST' || strpos($group['invoice'], 'ADJ-') === 0 || ($hasSys && !$hasSale && !$hasOrder)) {
            $group['type'] = 'SYS-ADJUST';
        } elseif (strpos($group['invoice'], 'INV-') === 0 || $hasSale) {
            $group['type'] = 'SALE';
        } elseif (strpos($group['invoice'], 'REC-') === 0 || $hasOrder) {
            $group['type'] = 'ORDER';
        } else {
            if ($group['type'] === 'FREE') {
                $group['type'] = 'SALE';
            }
        }
    }

    return $grouped;
}

function fetchDistinctCustomers(PDO $pdo): array {
    $stmt = $pdo->query("SELECT DISTINCT client_supplier FROM inventory_logs WHERE client_supplier != '' AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') ORDER BY client_supplier ASC");
    $customers = [];
    while ($row = $stmt->fetch()) {
        $c = trim($row['client_supplier'] ?? '');
        if (!empty($c) && !in_array($c, $customers, true)) {
            $customers[] = $c;
        }
    }
    return $customers;
}

function fetchWarrantyRecords(PDO $pdo): array {
    $warranties = [];
    $stmt = $pdo->query("SELECT id, category, invoice, client_supplier, item_desc, log_date, warranty, created_at FROM inventory_logs WHERE type IN ('SALE', 'WARRANTY') AND warranty > 0 ORDER BY log_date DESC");
    $today = new DateTime(date('Y-m-d'));

    while ($row = $stmt->fetch()) {
        try {
            $purchaseDate = new DateTime($row['log_date']);
            $expiryDate = clone $purchaseDate;
            $expiryDate->modify('+' . (int)$row['warranty'] . ' months');
            
            $expiryStr = $expiryDate->format('Y-m-d');
            $row['expiry_date'] = $expiryStr;
            $row['is_active'] = ($expiryDate >= $today);
            
            $diff = $today->diff($expiryDate);
            $daysLeft = (int)$diff->format('%r%a');
            $row['days_left'] = $daysLeft;

            $warranties[] = $row;
        } catch (Exception $e) {
            continue;
        }
    }

    usort($warranties, function($a, $b) {
        return strcmp($a['expiry_date'], $b['expiry_date']);
    });

    return $warranties;
}

function fetchCustomerMachineWarranties(PDO $pdo): array {
    $rawWarranties = fetchWarrantyRecords($pdo);
    $customerGroups = [];
    $today = new DateTime(date('Y-m-d'));

    // Also fetch all warranty claims / replacement parts (where warranty == 0 and type == 'WARRANTY' or invoice starts with WAR-)
    $claimStmt = $pdo->query("SELECT id, category, invoice, client_supplier, item_desc, quantity, log_date, created_at FROM inventory_logs WHERE type = 'WARRANTY' AND warranty = 0 ORDER BY log_date DESC, id DESC");
    $allClaims = $claimStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawWarranties as $w) {
        $client = trim($w['client_supplier']);
        if (empty($client)) continue;

        if (!isset($customerGroups[$client])) {
            $customerGroups[$client] = [
                'client_name' => $client,
                'total_machines' => 0,
                'active_warranties' => 0,
                'machines' => []
            ];
        }

        $customerGroups[$client]['total_machines']++;
        if ($w['is_active']) {
            $customerGroups[$client]['active_warranties']++;
        }

        // Find claimed parts for this specific machine warranty
        $machineClaims = [];
        $inv = trim($w['invoice']);
        foreach ($allClaims as $claim) {
            if (trim($claim['client_supplier']) === $client) {
                // If claim invoice references this machine invoice or matches client
                $claimInv = trim($claim['invoice']);
                if (empty($inv) || strpos($claimInv, $inv) !== false || $claimInv === 'WAR-' . $inv || strpos($claimInv, 'WAR-') === 0) {
                    $machineClaims[] = $claim;
                }
            }
        }

        $w['claims'] = $machineClaims;
        $customerGroups[$client]['machines'][] = $w;
    }

    // Sort customers with active warranties first, then alphabetically
    uasort($customerGroups, function($a, $b) {
        if ($a['active_warranties'] !== $b['active_warranties']) {
            return $b['active_warranties'] - $a['active_warranties'];
        }
        return strcasecmp($a['client_name'], $b['client_name']);
    });

    return $customerGroups;
}


/**
 * Account helpers (accounts live in the inventory_users table, passwords hashed)
 */
function ensureUsersTable(PDO $pdo): void {
    if (Database::getActiveDriver() === 'mysql') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `inventory_users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `role` VARCHAR(16) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } else {
        $pdo->exec("CREATE TABLE IF NOT EXISTS inventory_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role TEXT NOT NULL,
            password_hash TEXT NOT NULL
        )");
    }
    // First run only: seed accounts from environment variables
    if ((int)$pdo->query("SELECT COUNT(*) FROM inventory_users")->fetchColumn() === 0) {
        $ins = $pdo->prepare("INSERT INTO inventory_users (role, password_hash) VALUES (?, ?)");
        foreach (['admin' => 'INITIAL_ADMIN_PASSWORD', 'user' => 'INITIAL_USER_PASSWORD'] as $role => $var) {
            $pw = getenv($var);
            if ($pw !== false && $pw !== '') {
                $ins->execute([$role, password_hash($pw, PASSWORD_DEFAULT)]);
            }
        }
    }
}

/** Returns 'admin', 'user' or null for a given password. */
function authenticatePassword(PDO $pdo, string $password): ?string {
    ensureUsersTable($pdo);
    $rows = $pdo->query("SELECT role, password_hash FROM inventory_users ORDER BY role = 'admin' DESC, id")->fetchAll();
    foreach ($rows as $row) {
        if (password_verify($password, $row['password_hash'])) {
            return $row['role'];
        }
    }
    return null;
}
