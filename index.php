<?php
/**
 * Al Rish Al Khaleeji - Professional Inventory & Stock Control System
 * Enterprise Modern Edition - Mobile Optimized, Sticky Search & Role Guarded
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';

// --- SESSION AUTHENTICATION & LOGIN SYSTEM ---
$login_error = "";

// Handle Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ./");
    exit;
}

// Handle Login Attempt
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_password'])) {
    $pass = trim($_POST['login_password']);
    $role = authenticatePassword(getDb(), $pass);
    if ($role !== null) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = ($role === 'admin');
        header("Location: ./");
        exit;
    } else {
        $login_error = "Invalid password. Please check your credentials and try again.";
    }
}

// If user is NOT logged in, show modern login screen
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (isset($_GET['live_update'])) { exit; }
    ?>
    <!DOCTYPE html>
    <html lang="en" class="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <title>Al Rish Al Khaleeji - Inventory Control</title>
        <link rel="icon" type="image/png" href="logo.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                            mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace']
                        },
                        colors: {
                            slate: {
                                850: '#172033',
                                950: '#0b0f19'
                            },
                            brand: {
                                50: '#fff7ed',
                                100: '#ffedd5',
                                500: '#f97316',
                                600: '#ea580c',
                                700: '#c2410c'
                            }
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-brand-500 selection:text-white relative overflow-hidden">
        <!-- Background Ambient Glows -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-2xl p-6 sm:p-8 relative z-10">
            <div class="flex flex-col items-center text-center mb-8">
                <div class="w-20 h-20 rounded-2xl bg-white p-1 border border-slate-700/80 shadow-2xl flex items-center justify-center mb-4 overflow-hidden">
                    <img src="logo.png" alt="Al Rish Logo" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <span class="hidden"><?= icon('box', 'w-10 h-10 text-brand-500') ?></span>
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Al Rish Al Khaleeji</h1>
                <p class="text-xs sm:text-sm font-medium text-slate-400 mt-1">Enterprise Inventory & Stock Management</p>
            </div>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="login_password_field" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">System Password</label>
                    <div class="relative">
                        <input type="password" id="login_password_field" name="login_password" required autofocus aria-label="System Password" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-3.5 text-center text-slate-100 text-lg font-mono tracking-widest focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all placeholder:text-slate-600" placeholder="••••••">
                    </div>
                </div>

                <?php if (!empty($login_error)): ?>
                    <div class="p-3 bg-red-950/60 border border-red-800/80 rounded-xl text-red-300 text-xs font-semibold flex items-center gap-2">
                        <?= icon('alert', 'w-4 h-4 shrink-0 text-red-400') ?>
                        <span><?= htmlspecialchars($login_error) ?></span>
                    </div>
                <?php endif; ?>

                <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-600 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-brand-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]">
                    <span>Access Dashboard</span>
                    <?= icon('arrow-right', 'w-4 h-4') ?>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
                <span class="text-xs text-slate-500 font-medium">Secured Internal Operations Terminal</span>
            </div>
        </div>

        <script>
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.onkeydown = function(e) {
                if (e.keyCode == 123) return false;
                if (e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'C'.charCodeAt(0) || e.keyCode == 'J'.charCodeAt(0))) return false;
                if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false;
            }
        </script>
    </body>
    </html>
    <?php
    exit;
}

// -------------------------------------------------------------
// USER IS AUTHENTICATED: INITIALIZE DATABASE & LOGIC
// -------------------------------------------------------------
$pdo = getDb();
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Dynamic Categories & Catalog Fetch
$categories_db = fetchCategories($pdo);
$catalog = fetchCatalog($pdo, $categories_db);
$initial_stocks = fetchAllStocks($pdo, $categories_db, $catalog);
$customers_list = fetchDistinctCustomers($pdo);
$customer_warranties = fetchCustomerMachineWarranties($pdo);

function computeLowStockAlerts(array $stocks, array $categories_db): array {
    $alerts = [];
    $alert_category = ALERT_CATEGORY_SLUG;
    if (isset($stocks[$alert_category])) {
        foreach ($stocks[$alert_category] as $item_name => $stock_data) {
            if ($stock_data['balance'] <= LOW_STOCK_THRESHOLD) {
                $alerts[] = [
                    'category' => $alert_category,
                    'cat_name' => $categories_db[$alert_category]['name'] ?? 'Inks',
                    'item' => $item_name,
                    'balance' => $stock_data['balance']
                ];
            }
        }
    }
    return $alerts;
}

function getSystemMutationVersion(PDO $pdo): string {
    $maxLog = $pdo->query("SELECT COALESCE(MAX(id), 0) FROM inventory_logs")->fetchColumn();
    $countLog = $pdo->query("SELECT COUNT(*) FROM inventory_logs")->fetchColumn();
    $maxLogTime = $pdo->query("SELECT COALESCE(MAX(created_at), '') FROM inventory_logs")->fetchColumn();
    $catCount = $pdo->query("SELECT COUNT(*) FROM inventory_categories")->fetchColumn();
    $itemCount = $pdo->query("SELECT COUNT(*) FROM inventory_items")->fetchColumn();
    
    $cats = $pdo->query("SELECT slug, name, icon FROM inventory_categories ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
    $catStr = '';
    foreach ($cats as $c) {
        $catStr .= $c['slug'] . $c['name'] . ($c['icon'] ?? '') . '|';
    }
    
    return md5("{$maxLog}_{$countLog}_{$maxLogTime}_{$catCount}_{$itemCount}_{$catStr}");
}

// Latest Log ID and Mutation Version tracking for fast event-driven push syncing
$low_stock_alerts = computeLowStockAlerts($initial_stocks, $categories_db);
$max_log_id = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM inventory_logs")->fetchColumn();
$system_version = getSystemMutationVersion($pdo);

// --- POST SUBMISSION DISPATCHER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['logged_in'])) {
    
    $is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    function respondSuccess($msg = 'success') {
        global $is_ajax;
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'msg' => $msg]);
            exit;
        } else {
            header("Location: ./?msg=" . urlencode($msg));
            exit;
        }
    }

    if (isset($_POST['admin_action'])) {
        if (!$is_admin) {
            if ($is_ajax) {
                header('Content-Type: application/json', true, 403);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                exit;
            }
            die("Unauthorized access.");
        }

        $action = $_POST['admin_action'];

        // 1. ADD CATEGORY
        if ($action === 'add_category') {
            $cat_name = trim($_POST['cat_name'] ?? '');
            $cat_icon = trim($_POST['cat_icon'] ?? 'box');
            $is_machine = isset($_POST['is_machine']) ? (int)$_POST['is_machine'] : 1;
            if (empty($cat_icon)) $cat_icon = 'box';

            if (!empty($cat_name)) {
                $base_slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($cat_name)));
                $base_slug = trim($base_slug, '_');
                if (empty($base_slug)) $base_slug = 'cat_' . time();
                
                $slug = $base_slug;
                $counter = 1;
                while (true) {
                    $chk = $pdo->prepare("SELECT COUNT(*) FROM inventory_categories WHERE slug = :slug");
                    $chk->execute([':slug' => $slug]);
                    if ($chk->fetchColumn() == 0) break;
                    $slug = $base_slug . '_' . $counter;
                    $counter++;
                }

                $stmt = $pdo->prepare("INSERT INTO inventory_categories (slug, name, icon, is_machine) VALUES (:slug, :name, :icon, :is_machine)");
                $stmt->execute([
                    ':slug' => $slug,
                    ':name' => $cat_name,
                    ':icon' => $cat_icon,
                    ':is_machine' => $is_machine
                ]);
                respondSuccess('cat_added');
            }
        }

        // 2. EDIT CATEGORY
        if ($action === 'edit_category') {
            $cat_slug = trim($_POST['cat_slug'] ?? '');
            $cat_name = trim($_POST['cat_name'] ?? '');
            $cat_icon = trim($_POST['cat_icon'] ?? 'box');
            $is_machine = isset($_POST['is_machine']) ? (int)$_POST['is_machine'] : 1;
            if (empty($cat_icon)) $cat_icon = 'box';

            if (!empty($cat_slug) && !empty($cat_name)) {
                $stmt = $pdo->prepare("UPDATE inventory_categories SET name = :name, icon = :icon, is_machine = :is_machine WHERE slug = :slug");
                $stmt->execute([
                    ':name' => $cat_name,
                    ':icon' => $cat_icon,
                    ':is_machine' => $is_machine,
                    ':slug' => $cat_slug
                ]);
                respondSuccess('cat_edited');
            }
        }

        // 3. DELETE CATEGORY
        if ($action === 'delete_category') {
            $cat_slug = trim($_POST['cat_slug'] ?? '');
            if (!empty($cat_slug)) {
                $pdo->prepare("DELETE FROM inventory_categories WHERE slug = :slug")->execute([':slug' => $cat_slug]);
                $pdo->prepare("DELETE FROM inventory_items WHERE category = :slug")->execute([':slug' => $cat_slug]);
                respondSuccess('cat_deleted');
            }
        }

        // 4. BULK CATEGORY & ITEM UPDATE
        if ($action === 'bulk_category_update') {
            $cat = $_POST['category'] ?? '';
            $old_names = $_POST['old_name'] ?? [];
            $item_names = $_POST['item_name'] ?? [];
            $item_stocks = $_POST['item_stock'] ?? [];

            $current_items = $pdo->prepare("SELECT item_name FROM inventory_items WHERE category = :cat");
            $current_items->execute([':cat' => $cat]);
            $current_items = $current_items->fetchAll(PDO::FETCH_COLUMN);

            $submitted_old_names = [];
            $now = date('Y-m-d H:i:s');
            $adj_batch_invoice = 'ADJ-' . date('Ymd-His');

            for ($i = 0; $i < count($item_names); $i++) {
                $old = trim($old_names[$i] ?? '');
                $new = trim($item_names[$i] ?? '');
                $qty = intval($item_stocks[$i] ?? 0);

                if (empty($new)) continue;

                if (!empty($old)) {
                    $submitted_old_names[] = $old;
                }

                if (empty($old) && !empty($new)) {
                    $pdo->prepare("INSERT IGNORE INTO inventory_items (category, item_name) VALUES (:cat, :name)")
                        ->execute([':cat' => $cat, ':name' => $new]);

                    $submitted_old_names[] = $new;

                    if ($qty != 0) {
                        $stmtLog = $pdo->prepare("INSERT INTO inventory_logs (category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at) VALUES (:cat, 'SYS-ADJUST', :log_date, :inv, 'Initial Stock', :item, :qty, 0, :created_at)");
                        $stmtLog->execute([
                            ':cat' => $cat,
                            ':log_date' => date('Y-m-d'),
                            ':inv' => $adj_batch_invoice,
                            ':item' => $new,
                            ':qty' => $qty,
                            ':created_at' => $now
                        ]);
                    }
                    continue;
                }

                if (!empty($old) && !empty($new) && $old !== $new) {
                    $pdo->prepare("UPDATE inventory_items SET item_name = :new WHERE item_name = :old AND category = :cat")
                        ->execute([':new' => $new, ':old' => $old, ':cat' => $cat]);

                    $pdo->prepare("UPDATE inventory_logs SET item_desc = :new WHERE item_desc = :old AND category = :cat")
                        ->execute([':new' => $new, ':old' => $old, ':cat' => $cat]);

                    $old = $new;
                }

                if (!empty($old)) {
                    $current_data = getStockData($pdo, $cat, $old);
                    $diff = $qty - $current_data['balance'];
                    if ($diff !== 0) {
                        $stmtLog = $pdo->prepare("INSERT INTO inventory_logs (category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at) VALUES (:cat, 'SYS-ADJUST', :log_date, :inv, 'Inventory Correction', :item, :qty, 0, :created_at)");
                        $stmtLog->execute([
                            ':cat' => $cat,
                            ':log_date' => date('Y-m-d'),
                            ':inv' => $adj_batch_invoice,
                            ':item' => $old,
                            ':qty' => $diff,
                            ':created_at' => $now
                        ]);
                    }
                }
            }

            $items_to_delete = array_diff($current_items, $submitted_old_names);
            foreach ($items_to_delete as $del_item) {
                $current_data = getStockData($pdo, $cat, $del_item);
                $deleted_qty = $current_data['balance'];

                $pdo->prepare("DELETE FROM inventory_items WHERE item_name = :name AND category = :cat")
                    ->execute([':name' => $del_item, ':cat' => $cat]);

                $stmtLogDel = $pdo->prepare("INSERT INTO inventory_logs (category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at) VALUES (:cat, 'SYS-ADJUST', :log_date, :inv, 'Item Deleted', :item, :qty, -1, :created_at)");
                $stmtLogDel->execute([
                    ':cat' => $cat,
                    ':log_date' => date('Y-m-d'),
                    ':inv' => $adj_batch_invoice,
                    ':item' => $del_item,
                    ':qty' => -$deleted_qty,
                    ':created_at' => $now
                ]);
            }

            respondSuccess('bulk_updated');
        }

        if ($action === 'delete') {
            $id = intval($_POST['log_id'] ?? 0);
            $pdo->prepare("DELETE FROM inventory_logs WHERE id = :id")->execute([':id' => $id]);
            respondSuccess('deleted');
        }

        if ($action === 'reset') {
            $pdo->exec("DELETE FROM inventory_logs");
            respondSuccess('reset');
        }

        if ($action === 'edit') {
            $id = intval($_POST['log_id'] ?? 0);
            $type = $_POST['type'] ?? 'SALE';
            $log_date = $_POST['log_date'] ?? date('Y-m-d');
            $invoice = trim($_POST['invoice'] ?? '');
            $client_supplier = trim($_POST['client_supplier'] ?? '');
            $item_desc = trim($_POST['item_desc'] ?? '');
            $quantity = intval($_POST['quantity'] ?? 0);
            $warranty = intval($_POST['warranty'] ?? 0);

            $stmt = $pdo->prepare("UPDATE inventory_logs SET type=:type, log_date=:log_date, invoice=:invoice, client_supplier=:client, item_desc=:item, quantity=:qty, warranty=:warr WHERE id=:id");
            $stmt->execute([
                ':type' => $type,
                ':log_date' => $log_date,
                ':invoice' => $invoice,
                ':client' => $client_supplier,
                ':item' => $item_desc,
                ':qty' => $quantity,
                ':warr' => $warranty,
                ':id' => $id
            ]);

            respondSuccess('edited');
        }
    }

    // 5. FREE WARRANTY CLAIM REPLACEMENT PART STOCK-OUT (Staff and Admin accessible)
    if (isset($_POST['admin_action']) && $_POST['admin_action'] === 'warranty_claim_part' || (isset($_POST['type']) && $_POST['type'] === 'WARRANTY_CLAIM')) {
        $client_name = trim($_POST['client_name'] ?? '');
        $machine_invoice = trim($_POST['machine_invoice'] ?? '');
        $machine_desc = trim($_POST['machine_desc'] ?? '');
        $part_category = trim($_POST['part_category'] ?? '');
        $part_desc = trim($_POST['part_desc'] ?? '');
        $quantity = max(1, intval($_POST['quantity'] ?? 1));
        $claim_notes = trim($_POST['claim_notes'] ?? '');
        $log_date = trim($_POST['log_date'] ?? date('Y-m-d'));
        $now = date('Y-m-d H:i:s');

        $claim_inv = trim($_POST['claim_invoice'] ?? '');
        if (empty($claim_inv)) {
            $claim_inv = 'WAR-' . ($machine_invoice ? $machine_invoice . '-' : '') . date('Ymd') . '-' . rand(1000, 9999);
        }

        if (!empty($client_name) && !empty($part_category) && !empty($part_desc) && $quantity > 0) {
            $stmtClaim = $pdo->prepare("INSERT INTO inventory_logs (category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at) VALUES (:cat, 'WARRANTY', :log_date, :inv, :client, :item, :qty, 0, :created_at)");
            $stmtClaim->execute([
                ':cat' => $part_category,
                ':log_date' => $log_date,
                ':inv' => $claim_inv,
                ':client' => $client_name,
                ':item' => $part_desc,
                ':qty' => $quantity,
                ':created_at' => $now
            ]);
            respondSuccess('warranty_claimed');
        } else {
            if ($is_ajax) {
                header('Content-Type: application/json', true, 400);
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields for warranty claim']);
                exit;
            }
        }
    }

    // POS COUNTER & GENERAL LOGGING
    $type = $_POST['type'] ?? 'SALE';
    $log_date = $_POST['log_date'] ?? date('Y-m-d');
    $invoice = trim($_POST['invoice'] ?? '');
    $client_supplier = trim($_POST['client_supplier'] ?? '');
    $now = date('Y-m-d H:i:s');

    if (isset($_POST['item_desc_arr']) && is_array($_POST['item_desc_arr'])) {
        $count = count($_POST['item_desc_arr']);
        $stmtLog = $pdo->prepare("INSERT INTO inventory_logs (category, type, log_date, invoice, client_supplier, item_desc, quantity, warranty, created_at) VALUES (:cat, :type, :log_date, :inv, :client, :item, :qty, :warr, :created_at)");

        for ($i = 0; $i < $count; $i++) {
            $cat = $_POST['item_category_arr'][$i] ?? '';
            $item = trim($_POST['item_desc_arr'][$i] ?? '');
            $qty = intval($_POST['item_qty_arr'][$i] ?? 0);
            $warr = intval($_POST['item_warranty_arr'][$i] ?? 0);
            $item_type = $type;

            if (strpos($item, 'MACHINE_UNIT|') === 0) {
                $mParts = explode('|', $item);
                $cat = $mParts[1] ?? $cat;
                $item = '[Machine Unit] ' . ($mParts[2] ?? $cat);
            }

            if ($item_type === 'SALE' && $warr > 0) {
                $item_type = 'WARRANTY';
            }

            if (!empty($cat) && !empty($item) && $qty > 0) {
                $stmtLog->execute([
                    ':cat' => $cat,
                    ':type' => $item_type,
                    ':log_date' => $log_date,
                    ':inv' => $invoice,
                    ':client' => $client_supplier,
                    ':item' => $item,
                    ':qty' => $qty,
                    ':warr' => $warr,
                    ':created_at' => $now
                ]);
            }
        }

        if (!empty($_POST['freebie_data']) && is_array($_POST['freebie_data'])) {
            $freebie_count = count($_POST['freebie_data']);
            for ($i = 0; $i < $freebie_count; $i++) {
                $f_data = $_POST['freebie_data'][$i];
                if (!empty($f_data)) {
                    $f_qty = isset($_POST['freebie_qty'][$i]) ? intval($_POST['freebie_qty'][$i]) : 1;
                    $parts = explode('|', $f_data);
                    if (count($parts) === 2 && $f_qty > 0) {
                        $stmtLog->execute([
                            ':cat' => $parts[0],
                            ':type' => 'FREE',
                            ':log_date' => $log_date,
                            ':inv' => $invoice,
                            ':client' => $client_supplier,
                            ':item' => $parts[1],
                            ':qty' => $f_qty,
                            ':warr' => 0,
                            ':created_at' => $now
                        ]);
                    }
                }
            }
        }

        respondSuccess('success');
    }
}

// -------------------------------------------------------------
// RENDER TEMPLATES FOR HTML & LIVE AJAX POLLING
// -------------------------------------------------------------

function renderLedgerTableBody(array $grouped_logs, array $categories_db, bool $is_admin = false): string {
    ob_start();
    foreach ($grouped_logs as $inv_key => $group): 
        $group_id = 'group-' . md5($inv_key);
        $display_timestamp = !empty($group['created_at']) ? date('h:i:s A', strtotime($group['created_at'])) : '--';
        $total_qty = 0;
        foreach ($group['items'] as $item_calc) {
            $total_qty += $item_calc['quantity'];
        }

        $badgeClass = '';
        $typeLabel = $group['type'];
        if ($group['type'] === 'ORDER') {
            $badgeClass = 'bg-emerald-950/80 text-emerald-400 border-emerald-800/80';
            $typeLabel = 'STOCK IN (ORDER)';
        } elseif ($group['type'] === 'SALE') {
            $badgeClass = 'bg-brand-950/80 text-brand-400 border-brand-800/80';
            $typeLabel = 'STOCK OUT (SALE)';
        } elseif ($group['type'] === 'WARRANTY') {
            $badgeClass = 'bg-blue-950/80 text-blue-400 border-blue-800/80';
            $typeLabel = 'WARRANTY';
        } elseif ($group['type'] === 'FREE') {
            $badgeClass = 'bg-purple-950/80 text-purple-400 border-purple-800/80';
            $typeLabel = 'FREE GIFT';
        } else {
            $badgeClass = 'bg-slate-800 text-slate-300 border-slate-700';
            $typeLabel = 'ADJUSTMENT';
        }
    ?>
    <div class="ledger-parent-container border-b border-slate-800/80 transition-colors" data-group-id="<?= $group_id ?>" data-group-type="<?= $group['type'] ?>" data-invoice="<?= htmlspecialchars($group['invoice']) ?>" data-client="<?= htmlspecialchars($group['client_supplier']) ?>" data-date="<?= htmlspecialchars($group['log_date']) ?>">
        <!-- Collapsible Header -->
        <div class="p-3.5 sm:p-4 bg-slate-900/90 hover:bg-slate-850 cursor-pointer flex flex-col md:flex-row justify-between items-start md:items-center gap-2 select-none" onclick="toggleLedgerGroup('<?= $group_id ?>')">
            <div class="flex items-center gap-2.5 sm:gap-3 w-full md:w-auto">
                <span id="icon-<?= $group_id ?>" class="text-slate-500 transition-transform duration-200">
                    <?= icon('chevron-right', 'w-4 h-4') ?>
                </span>
                <span class="px-2.5 py-0.5 rounded-lg text-[10px] sm:text-xs font-bold border <?= $badgeClass ?> uppercase tracking-wider">
                    <?= $typeLabel ?>
                </span>
                <div class="flex items-center gap-2 font-mono text-xs sm:text-sm font-bold text-white">
                    <span><?= htmlspecialchars($group['invoice']) ?></span>
                </div>
            </div>

            <div class="flex items-center justify-between w-full md:w-auto gap-4 text-xs pl-7 md:pl-0">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <?= icon('user', 'w-3.5 h-3.5 text-slate-500') ?>
                    <span class="font-semibold text-slate-200"><?= htmlspecialchars($group['client_supplier']) ?></span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-mono hidden sm:inline"><?= htmlspecialchars($group['log_date']) ?></span>
                    <span class="text-[11px] text-slate-500 font-mono hidden lg:inline"><?= $display_timestamp ?></span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-950 text-slate-300 font-bold border border-slate-800 text-[11px]">
                        <?= count($group['items']) ?> lines (<?= $total_qty ?> units)
                    </span>
                </div>
            </div>
        </div>

        <!-- Child Items Grid -->
        <div id="<?= $group_id ?>" class="hidden bg-slate-950/60 p-2 sm:p-4 border-t border-slate-800/60">
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <div class="hidden md:grid grid-cols-12 gap-2 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/80 px-3">
                        <div class="col-span-1">ID</div>
                        <div class="col-span-2">Category</div>
                        <div class="col-span-2">Type</div>
                        <div class="<?= $is_admin ? 'col-span-4' : 'col-span-5' ?>">Product Line / Description</div>
                        <div class="col-span-1 text-center">Qty</div>
                        <div class="col-span-1 text-center">Warranty</div>
                        <?php if ($is_admin): ?>
                            <div class="col-span-1 text-right">Actions</div>
                        <?php endif; ?>
                    </div>

                    <div class="divide-y divide-slate-800/40">
                        <?php foreach($group['items'] as $item): 
                            $catIcon = $categories_db[$item['category']]['icon'] ?? 'box';
                            $catIconSvg = getCategoryIcon($catIcon, 'w-3.5 h-3.5 text-brand-500 shrink-0');
                            $catNameFormatted = $categories_db[$item['category']]['name'] ?? ucfirst($item['category']);
                            $warrDisplay = ($item['warranty'] > 0) ? $item['warranty'] . ' Mos' : '--';
                            
                            $itemBadge = 'bg-slate-800 text-slate-300';
                            if ($item['type'] === 'ORDER') $itemBadge = 'bg-emerald-950 text-emerald-400 border border-emerald-800/60';
                            if ($item['type'] === 'SALE') $itemBadge = 'bg-brand-950 text-brand-400 border border-brand-800/60';
                            if ($item['type'] === 'WARRANTY') $itemBadge = 'bg-blue-950 text-blue-400 border border-blue-800/60';
                            if ($item['type'] === 'FREE') $itemBadge = 'bg-purple-950 text-purple-400 border border-purple-800/60';
                        ?>
                        <div class="ledger-item-row p-3 md:py-2 md:px-3 rounded-xl md:rounded-none flex flex-col md:grid md:grid-cols-12 gap-1.5 md:gap-2 items-stretch md:items-center text-xs hover:bg-slate-900/60 transition-colors"
                            data-id="<?= $item['id'] ?>"
                            data-date="<?= $item['log_date'] ?>"
                            data-category="<?= $item['category'] ?>"
                            data-cat-name="<?= htmlspecialchars($catNameFormatted) ?>"
                            data-type="<?= $item['type'] ?>"
                            data-invoice="<?= htmlspecialchars($item['invoice']) ?>"
                            data-item="<?= htmlspecialchars($item['item_desc']) ?>"
                            data-qty="<?= $item['quantity'] ?>"
                            data-client="<?= htmlspecialchars($item['client_supplier']) ?>"
                            data-warranty="<?= $item['warranty'] ?>">

                            <div class="md:col-span-1 font-mono text-slate-500 text-xs flex justify-between md:block">
                                <span class="md:hidden text-[10px] font-bold uppercase text-slate-500">Sys ID</span>
                                #<?= $item['id'] ?>
                            </div>

                            <div class="md:col-span-2 flex items-center justify-between md:justify-start gap-1.5 text-slate-300 truncate">
                                <span class="md:hidden text-[10px] font-bold uppercase text-slate-500">Category</span>
                                <div class="flex items-center gap-1.5 truncate">
                                    <?= $catIconSvg ?>
                                    <span class="truncate font-medium"><?= htmlspecialchars($catNameFormatted) ?></span>
                                </div>
                            </div>

                            <div class="md:col-span-2 flex justify-between md:justify-start items-center">
                                <span class="md:hidden text-[10px] font-bold uppercase text-slate-500">Type</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $itemBadge ?> uppercase"><?= $item['type'] ?></span>
                            </div>

                            <div class="<?= $is_admin ? 'md:col-span-4' : 'md:col-span-5' ?> font-medium text-slate-100 flex justify-between md:block">
                                <span class="md:hidden text-[10px] font-bold uppercase text-slate-500">Description</span>
                                <span class="truncate"><?= htmlspecialchars($item['item_desc']) ?></span>
                            </div>

                            <div class="md:col-span-1 flex justify-between md:justify-center items-center">
                                <span class="md:hidden text-[10px] font-bold uppercase text-slate-500">Quantity</span>
                                <span class="font-bold text-white bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800 font-mono"><?= $item['quantity'] ?></span>
                            </div>

                            <div class="md:col-span-1 flex justify-between md:justify-center items-center">
                                <span class="md:hidden text-[10px] font-bold uppercase text-slate-500">Warranty</span>
                                <?= $warrDisplay ?>
                            </div>

                            <?php if ($is_admin): ?>
                            <div class="md:col-span-1 flex justify-end gap-2 pt-2 md:pt-0 border-t border-slate-800/80 md:border-none">
                                <button type="button" onclick="openEditModal(<?= $item['id'] ?>, '<?= $item['type'] ?>', '<?= $item['log_date'] ?>', '<?= addslashes($item['invoice']) ?>', '<?= addslashes($item['client_supplier']) ?>', '<?= addslashes($item['item_desc']) ?>', <?= $item['quantity'] ?>, <?= $item['warranty'] ?>)" aria-label="Edit Record" class="p-2 rounded-xl bg-blue-950/60 hover:bg-blue-900/60 text-blue-400 border border-blue-800/60 transition-colors cursor-pointer" title="Edit Record">
                                    <?= icon('edit', 'w-3.5 h-3.5') ?>
                                </button>
                                <form method="POST" onsubmit="if(!confirm('Are you sure you want to delete this record permanently?')) return false; return handleAsyncDeleteSubmit(event, this);" class="inline">
                                    <input type="hidden" name="admin_action" value="delete">
                                    <input type="hidden" name="log_id" value="<?= $item['id'] ?>">
                                    <button type="submit" aria-label="Delete Record" class="p-2 rounded-xl bg-red-950/60 hover:bg-red-900 border border-red-800/80 text-red-400 transition-colors cursor-pointer" title="Delete Record">
                                        <?= icon('trash', 'w-3.5 h-3.5') ?>
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
    endforeach;
    return ob_get_clean();
}

function renderDashboardCards(array $categories_db, array $catalog, PDO $pdo, bool $is_admin = false): string {
    ob_start();
    ?>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6" id="dashboard-categories-grid">
        <?php foreach ($categories_db as $slug => $cat_info): 
            $catIconSvg = getCategoryIcon($cat_info['icon'] ?? 'box', 'w-5 h-5 text-brand-500 shrink-0');
        ?>
        <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-xl overflow-hidden dashboard-category-card" data-cat="<?= $slug ?>">
            <!-- Header Bar (Wrap-Friendly) -->
            <div class="flex justify-between items-center cursor-pointer bg-slate-850 hover:bg-slate-800/80 p-4 sm:p-5 transition-colors select-none gap-2" onclick="toggleDashboardCategory('<?= $slug ?>')">
                <div class="flex items-center gap-3 min-w-0 flex-1 pr-2">
                    <div class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center p-2 shadow-inner shrink-0">
                        <?= $catIconSvg ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xs sm:text-base font-bold text-white m-0 break-words leading-tight"><?= htmlspecialchars($cat_info['name']) ?></h3>
                        <span class="text-[10px] sm:text-xs text-slate-400 block mt-0.5"><?= count($catalog[$slug] ?? []) ?> product lines</span>
                    </div>
                </div>
                <span id="dash-icon-<?= $slug ?>" class="text-slate-500 transition-transform duration-200 shrink-0 ml-1">
                    <?= icon('chevron-down', 'w-5 h-5') ?>
                </span>
            </div>
            
            <!-- Items Table -->
            <div id="dash-content-<?= $slug ?>" class="dash-content border-t border-slate-800/80 p-3 sm:p-4 bg-slate-950/40">
                <div class="hidden md:grid grid-cols-12 gap-3 pb-2.5 px-2 border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <div class="col-span-6">Product Line</div>
                    <div class="col-span-2 text-center text-emerald-400">Total In</div>
                    <div class="col-span-2 text-center text-brand-400">Total Out</div>
                    <div class="col-span-2 text-right">On Hand</div>
                </div>

                <div class="divide-y divide-slate-800/50">
                    <?php 
                    $items = $catalog[$slug] ?? [];
                    foreach ($items as $item): 
                        $data = getStockData($pdo, $slug, $item);
                        $balanceClass = $data['balance'] <= 0 ? 'text-red-400 bg-red-950/60 border-red-800/60' : ($data['balance'] <= 5 ? 'text-yellow-400 bg-yellow-950/60 border-yellow-800/60' : 'text-emerald-400 bg-emerald-950/60 border-emerald-800/60');
                        $encodedItem = htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="dashboard-item-row p-3 md:py-2.5 md:px-2 rounded-2xl md:rounded-none flex flex-col md:grid md:grid-cols-12 gap-2 md:gap-3 items-stretch md:items-center hover:bg-slate-800/30 transition-colors">
                        <div class="md:col-span-6 font-semibold text-slate-200 text-xs sm:text-sm item-name break-words" title="<?= htmlspecialchars($item) ?>">
                            <?php if ($is_admin): ?>
                                <button type="button" onclick="editProductLineFromDashboard('<?= $slug ?>', <?= $encodedItem ?>)" class="text-left font-semibold text-slate-200 hover:text-brand-400 transition-colors inline-flex items-center gap-1.5 group cursor-pointer" title="Click to edit in Catalog Admin">
                                    <span class="group-hover:underline underline-offset-2"><?= htmlspecialchars($item) ?></span>
                                    <span class="opacity-0 group-hover:opacity-100 text-brand-400 transition-opacity shrink-0">
                                        <?= icon('edit', 'w-3.5 h-3.5') ?>
                                    </span>
                                </button>
                            <?php else: ?>
                                <span><?= htmlspecialchars($item) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center justify-between md:contents gap-2 pt-1 md:pt-0 border-t border-slate-800/50 md:border-none">
                            <div class="md:col-span-2 flex items-center gap-1.5 md:justify-center">
                                <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">In:</span>
                                <span class="font-mono font-medium text-emerald-400 text-xs md:text-sm live-in bg-slate-950 md:bg-transparent px-2 py-0.5 rounded border border-slate-800 md:border-none" data-category="<?= $slug ?>" data-item="<?= htmlspecialchars($item) ?>"><?= $data['in'] ?></span>
                            </div>
                            <div class="md:col-span-2 flex items-center gap-1.5 md:justify-center">
                                <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Out:</span>
                                <span class="font-mono font-medium text-brand-400 text-xs md:text-sm live-out bg-slate-950 md:bg-transparent px-2 py-0.5 rounded border border-slate-800 md:border-none" data-category="<?= $slug ?>" data-item="<?= htmlspecialchars($item) ?>"><?= $data['out'] ?></span>
                            </div>
                            <div class="md:col-span-2 flex items-center gap-1.5 md:justify-end">
                                <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Stock:</span>
                                <span class="font-mono font-bold text-xs sm:text-sm px-2.5 py-0.5 rounded-lg border <?= $balanceClass ?> live-balance" data-category="<?= $slug ?>" data-item="<?= htmlspecialchars($item) ?>"><?= $data['balance'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

function renderWarrantySection(PDO $pdo): string {
    $customerGroups = fetchCustomerMachineWarranties($pdo);
    $rawWarranties = fetchWarrantyRecords($pdo);
    $activeCount = count(array_filter($rawWarranties, fn($w) => $w['is_active']));
    ob_start();
    ?>
    <div class="space-y-4">
        <!-- Warranty Header & View Switcher -->
        <div class="bg-slate-900/90 p-3 sm:p-4 rounded-2xl border border-slate-800 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Warranty View:</span>
                <div class="flex bg-slate-950 p-1 rounded-xl border border-slate-800">
                    <button type="button" id="btn-warr-view-clients" onclick="switchWarrantyView('clients')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white shadow-md transition-all cursor-pointer">
                        <?= icon('user', 'w-3.5 h-3.5 inline mr-1') ?> Client Machine Fleet
                    </button>
                    <button type="button" id="btn-warr-view-table" onclick="switchWarrantyView('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-white transition-all cursor-pointer">
                        <?= icon('list', 'w-3.5 h-3.5 inline mr-1') ?> All Warranties Ledger
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="text-slate-400 font-semibold">Total Clients: <strong class="text-white"><?= count($customerGroups) ?></strong></span>
                <span class="text-slate-700">•</span>
                <span class="text-slate-400 font-semibold">Active Warranties: <strong class="text-emerald-400 font-mono"><?= $activeCount ?></strong></span>
            </div>
        </div>

        <!-- 1. Collapsible Accordions Grouped by Client (Default View) -->
        <div id="warranty-clients-view" class="space-y-3">
            <?php if (empty($customerGroups)): ?>
                <div class="bg-slate-900 p-12 text-center text-slate-500 rounded-3xl border border-slate-800 shadow-xl">
                    <?= icon('shield', 'w-12 h-12 mx-auto text-slate-700 mb-3') ?>
                    <h3 class="text-base font-bold text-white mb-1">No Machine Warranties Recorded</h3>
                    <p class="text-xs text-slate-400">When you sell machines to clients in the POS Counter with warranty months, they will automatically appear here grouped by client.</p>
                </div>
            <?php else: ?>
                <?php foreach ($customerGroups as $client => $group): 
                    $cid = md5($client);
                    $hasActive = ($group['active_warranties'] > 0);
                    $badgeClass = $hasActive ? 'bg-emerald-950/80 text-emerald-400 border-emerald-800/80' : 'bg-red-950/80 text-red-400 border-red-800/80';
                ?>
                <div class="warranty-client-card bg-slate-900 rounded-3xl border border-slate-800 shadow-xl overflow-hidden transition-all" data-client="<?= htmlspecialchars(strtolower($client)) ?>">
                    <!-- Collapsible Header -->
                    <div class="p-4 sm:p-5 bg-slate-850 hover:bg-slate-800/90 cursor-pointer flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 select-none transition-colors" onclick="toggleWarrantyClient('<?= $cid ?>')">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span id="warr-icon-<?= $cid ?>" class="text-slate-500 transition-transform duration-200 shrink-0">
                                <?= icon('chevron-down', 'w-5 h-5') ?>
                            </span>
                            <div class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center p-2 text-brand-500 shrink-0 shadow-inner">
                                <?= icon('user', 'w-5 h-5') ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm sm:text-base font-extrabold text-white truncate"><?= htmlspecialchars($client) ?></h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] text-slate-400 font-medium"><?= $group['total_machines'] ?> <?= $group['total_machines'] === 1 ? 'Machine' : 'Machines' ?> Purchased</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 self-end sm:self-auto shrink-0">
                            <span class="px-3 py-1 rounded-xl text-xs font-bold border <?= $badgeClass ?> uppercase tracking-wider flex items-center gap-1.5">
                                <?= $hasActive ? icon('check', 'w-3.5 h-3.5') : icon('close', 'w-3.5 h-3.5') ?>
                                <?= $group['active_warranties'] ?> Active <?= $group['active_warranties'] === 1 ? 'Warranty' : 'Warranties' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Collapsible Body: List of Bought Machines -->
                    <div id="warr-body-<?= $cid ?>" class="warr-client-content border-t border-slate-800/80 p-3 sm:p-5 bg-slate-950/50 space-y-3">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2 pb-1 border-b border-slate-800/60">
                            <?= icon('box', 'w-4 h-4 text-brand-500') ?>
                            <span>Purchased Machine Fleet & Active Guarantees</span>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-3 sm:gap-4">
                            <?php foreach ($group['machines'] as $m): 
                                $isMachineActive = $m['is_active'];
                                $mStatusClass = $isMachineActive ? 'bg-emerald-950/80 text-emerald-400 border-emerald-800/80' : 'bg-red-950/80 text-red-400 border-red-800/80';
                                $mCountdown = $isMachineActive ? ($m['days_left'] . ' days remaining') : 'Expired';
                            ?>
                            <div class="warranty-machine-card p-4 rounded-2xl bg-slate-900 border <?= $isMachineActive ? 'border-blue-900/60 shadow-lg shadow-blue-950/30' : 'border-slate-800' ?> flex flex-col justify-between space-y-3.5" data-machine="<?= htmlspecialchars(strtolower($m['item_desc'])) ?>" data-invoice="<?= htmlspecialchars(strtolower($m['invoice'])) ?>">
                                <div>
                                    <div class="flex justify-between items-start gap-2 mb-2">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <div class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-brand-500 shrink-0">
                                                <?= icon('box', 'w-4 h-4') ?>
                                            </div>
                                            <h4 class="font-bold text-white text-xs sm:text-sm truncate" title="<?= htmlspecialchars($m['item_desc']) ?>"><?= htmlspecialchars($m['item_desc']) ?></h4>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-lg border <?= $mStatusClass ?> text-[10px] font-bold uppercase tracking-wider shrink-0">
                                            <?= $isMachineActive ? 'Active' : 'Expired' ?>
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-950/80 p-3 rounded-xl border border-slate-800/80">
                                        <div>
                                            <span class="text-[10px] text-slate-500 uppercase font-bold block">Invoice Ref</span>
                                            <span class="font-mono text-slate-200 font-semibold"><?= htmlspecialchars($m['invoice']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-500 uppercase font-bold block">Purchased</span>
                                            <span class="font-mono text-slate-300"><?= htmlspecialchars($m['log_date']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-500 uppercase font-bold block">Warranty Duration</span>
                                            <span class="text-slate-200 font-bold"><?= $m['warranty'] ?> Months</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-500 uppercase font-bold block">Expiry Date</span>
                                            <span class="font-mono font-bold <?= $isMachineActive ? 'text-emerald-400' : 'text-red-400' ?>"><?= htmlspecialchars($m['expiry_date']) ?></span>
                                        </div>
                                    </div>

                                    <div class="mt-2 flex items-center justify-between text-[11px] px-1">
                                        <span class="text-slate-400">Coverage Status:</span>
                                        <span class="font-bold <?= $isMachineActive ? 'text-emerald-400' : 'text-slate-500' ?>"><?= $mCountdown ?></span>
                                    </div>
                                </div>

                                <!-- Action Button: Order Parts Under Warranty -->
                                <div>
                                    <?php if ($isMachineActive): ?>
                                        <button type="button" onclick="openWarrantyClaimModal('<?= addslashes($client) ?>', '<?= addslashes($m['item_desc']) ?>', '<?= addslashes($m['invoice']) ?>', '<?= addslashes($m['expiry_date']) ?>', '<?= addslashes($m['category']) ?>')" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-blue-600/20 text-xs sm:text-sm flex items-center justify-center gap-2 transition-all cursor-pointer active:scale-[0.98]">
                                            <?= icon('cart', 'w-4 h-4') ?>
                                            <span>Order Parts Under Warranty</span>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" disabled class="w-full bg-slate-950 text-slate-600 border border-slate-800 font-semibold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 cursor-not-allowed">
                                            <?= icon('lock', 'w-4 h-4') ?>
                                            <span>Warranty Coverage Expired</span>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Claimed Parts History -->
                                <?php if (!empty($m['claims'])): ?>
                                <div class="pt-2 border-t border-slate-800/80">
                                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider block mb-1.5 flex items-center gap-1">
                                        <?= icon('history', 'w-3 h-3') ?> Claimed Parts History (<?= count($m['claims']) ?>):
                                    </span>
                                    <div class="space-y-1.5 max-h-32 overflow-y-auto custom-scrollbar pr-1">
                                        <?php foreach ($m['claims'] as $claim): ?>
                                            <div class="p-2 rounded-xl bg-slate-950 border border-slate-800/80 text-xs flex justify-between items-center gap-2">
                                                <div class="truncate">
                                                    <span class="font-bold text-white block truncate"><?= htmlspecialchars($claim['item_desc']) ?></span>
                                                    <span class="text-[10px] text-slate-400 font-mono">Qty: <?= $claim['quantity'] ?> • <?= htmlspecialchars($claim['log_date']) ?></span>
                                                </div>
                                                <span class="text-[10px] px-2 py-0.5 rounded bg-blue-950 text-blue-400 border border-blue-800/60 font-mono shrink-0"><?= htmlspecialchars($claim['invoice']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 2. Master Warranties Table View (Toggled) -->
        <div id="warranty-table-view" class="hidden bg-slate-900 rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <div class="hidden md:grid grid-cols-12 gap-3 p-4 border-b border-slate-800 bg-slate-950 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <div class="col-span-2">Invoice #</div>
                    <div class="col-span-2">Client / Customer</div>
                    <div class="col-span-3">Machine Model</div>
                    <div class="col-span-2 text-center">Purchase Date</div>
                    <div class="col-span-1 text-center">Duration</div>
                    <div class="col-span-1 text-center">Expiry Date</div>
                    <div class="col-span-1 text-right">Action</div>
                </div>
                <div id="warranty-tbody" class="divide-y divide-slate-800/80 p-2 md:p-0">
                    <?php foreach($rawWarranties as $w): ?>
                    <div class="warranty-item-row p-4 md:py-3.5 md:px-4 hover:bg-slate-850/40 transition-colors grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-3 items-center text-xs md:text-sm">
                        <div class="md:col-span-2 flex justify-between md:block items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Invoice</span>
                            <span class="font-mono text-slate-300 font-semibold"><?= htmlspecialchars($w['invoice']) ?></span>
                        </div>
                        <div class="md:col-span-2 flex justify-between md:block items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Client</span>
                            <span class="font-bold text-white"><?= htmlspecialchars($w['client_supplier']) ?></span>
                        </div>
                        <div class="md:col-span-3 flex justify-between md:block items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Item</span>
                            <span class="text-brand-400 font-medium truncate" title="<?= htmlspecialchars($w['item_desc']) ?>"><?= htmlspecialchars($w['item_desc']) ?></span>
                        </div>
                        <div class="md:col-span-2 flex justify-between md:justify-center items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Date</span>
                            <span class="text-slate-400 font-mono"><?= htmlspecialchars($w['log_date']) ?></span>
                        </div>
                        <div class="md:col-span-1 flex justify-between md:justify-center items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Duration</span>
                            <span class="text-slate-300"><?= $w['warranty'] ?> Mos</span>
                        </div>
                        <div class="md:col-span-1 flex justify-between md:justify-center items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Expiry</span>
                            <span class="font-mono font-bold <?= $w['is_active'] ? 'text-emerald-400' : 'text-red-400' ?>"><?= htmlspecialchars($w['expiry_date']) ?></span>
                        </div>
                        <div class="md:col-span-1 flex justify-between md:justify-end items-center">
                            <span class="text-[10px] uppercase font-bold text-slate-500 md:hidden">Action</span>
                            <?php if ($w['is_active']): ?>
                                <button type="button" onclick="openWarrantyClaimModal('<?= addslashes($w['client_supplier']) ?>', '<?= addslashes($w['item_desc']) ?>', '<?= addslashes($w['invoice']) ?>', '<?= addslashes($w['expiry_date']) ?>', '<?= addslashes($w['category']) ?>')" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-500 text-white text-[11px] rounded-lg font-bold shadow transition-colors cursor-pointer">
                                    Order Parts
                                </button>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-red-950/80 text-red-400 border border-red-800/80 text-[10px] rounded-lg font-bold">
                                    Expired
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function renderAdminCategoryCards(array $categories_db, array $catalog, PDO $pdo): string {
    ob_start();
    ?>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 mb-8" id="admin-category-cards-grid">
        <?php foreach($categories_db as $slug => $cat_info): 
            $catIconSvg = getCategoryIcon($cat_info['icon'] ?? 'box', 'w-5 h-5 text-brand-500 shrink-0');
        ?>
        <div class="bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-800 shadow-xl admin-category-card" data-cat="<?= $slug ?>">
            <div class="flex justify-between items-center mb-4 border-b border-slate-800 pb-3 gap-2">
                <div class="flex items-center gap-2.5 min-w-0 flex-1 pr-1">
                    <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-1.5 shadow-inner shrink-0">
                        <?= $catIconSvg ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xs sm:text-base font-bold text-white admin-cat-title break-words leading-tight"><?= htmlspecialchars($cat_info['name']) ?></h3>
                    </div>
                </div>
                <div class="flex gap-1.5 shrink-0">
                    <button type="button" onclick="openEditCategoryModal('<?= $slug ?>', '<?= addslashes($cat_info['name']) ?>', '<?= addslashes($cat_info['icon'] ?? 'box') ?>')" aria-label="Edit Category" class="p-2 sm:px-3 sm:py-1.5 text-xs bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 rounded-xl transition-colors flex items-center gap-1 font-semibold cursor-pointer">
                        <?= icon('edit', 'w-3.5 h-3.5') ?> <span class="hidden sm:inline">Edit</span>
                    </button>
                    <form method="POST" onsubmit="return handleAsyncFormSubmit(event, this)" class="inline">
                        <input type="hidden" name="admin_action" value="delete_category">
                        <input type="hidden" name="cat_slug" value="<?= $slug ?>">
                        <button type="submit" onclick="return confirm('Permanently delete this category and all its catalog items?')" aria-label="Delete Category" class="p-2 sm:px-3 sm:py-1.5 text-xs bg-red-950/60 hover:bg-red-900 border border-red-800/80 text-red-300 rounded-xl transition-colors flex items-center gap-1 font-semibold cursor-pointer">
                            <?= icon('trash', 'w-3.5 h-3.5') ?> <span class="hidden sm:inline">Delete</span>
                        </button>
                    </form>
                </div>
            </div>

            <form id="admin-form-<?= $slug ?>" method="POST" onsubmit="return handleAsyncFormSubmit(event, this)">
                <input type="hidden" name="admin_action" value="bulk_category_update">
                <input type="hidden" name="category" value="<?= $slug ?>">

                <div class="max-h-[350px] overflow-y-auto custom-scrollbar pr-1">
                    <div class="hidden md:grid grid-cols-12 gap-3 pb-2 border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider sticky top-0 bg-slate-900 z-10">
                        <div class="col-span-7">Product Line Name</div>
                        <div class="col-span-3 text-center">Stock Balance</div>
                        <div class="col-span-2 text-right pr-2">Remove</div>
                    </div>
                    <div id="admin-tbody-<?= $slug ?>" class="divide-y divide-slate-800/60 pt-1">
                        <?php 
                        $items = $catalog[$slug] ?? [];
                        foreach ($items as $item): 
                            $data = getStockData($pdo, $slug, $item);
                        ?>
                        <div class="admin-item-row p-2.5 md:px-0 grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-3 items-center hover:bg-slate-800/20 transition-colors">
                            <div class="md:col-span-7">
                                <input type="hidden" name="old_name[]" value="<?= htmlspecialchars($item) ?>">
                                <input type="text" name="item_name[]" value="<?= htmlspecialchars($item) ?>" oninput="markAdminFormDirty('<?= $slug ?>')" aria-label="Product Line Name" class="w-full bg-slate-950 border border-slate-700/80 focus:border-brand-500 rounded-xl px-3 py-2 text-xs sm:text-sm text-white outline-none transition-all admin-item-input" required>
                            </div>
                            <div class="md:col-span-3 flex justify-between md:justify-center items-center">
                                <span class="text-xs text-slate-500 font-bold md:hidden">Stock:</span>
                                <input type="number" name="item_stock[]" value="<?= $data['balance'] ?>" min="0" oninput="markAdminFormDirty('<?= $slug ?>')" aria-label="Stock Balance" class="w-24 bg-slate-950 border border-slate-700/80 rounded-xl px-2 py-1.5 text-center text-xs sm:text-sm font-bold text-white focus:outline-none focus:border-brand-500 font-mono" required>
                            </div>
                            <div class="md:col-span-2 flex justify-end">
                                <button type="button" onclick="this.closest('.admin-item-row').remove(); markAdminFormDirty('<?= $slug ?>');" aria-label="Remove Product Line" class="p-2 text-slate-500 hover:text-red-400 transition-colors cursor-pointer">
                                    <?= icon('trash', 'w-4 h-4') ?>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-800 flex justify-between items-center gap-2">
                    <button type="button" onclick="addAdminRow('<?= $slug ?>')" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-bold px-3.5 py-2.5 rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer">
                        <?= icon('plus', 'w-3.5 h-3.5') ?> Add Line
                    </button>
                    <div id="admin-actions-<?= $slug ?>" class="hidden flex gap-2">
                        <button type="button" onclick="revertAdminForm('<?= $slug ?>')" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold px-3 py-2 rounded-xl transition-colors cursor-pointer">Revert</button>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-lg shadow-brand-600/20 transition-colors flex items-center gap-1.5 cursor-pointer">
                            <?= icon('save', 'w-3.5 h-3.5') ?> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

function renderAlerts(array $low_stock_alerts): string {
    ob_start();
    if (count($low_stock_alerts) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <?php foreach ($low_stock_alerts as $alert): ?>
                <div class="bg-slate-900 border-l-4 border-red-500 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-800 shadow-xl flex justify-between items-center gap-3">
                    <div class="truncate">
                        <h4 class="font-bold text-white text-sm sm:text-base truncate"><?= htmlspecialchars($alert['item']) ?></h4>
                        <span class="text-xs text-slate-400"><?= htmlspecialchars($alert['cat_name']) ?></span>
                    </div>
                    <span class="bg-red-950/80 text-red-400 border border-red-800/80 font-mono font-bold text-xs sm:text-sm px-3 py-1 rounded-xl shrink-0">
                        <?= $alert['balance'] ?> left
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-slate-900 p-8 sm:p-12 rounded-3xl text-center text-slate-400 border border-slate-800 shadow-xl">
            <div class="w-12 h-12 mx-auto rounded-full bg-emerald-950/80 text-emerald-400 border border-emerald-800/80 flex items-center justify-center mb-3">
                <?= icon('check', 'w-6 h-6') ?>
            </div>
            <h3 class="font-bold text-white text-base mb-1">All Stock Levels Optimal</h3>
            <p class="text-xs text-slate-500">No critical low-stock alerts detected for tracked product lines.</p>
        </div>
    <?php endif;
    return ob_get_clean();
}

// -------------------------------------------------------------
// REAL-TIME SERVER-SENT EVENTS (SSE) STREAM (EVENT-DRIVEN PUSH)
// -------------------------------------------------------------
if (isset($_GET['events'])) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        http_response_code(403);
        exit;
    }

    // Crucial: Release session lock so concurrent POST/AJAX requests are never blocked
    session_write_close();

    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no');
    header('Access-Control-Allow-Origin: *');

    set_time_limit(60);
    ignore_user_abort(true);

    $last_seen_version = $_GET['version'] ?? '';
    $last_seen_id = isset($_GET['last_log_id']) ? (int)$_GET['last_log_id'] : 0;

    $current_version = getSystemMutationVersion($pdo);
    $current_max_id = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM inventory_logs")->fetchColumn();

    echo "event: connected\n";
    echo "data: " . json_encode([
        'status' => 'connected',
        'version' => $current_version,
        'latest_log_id' => $current_max_id,
        'server_time' => time()
    ]) . "\n\n";
    @flush();

    $start_time = time();
    $max_duration = 25;
    $last_ping = time();

    while ((time() - $start_time) < $max_duration) {
        if (connection_aborted()) {
            break;
        }

        $check_version = getSystemMutationVersion($pdo);
        $check_max_id = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM inventory_logs")->fetchColumn();

        if ($check_version !== $last_seen_version || $check_max_id > $last_seen_id) {
            echo "event: mutation\n";
            echo "data: " . json_encode([
                'event' => 'SYSTEM_MUTATION',
                'version' => $check_version,
                'latest_log_id' => $check_max_id,
                'timestamp' => time()
            ]) . "\n\n";
            @flush();
            $last_seen_version = $check_version;
            $last_seen_id = $check_max_id;
        }

        if ((time() - $last_ping) >= 8) {
            echo ": keepalive\n\n";
            @flush();
            $last_ping = time();
        }

        usleep(350000);
    }

    exit;
}

// Live JSON Polling & Real-time Push Endpoint
if (isset($_GET['live_update'])) {
    header('Content-Type: application/json');
    
    $client_version = $_GET['version'] ?? '';
    $current_version = getSystemMutationVersion($pdo);
    $has_changed = ($client_version !== $current_version);

    $client_last_id = isset($_GET['last_log_id']) ? (int)$_GET['last_log_id'] : 0;
    $current_max_id = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM inventory_logs")->fetchColumn();
    $has_new_logs = ($current_max_id > $client_last_id);

    $grouped_logs = fetchGroupedLogs($pdo);
    $current_low_stock_alerts = computeLowStockAlerts($initial_stocks, $categories_db);

    $total_inbound = (int)$pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE type = 'ORDER' AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') AND invoice NOT LIKE 'ADJ-%'")->fetchColumn();
    $total_outbound = (int)$pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE type IN ('SALE', 'WARRANTY', 'FREE') AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') AND invoice NOT LIKE 'ADJ-%'")->fetchColumn();
    $total_transactions = (int)$pdo->query("SELECT COUNT(DISTINCT CASE WHEN created_at != '' THEN created_at ELSE CAST(id AS CHAR) END) FROM inventory_logs")->fetchColumn();
    $active_warranties_count = count(fetchWarrantyRecords($pdo));

    $customers_live = fetchDistinctCustomers($pdo);
    $customer_warranties_live = fetchCustomerMachineWarranties($pdo);

    $response = [
        'version' => $current_version,
        'has_changed' => $has_changed,
        'stocks' => $initial_stocks,
        'categories' => $categories_db,
        'catalog' => $catalog,
        'customers' => $customers_live,
        'customer_warranties' => $customer_warranties_live,
        'has_new_logs' => $has_new_logs,
        'latest_log_id' => $current_max_id,
        'dashboard_html' => renderDashboardCards($categories_db, $catalog, $pdo, $is_admin),
        'warranty_html' => renderWarrantySection($pdo),
        'ledger_html' => renderLedgerTableBody($grouped_logs, $categories_db, $is_admin),
        'alerts_html' => renderAlerts($current_low_stock_alerts),
        'alerts_count' => count($current_low_stock_alerts),
        'kpi' => [
            'total_transactions' => $total_transactions,
            'total_inbound' => $total_inbound,
            'total_outbound' => $total_outbound,
            'active_warranties' => $active_warranties_count
        ]
    ];

    if ($is_admin) {
        $response['admin_categories_html'] = renderAdminCategoryCards($categories_db, $catalog, $pdo);
    }
    
    echo json_encode($response);
    exit;
}

// Grouped Logs for Global History Ledger (Accessible by all users, read-only for staff)
$grouped_logs = fetchGroupedLogs($pdo);

// Calculate Summary Statistics for Global Ledger
$total_inbound = (int)$pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE type = 'ORDER' AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') AND invoice NOT LIKE 'ADJ-%'")->fetchColumn();
$total_outbound = (int)$pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM inventory_logs WHERE type IN ('SALE', 'WARRANTY', 'FREE') AND client_supplier NOT IN ('Initial Stock', 'Inventory Correction', 'System Adjustment', 'Item Deleted') AND invoice NOT LIKE 'ADJ-%'")->fetchColumn();
$total_transactions = (int)$pdo->query("SELECT COUNT(DISTINCT CASE WHEN created_at != '' THEN created_at ELSE CAST(id AS CHAR) END) FROM inventory_logs")->fetchColumn();
$active_warranties_count = count(fetchWarrantyRecords($pdo));

// Flash Messages
$message = "";
if (isset($_GET['msg'])) {
    $msgMap = [
        'success' => 'Transaction saved successfully.',
        'deleted' => 'Record deleted permanently.',
        'edited' => 'Record updated successfully.',
        'reset' => 'System reset completed. All records wiped.',
        'cat_added' => 'New category created successfully.',
        'cat_edited' => 'Category updated successfully.',
        'cat_deleted' => 'Category deleted successfully.',
        'bulk_updated' => 'Inventory catalog and stocks updated.'
    ];
    $message = $msgMap[$_GET['msg']] ?? htmlspecialchars($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Al Rish Al Khaleeji - Stock Control Panel</title>
    
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ea580c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace']
                    },
                    colors: {
                        slate: {
                            850: '#172033',
                            950: '#0b0f19'
                        },
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0b0f19; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @media (max-width: 640px) {
            input, select, textarea, button {
                font-size: 14px;
            }
        }
    </style>

    <script>
        window.IS_ADMIN = <?= $is_admin ? 'true' : 'false' ?>;
        window.SYSTEM_CATALOG = <?= json_encode($catalog) ?>;
        window.SYSTEM_CATEGORIES = <?= json_encode($categories_db) ?>;
        window.CURRENT_STOCKS = <?= json_encode($initial_stocks) ?>;
        window.SYSTEM_CUSTOMERS = <?= json_encode($customers_list) ?>;
        window.CUSTOMER_WARRANTIES = <?= json_encode($customer_warranties) ?>;
        window.SYSTEM_LAST_LOG_ID = <?= json_encode($max_log_id) ?>;
        window.SYSTEM_VERSION = <?= json_encode($system_version) ?>;
        let counterItemsCart = [];
        let currentCounterMode = 'SALE';
        window.IS_SYNCING = false;

        // --- REAL-TIME EVENT BUS (Cross-Tab Instant Push like Laravel Echo / Reverb) ---
        window.APP_EVENTS = ('BroadcastChannel' in window) ? new BroadcastChannel('alrish_realtime_events') : null;

        function broadcastAppMutation(actionName, meta = {}) {
            if (window.APP_EVENTS) {
                try {
                    window.APP_EVENTS.postMessage({
                        action: actionName,
                        meta: meta,
                        timestamp: Date.now()
                    });
                } catch(e) {}
            }
        }

        if (window.APP_EVENTS) {
            window.APP_EVENTS.onmessage = (e) => {
                // Instant reactive update when another tab commits a change
                triggerLiveUpdateNow(true);
            };
        }

        // --- REAL-TIME SERVER-SENT EVENTS (SSE) ENGINE (Cross-Device Push) ---
        let realtimeEventSource = null;
        let sseReconnectTimer = null;

        function initRealtimeEventStream() {
            if (!window.EventSource) return;
            if (realtimeEventSource) {
                try { realtimeEventSource.close(); } catch(e) {}
                realtimeEventSource = null;
            }

            const sseUrl = `./?events=1&version=${encodeURIComponent(window.SYSTEM_VERSION || '')}&last_log_id=${encodeURIComponent(window.SYSTEM_LAST_LOG_ID || 0)}`;

            try {
                realtimeEventSource = new EventSource(sseUrl);

                realtimeEventSource.addEventListener('connected', (e) => {
                    // Connected to Server-Sent Real-time Stream
                });

                realtimeEventSource.addEventListener('mutation', (e) => {
                    try {
                        const payload = JSON.parse(e.data);
                        if (payload.version) {
                            window.SYSTEM_VERSION = payload.version;
                        }
                        if (payload.latest_log_id) {
                            window.SYSTEM_LAST_LOG_ID = payload.latest_log_id;
                        }
                    } catch(err) {}
                    // Instant live update push
                    triggerLiveUpdateNow(true);
                });

                realtimeEventSource.onerror = (e) => {
                    if (realtimeEventSource) {
                        try { realtimeEventSource.close(); } catch(err) {}
                        realtimeEventSource = null;
                    }
                    if (!sseReconnectTimer && navigator.onLine) {
                        sseReconnectTimer = setTimeout(() => {
                            sseReconnectTimer = null;
                            initRealtimeEventStream();
                        }, 2500);
                    }
                };
            } catch(err) {
                console.warn("Real-time EventSource error:", err);
            }
        }

        function syncCategorySelects() {
            if (!window.SYSTEM_CATEGORIES) return;

            // 1. Update Counter Category Select
            const counterCatSelect = document.getElementById('counter_cat');
            if (counterCatSelect) {
                const currentSelected = counterCatSelect.value;
                counterCatSelect.innerHTML = '';
                for (const [slug, cat] of Object.entries(window.SYSTEM_CATEGORIES)) {
                    const opt = document.createElement('option');
                    opt.value = slug;
                    opt.textContent = cat.name || slug;
                    if (slug === currentSelected) opt.selected = true;
                    counterCatSelect.appendChild(opt);
                }
            }

            // 2. Update History Category Filter
            const historyCatFilter = document.getElementById('historyCatFilter');
            if (historyCatFilter) {
                const currentSelected = historyCatFilter.value;
                historyCatFilter.innerHTML = '<option value="">All Categories</option>';
                for (const [slug, cat] of Object.entries(window.SYSTEM_CATEGORIES)) {
                    const opt = document.createElement('option');
                    opt.value = slug;
                    opt.textContent = cat.name || slug;
                    if (slug === currentSelected) opt.selected = true;
                    historyCatFilter.appendChild(opt);
                }
            }
        }

        // --- SEAMLESS OFFLINE DATA & BACKGROUND SYNC ENGINE ---
        function getOfflineOutbox() {
            try {
                const raw = localStorage.getItem('alrish_offline_outbox');
                return raw ? JSON.parse(raw) : [];
            } catch(e) {
                return [];
            }
        }

        function saveToOfflineOutbox(item) {
            const queue = getOfflineOutbox();
            queue.push(item);
            try {
                localStorage.setItem('alrish_offline_outbox', JSON.stringify(queue));
            } catch(e) {}
        }

        function removeFirstFromOfflineOutbox() {
            const queue = getOfflineOutbox();
            if (queue.length > 0) {
                queue.shift();
                try {
                    localStorage.setItem('alrish_offline_outbox', JSON.stringify(queue));
                } catch(e) {}
            }
        }

        function formDataToEntries(formData) {
            const entries = [];
            for (const [key, value] of formData.entries()) {
                entries.push([key, value]);
            }
            return entries;
        }

        function entriesToFormData(entries) {
            const fd = new FormData();
            for (const [key, value] of entries) {
                fd.append(key, value);
            }
            return fd;
        }

        function applyOptimisticStocksToDom() {
            if (!window.CURRENT_STOCKS) return;
            document.querySelectorAll('.live-balance').forEach(el => {
                let cat = el.getAttribute('data-category');
                let item = el.getAttribute('data-item');
                if(window.CURRENT_STOCKS[cat] && window.CURRENT_STOCKS[cat][item] !== undefined) {
                    el.innerText = window.CURRENT_STOCKS[cat][item]['balance'];
                }
            });
            document.querySelectorAll('.live-in').forEach(el => {
                let cat = el.getAttribute('data-category');
                let item = el.getAttribute('data-item');
                if(window.CURRENT_STOCKS[cat] && window.CURRENT_STOCKS[cat][item] !== undefined) {
                    el.innerText = window.CURRENT_STOCKS[cat][item]['in'];
                }
            });
            document.querySelectorAll('.live-out').forEach(el => {
                let cat = el.getAttribute('data-category');
                let item = el.getAttribute('data-item');
                if(window.CURRENT_STOCKS[cat] && window.CURRENT_STOCKS[cat][item] !== undefined) {
                    el.innerText = window.CURRENT_STOCKS[cat][item]['out'];
                }
            });
            if (typeof updateStockBadge === 'function') updateStockBadge();
        }

        async function processOfflineQueue() {
            if (window.IS_SYNCING) return;
            const queue = getOfflineOutbox();
            if (queue.length === 0) return;
            if (!navigator.onLine) return;

            window.IS_SYNCING = true;
            let syncedCount = 0;

            try {
                while (getOfflineOutbox().length > 0) {
                    const currentQueue = getOfflineOutbox();
                    const item = currentQueue[0];
                    const formData = entriesToFormData(item.entries);
                    formData.set('ajax', '1');

                    try {
                        const res = await fetch(item.endpoint || './', {
                            method: 'POST',
                            body: formData
                        });

                        removeFirstFromOfflineOutbox();
                        syncedCount++;
                    } catch(netErr) {
                        break;
                    }
                }

                if (syncedCount > 0) {
                    showToast("Changes synced to system.", "success");
                    broadcastAppMutation('SYNC_COMPLETED');
                    triggerLiveUpdateNow(true);
                }
            } catch(e) {
                console.warn("Background sync error:", e);
            } finally {
                window.IS_SYNCING = false;
            }
        }

        function queueTicketOffline(formData, inv, client, mode, submitBtn) {
            // Immediate optimistic stock updates in memory & on-screen DOM
            if (window.CURRENT_STOCKS && counterItemsCart.length > 0) {
                counterItemsCart.forEach(item => {
                    const cat = item.catSlug;
                    const desc = item.item;
                    const qty = parseInt(item.qty, 10) || 0;
                    if (!window.CURRENT_STOCKS[cat]) window.CURRENT_STOCKS[cat] = {};
                    if (!window.CURRENT_STOCKS[cat][desc]) window.CURRENT_STOCKS[cat][desc] = { balance: 0, in: 0, out: 0 };

                    if (mode === 'SALE') {
                        window.CURRENT_STOCKS[cat][desc]['balance'] = (parseInt(window.CURRENT_STOCKS[cat][desc]['balance'], 10) || 0) - qty;
                        window.CURRENT_STOCKS[cat][desc]['out'] = (parseInt(window.CURRENT_STOCKS[cat][desc]['out'], 10) || 0) + qty;
                    } else if (mode === 'ORDER') {
                        window.CURRENT_STOCKS[cat][desc]['balance'] = (parseInt(window.CURRENT_STOCKS[cat][desc]['balance'], 10) || 0) + qty;
                        window.CURRENT_STOCKS[cat][desc]['in'] = (parseInt(window.CURRENT_STOCKS[cat][desc]['in'], 10) || 0) + qty;
                    } else if (mode === 'ADJUST') {
                        window.CURRENT_STOCKS[cat][desc]['balance'] = qty;
                    }
                });

                applyOptimisticStocksToDom();

                try {
                    localStorage.setItem('alrish_cached_stocks', JSON.stringify(window.CURRENT_STOCKS));
                } catch(e) {}
            }

            const queuedItem = {
                id: 'outbox_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6),
                endpoint: './',
                entries: formDataToEntries(formData),
                createdAt: new Date().toISOString()
            };

            saveToOfflineOutbox(queuedItem);

            counterItemsCart = [];
            renderCounterTicket();
            if (document.getElementById('counter_invoice')) document.getElementById('counter_invoice').value = '';
            if (document.getElementById('counter_client')) document.getElementById('counter_client').value = '';
            const freebieContainer = document.querySelector('#counterOrderForm .freebie-container');
            if (freebieContainer) freebieContainer.innerHTML = '';

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = "Process & Finalize Transaction";
            }

            showToast("Transaction saved successfully!", "success");
            broadcastAppMutation('OFFLINE_TRANSACTION');
        }

        function queueFormOffline(form, formData) {
            closeEditModal();
            closeEditCategoryModal();
            const addModal = document.getElementById('addCategoryModal');
            if (addModal) addModal.classList.add('hidden');

            const queuedItem = {
                id: 'outbox_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6),
                endpoint: form.action || './',
                entries: formDataToEntries(formData),
                createdAt: new Date().toISOString()
            };
            saveToOfflineOutbox(queuedItem);
            showToast("Changes saved successfully!", "success");
            broadcastAppMutation('OFFLINE_FORM_SAVED');
        }

        // --- TAB NAVIGATION ENGINE ---
        function showTab(tabId) {
            if (!tabId) tabId = 'tab-dashboard';
            if (tabId === 'pos' || tabId === 'counter' || tabId === 'tab-pos') tabId = 'tab-counter';
            if (tabId === 'dashboard') tabId = 'tab-dashboard';
            if (tabId === 'alerts') tabId = 'tab-alerts';
            if (tabId === 'admin') tabId = 'tab-admin';
            if (tabId === 'history' || tabId === 'ledger') tabId = 'tab-history';
            if (tabId === 'warranty') tabId = 'tab-warranty';
            if (!tabId.startsWith('tab-')) tabId = 'tab-' + tabId;

            // Strict Role Guard (Catalog Admin is Admin-Only)
            if (!window.IS_ADMIN && tabId === 'tab-admin') {
                tabId = 'tab-dashboard';
            }

            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

            let selectedTab = document.getElementById(tabId);
            if (!selectedTab) {
                tabId = 'tab-dashboard';
                selectedTab = document.getElementById('tab-dashboard');
            }
            if (selectedTab) selectedTab.classList.remove('hidden');

            // Desktop Sidebar tab styling
            document.querySelectorAll('.tab-btn-desktop').forEach(el => {
                el.classList.remove('bg-slate-800', 'text-brand-400', 'border-l-4', 'border-brand-500');
                el.classList.add('text-slate-400');
            });
            let selectedDesktopBtn = document.getElementById('btn-desktop-' + tabId);
            if (selectedDesktopBtn) {
                selectedDesktopBtn.classList.remove('text-slate-400');
                selectedDesktopBtn.classList.add('bg-slate-800', 'text-brand-400', 'border-l-4', 'border-brand-500');
            }

            // Mobile Bottom Navigation tab styling
            document.querySelectorAll('.tab-btn-mobile').forEach(el => {
                el.classList.remove('text-brand-400', 'bg-slate-800/80');
                el.classList.add('text-slate-400');
            });
            let selectedMobileBtn = document.getElementById('btn-mobile-' + tabId);
            if (selectedMobileBtn) {
                selectedMobileBtn.classList.remove('text-slate-400');
                selectedMobileBtn.classList.add('text-brand-400', 'bg-slate-800/80');
            }

            try {
                localStorage.setItem('activeInventoryTab', tabId);
            } catch(e) {}

            if (tabId === 'tab-counter' && typeof updateCounterProductOptions === 'function') {
                const itSelect = document.getElementById('counter_item');
                if (!itSelect || itSelect.children.length === 0) {
                    updateCounterProductOptions(false);
                } else {
                    updateCounterProductOptions(true);
                }
            }
        }

        // --- BACKGROUND LIVE & EVENT-DRIVEN UPDATER ---
        function triggerLiveUpdateNow(forceHtmlUpdate = false) {
            let fetchUrl = '?live_update=1&t=' + Date.now();
            if (window.SYSTEM_VERSION !== undefined) {
                fetchUrl += '&version=' + encodeURIComponent(window.SYSTEM_VERSION);
            }
            if (window.SYSTEM_LAST_LOG_ID !== undefined) {
                fetchUrl += '&last_log_id=' + window.SYSTEM_LAST_LOG_ID;
            }
            
            fetch(fetchUrl)
            .then(r => r.json())
            .then(data => {
                if (data.offline) return;

                let stocksData = data.stocks || data;
                window.CURRENT_STOCKS = stocksData;
                
                if (data.categories) {
                    window.SYSTEM_CATEGORIES = data.categories;
                    syncCategorySelects();
                }
                if (data.catalog) {
                    window.SYSTEM_CATALOG = data.catalog;
                }
                if (typeof updateCounterProductOptions === 'function' && (data.has_changed || forceHtmlUpdate)) {
                    updateCounterProductOptions(true);
                }

                try {
                    localStorage.setItem('alrish_cached_stocks', JSON.stringify(stocksData));
                    if (data.categories) localStorage.setItem('alrish_cached_categories', JSON.stringify(data.categories));
                    if (data.catalog) localStorage.setItem('alrish_cached_catalog', JSON.stringify(data.catalog));
                    if (data.version) localStorage.setItem('alrish_cached_version', data.version);
                } catch(e) {}

                if (data.has_changed || data.has_new_logs) {
                    forceHtmlUpdate = true;
                    if (data.version) window.SYSTEM_VERSION = data.version;
                    if (data.latest_log_id) window.SYSTEM_LAST_LOG_ID = data.latest_log_id;
                }
                
                if (typeof updateStockBadge === 'function') updateStockBadge();

                // Live Stock DOM Updater
                applyOptimisticStocksToDom();

                // Live KPI Metrics Updater
                if (data.kpi) {
                    const elTrans = document.getElementById('kpi-total-transactions');
                    const elIn = document.getElementById('kpi-total-inbound');
                    const elOut = document.getElementById('kpi-total-outbound');
                    const elWarr = document.getElementById('kpi-active-warranties');
                    if (elTrans && data.kpi.total_transactions !== undefined) elTrans.innerText = data.kpi.total_transactions;
                    if (elIn && data.kpi.total_inbound !== undefined) elIn.innerText = '+' + data.kpi.total_inbound;
                    if (elOut && data.kpi.total_outbound !== undefined) elOut.innerText = '-' + data.kpi.total_outbound;
                    if (elWarr && data.kpi.active_warranties !== undefined) elWarr.innerText = data.kpi.active_warranties;
                }

                // Live Alerts Count & Badges Updater
                if (data.alerts_count !== undefined) {
                    const deskBadge = document.getElementById('desktop-alerts-badge');
                    const mobBadge = document.getElementById('mobile-alerts-badge');
                    if (deskBadge) {
                        deskBadge.innerText = data.alerts_count;
                        if (data.alerts_count > 0) deskBadge.classList.remove('hidden');
                        else deskBadge.classList.add('hidden');
                    }
                    if (mobBadge) {
                        mobBadge.innerText = data.alerts_count;
                        if (data.alerts_count > 0) mobBadge.classList.remove('hidden');
                        else mobBadge.classList.add('hidden');
                    }
                }

                if (forceHtmlUpdate) {
                    if (data.dashboard_html) {
                        const dashBox = document.getElementById('dashboard-cards-container');
                        const searchInput = document.getElementById('dashboardSearch');
                        const isSearching = searchInput && searchInput.value.trim().length > 0;
                        if (dashBox && !isSearching) dashBox.innerHTML = data.dashboard_html;
                    }
                    if (data.warranty_html) {
                        const warrBox = document.getElementById('warranty-container');
                        const searchInput = document.getElementById('warrantySearch');
                        const isSearching = searchInput && searchInput.value.trim().length > 0;
                        if (warrBox && !isSearching) warrBox.innerHTML = data.warranty_html;
                    }
                    if (data.alerts_html) {
                        const alertsBox = document.getElementById('alerts-container');
                        if (alertsBox) alertsBox.innerHTML = data.alerts_html;
                    }
                    if (data.admin_categories_html) {
                        const adminBox = document.getElementById('admin-categories-container');
                        const searchInput = document.getElementById('catalogAdminSearch');
                        const isSearching = searchInput && searchInput.value.trim().length > 0;
                        const hasDirtyAdminForm = Array.from(document.querySelectorAll('[id^="admin-actions-"]')).some(el => !el.classList.contains('hidden'));
                        if (adminBox && !isSearching && !hasDirtyAdminForm) {
                            adminBox.innerHTML = data.admin_categories_html;
                            if (typeof captureAdminSnapshots === 'function') captureAdminSnapshots();
                        }
                    }
                    if (data.ledger_html) {
                        const ledgerBody = document.getElementById('adminTableBody');
                        const searchInput = document.getElementById('adminSearch');
                        const isSearching = searchInput && searchInput.value.trim().length > 0;
                        if (ledgerBody && !isSearching) {
                            ledgerBody.innerHTML = data.ledger_html;
                            if (typeof filterAdminTable === 'function') filterAdminTable();
                        }
                    }
                }
            })
            .catch(e => {
                // Network unreachable
            });
        }

        // --- POS COUNTER ENGINE ---
        function setCounterMode(mode) {
            currentCounterMode = mode;
            const btnSale = document.getElementById('btn-mode-sale');
            const btnOrder = document.getElementById('btn-mode-order');
            const clientLabel = document.getElementById('lbl-client-supplier');
            const clientInput = document.getElementById('counter_client');
            const warrantyContainer = document.getElementById('counter_warranty_container');
            const modeBadge = document.getElementById('ticket-mode-badge');
            const submitBtn = document.getElementById('counter-submit-btn');

            if (mode === 'SALE') {
                btnSale.className = "flex-1 py-3 px-3 sm:px-4 rounded-2xl font-bold text-xs sm:text-sm bg-brand-600 text-white shadow-lg shadow-brand-600/20 border border-brand-500 transition-all flex items-center justify-center gap-2 cursor-pointer";
                btnOrder.className = "flex-1 py-3 px-3 sm:px-4 rounded-2xl font-bold text-xs sm:text-sm bg-slate-900 text-slate-400 border border-slate-800 hover:text-white transition-all flex items-center justify-center gap-2 cursor-pointer";
                if (clientLabel) clientLabel.innerText = "Client / Customer Name";
                if (clientInput) clientInput.placeholder = "e.g. Al Faisal Trading";
                if (warrantyContainer) warrantyContainer.classList.remove('hidden');
                if (modeBadge) {
                    modeBadge.innerText = "Client Sale Ticket";
                    modeBadge.className = "px-3 py-1 text-xs font-bold rounded-full bg-brand-950/80 text-brand-400 border border-brand-800/80 uppercase tracking-wider";
                }
                if (submitBtn) submitBtn.className = "w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-600 disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed text-white font-bold py-4 rounded-2xl shadow-xl shadow-brand-600/20 text-sm md:text-base flex items-center justify-center gap-2 transition-all cursor-pointer";
            } else {
                btnSale.className = "flex-1 py-3 px-3 sm:px-4 rounded-2xl font-bold text-xs sm:text-sm bg-slate-900 text-slate-400 border border-slate-800 hover:text-white transition-all flex items-center justify-center gap-2 cursor-pointer";
                btnOrder.className = "flex-1 py-3 px-3 sm:px-4 rounded-2xl font-bold text-xs sm:text-sm bg-emerald-600 text-white shadow-lg shadow-emerald-600/20 border border-emerald-500 transition-all flex items-center justify-center gap-2 cursor-pointer";
                if (clientLabel) clientLabel.innerText = "Supplier / Factory Location";
                if (clientInput) clientInput.placeholder = "e.g. Shanghai Source Factory";
                if (warrantyContainer) warrantyContainer.classList.add('hidden');
                if (modeBadge) {
                    modeBadge.innerText = "Supplier Receipt Ticket";
                    modeBadge.className = "px-3 py-1 text-xs font-bold rounded-full bg-emerald-950/80 text-emerald-400 border border-emerald-800/80 uppercase tracking-wider";
                }
                if (submitBtn) submitBtn.className = "w-full bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-600 disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed text-white font-bold py-4 rounded-2xl shadow-xl shadow-emerald-600/20 text-sm md:text-base flex items-center justify-center gap-2 transition-all cursor-pointer";
            }
            const clientSuggestions = document.getElementById('counter_client_suggestions');
            if (clientSuggestions) {
                clientSuggestions.classList.add('hidden');
                clientSuggestions.innerHTML = '';
            }
            renderCounterTicket();
        }

        function generateAutoInvoice() {
            const today = new Date();
            const dateStr = today.getFullYear().toString() +
                            String(today.getMonth() + 1).padStart(2, '0') +
                            String(today.getDate()).padStart(2, '0');
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            const prefix = currentCounterMode === 'SALE' ? 'INV' : 'REC';
            const invInput = document.getElementById('counter_invoice');
            if (invInput) invInput.value = `${prefix}-${dateStr}-${randomNum}`;
        }

        // --- POS CUSTOMER AUTOCOMPLETE ENGINE ---
        let activeClientSuggestionIndex = -1;

        function handleClientAutocomplete(input) {
            const listContainer = document.getElementById('counter_client_suggestions');
            if (!listContainer) return;
            const query = (input ? input.value || '' : '').trim().toLowerCase();
            
            // Immediately hide suggestions if input is empty or cleared
            if (!query || query.length === 0 || currentCounterMode !== 'SALE' || !window.SYSTEM_CUSTOMERS || window.SYSTEM_CUSTOMERS.length === 0) {
                listContainer.classList.add('hidden');
                listContainer.innerHTML = '';
                activeClientSuggestionIndex = -1;
                return;
            }

            // Match customers by substring OR by initials (e.g. "HA" matches "Haneen Advt.")
            const matches = window.SYSTEM_CUSTOMERS.filter(name => {
                const lowerName = name.toLowerCase();
                if (lowerName.includes(query)) return true;
                // Initials matching
                const words = lowerName.split(/\s+/).filter(Boolean);
                const initials = words.map(w => w[0]).join('');
                if (initials.startsWith(query) || initials.includes(query)) return true;
                return false;
            });

            if (matches.length === 0 || (matches.length === 1 && matches[0].toLowerCase() === query)) {
                listContainer.classList.add('hidden');
                listContainer.innerHTML = '';
                activeClientSuggestionIndex = -1;
                return;
            }

            listContainer.innerHTML = '';
            activeClientSuggestionIndex = -1;

            matches.slice(0, 8).forEach((custName, idx) => {
                const item = document.createElement('div');
                item.className = "client-sugg-item p-2.5 rounded-xl hover:bg-slate-800 text-xs font-semibold text-slate-200 flex items-center justify-between cursor-pointer transition-colors";
                item.setAttribute('data-index', idx);
                item.innerHTML = `
                    <div class="flex items-center gap-2 truncate">
                        <span class="w-6 h-6 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center text-brand-500 shrink-0">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <span class="truncate text-white font-bold">${custName}</span>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-brand-950/80 text-brand-400 border border-brand-800/80 uppercase font-bold shrink-0">Existing Client</span>
                `;
                item.onmousedown = (e) => {
                    e.preventDefault();
                    selectClientSuggestion(custName);
                };
                listContainer.appendChild(item);
            });

            listContainer.classList.remove('hidden');
        }

        function handleClientAutocompleteKey(e) {
            const listContainer = document.getElementById('counter_client_suggestions');
            if (!listContainer || listContainer.classList.contains('hidden')) return;

            const items = listContainer.querySelectorAll('.client-sugg-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeClientSuggestionIndex = (activeClientSuggestionIndex + 1) % items.length;
                updateClientSuggestionHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeClientSuggestionIndex = (activeClientSuggestionIndex - 1 + items.length) % items.length;
                updateClientSuggestionHighlight(items);
            } else if (e.key === 'Enter') {
                if (activeClientSuggestionIndex >= 0 && activeClientSuggestionIndex < items.length) {
                    e.preventDefault();
                    const selectedText = items[activeClientSuggestionIndex].querySelector('.truncate.text-white').textContent;
                    selectClientSuggestion(selectedText);
                }
            } else if (e.key === 'Escape') {
                listContainer.classList.add('hidden');
            }
        }

        function updateClientSuggestionHighlight(items) {
            items.forEach((it, idx) => {
                if (idx === activeClientSuggestionIndex) {
                    it.classList.add('bg-slate-800', 'border', 'border-brand-500/50');
                    it.scrollIntoView({ block: 'nearest' });
                } else {
                    it.classList.remove('bg-slate-800', 'border', 'border-brand-500/50');
                }
            });
        }

        function selectClientSuggestion(name) {
            const input = document.getElementById('counter_client');
            if (input) {
                input.value = name;
            }
            const listContainer = document.getElementById('counter_client_suggestions');
            if (listContainer) {
                listContainer.classList.add('hidden');
                listContainer.innerHTML = '';
            }
        }

        // --- POS FAST PRODUCT SEARCH ENGINE ACROSS ALL CATEGORIES ---
        let activePosSearchIndex = -1;

        function getJsCategoryIcon(iconCode, classes = 'w-3.5 h-3.5') {
            const customPngIcons = {
                'uv-printer': 'icons/uv-printer.png',
                'laser-cutter': 'icons/laser-cutter.png',
                'cnc-spindle': 'icons/cnc-spindle.png',
                'vinyl-roll': 'icons/vinyl-roll.png'
            };

            const icons = {
                'dashboard': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>`,
                'cart': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>`,
                'terminal': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>`,
                'bell': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>`,
                'shield': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>`,
                'settings': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>`,
                'history': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
                'box': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>`,
                'zap': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
                'droplet': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>`,
                'printer': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>`,
                'cpu': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>`,
                'tool': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>`,
                'layers': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>`,
                'scissors': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>`,
                'disc': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>`,
                'sliders': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>`,
                'tag': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`,
                'truck': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>`,
                'activity': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>`,
                'sparkles': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>`,
                'archive': `<svg class="${classes}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>`
            };

            const norm = (iconCode || 'box').toLowerCase().trim();
            if (customPngIcons[norm]) {
                return `<span class="${classes} inline-block bg-current shrink-0" style="-webkit-mask: url('${customPngIcons[norm]}') no-repeat center / contain; mask: url('${customPngIcons[norm]}') no-repeat center / contain;"></span>`;
            }
            if (icons[norm]) {
                return icons[norm];
            }
            if (norm.includes('uv') || norm.includes('dtf')) return `<span class="${classes} inline-block bg-current shrink-0" style="-webkit-mask: url('icons/uv-printer.png') no-repeat center / contain; mask: url('icons/uv-printer.png') no-repeat center / contain;"></span>`;
            if (norm.includes('laser') || norm === '⚡' || norm === 'zap') return `<span class="${classes} inline-block bg-current shrink-0" style="-webkit-mask: url('icons/laser-cutter.png') no-repeat center / contain; mask: url('icons/laser-cutter.png') no-repeat center / contain;"></span>`;
            if (norm.includes('spindle') || norm.includes('milling') || norm.includes('router') || norm.includes('cnc') || norm === '🗜️') return `<span class="${classes} inline-block bg-current shrink-0" style="-webkit-mask: url('icons/cnc-spindle.png') no-repeat center / contain; mask: url('icons/cnc-spindle.png') no-repeat center / contain;"></span>`;
            if (norm.includes('vinyl') || norm.includes('roll') || norm.includes('film')) return `<span class="${classes} inline-block bg-current shrink-0" style="-webkit-mask: url('icons/vinyl-roll.png') no-repeat center / contain; mask: url('icons/vinyl-roll.png') no-repeat center / contain;"></span>`;
            if (norm.includes('ink') || norm === '🧪' || norm === 'droplet') return icons['droplet'];
            if (norm.includes('printer') || norm.includes('plotter') || norm.includes('eco') || norm === '⚙️') return icons['printer'];
            if (norm.includes('lamination') || norm.includes('heat')) return icons['tool'];
            if (norm.includes('media') || norm.includes('sheet')) return `<span class="${classes} inline-block bg-current shrink-0" style="-webkit-mask: url('icons/vinyl-roll.png') no-repeat center / contain; mask: url('icons/vinyl-roll.png') no-repeat center / contain;"></span>`;
            if (norm.includes('cut')) return icons['scissors'];

            return icons['box'];
        }

        function getAllSearchableProducts() {
            const list = [];
            if (!window.SYSTEM_CATEGORIES) return list;

            for (const [slug, catInfo] of Object.entries(window.SYSTEM_CATEGORIES)) {
                const catName = catInfo.name || slug;
                const catIcon = catInfo.icon || 'box';
                const isMachine = (catInfo.is_machine !== undefined) ? parseInt(catInfo.is_machine, 10) !== 0 : true;

                // 1. Complete Machine Model Unit
                if (isMachine) {
                    list.push({
                        type: 'machine',
                        catSlug: slug,
                        catName: catName,
                        catIcon: catIcon,
                        itemValue: `MACHINE_UNIT|${slug}|${catName}`,
                        displayName: `[Complete Machine Unit] ${catName}`,
                        stock: null,
                        isMachineUnit: true
                    });
                }

                // 2. Product items / Spare parts in this category
                const items = window.SYSTEM_CATALOG ? (window.SYSTEM_CATALOG[slug] || []) : [];
                items.forEach(itemName => {
                    let balance = 0;
                    if (window.CURRENT_STOCKS && window.CURRENT_STOCKS[slug] && window.CURRENT_STOCKS[slug][itemName]) {
                        balance = window.CURRENT_STOCKS[slug][itemName]['balance'] || 0;
                    }
                    list.push({
                        type: 'part',
                        catSlug: slug,
                        catName: catName,
                        catIcon: catIcon,
                        itemValue: itemName,
                        displayName: itemName,
                        stock: balance,
                        isMachineUnit: false
                    });
                });
            }
            return list;
        }

        function handlePosProductSearch(input) {
            const dropdown = document.getElementById('pos_product_search_dropdown');
            const clearBtn = document.getElementById('clearPosProdSearch');
            if (!dropdown) return;

            const query = (input.value || '').trim().toLowerCase();
            if (clearBtn) {
                if (query.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            if (!query) {
                dropdown.classList.add('hidden');
                dropdown.innerHTML = '';
                activePosSearchIndex = -1;
                return;
            }

            const terms = query.split(/\s+/).filter(Boolean);
            const allProducts = getAllSearchableProducts();

            const matches = allProducts.filter(p => {
                const searchBlob = `${p.displayName} ${p.catName} ${p.catSlug}`.toLowerCase();
                return terms.every(t => searchBlob.includes(t));
            });

            if (matches.length === 0) {
                dropdown.innerHTML = `
                    <div class="p-3 text-center text-xs text-slate-500">
                        No matching machines or spare parts found for "${input.value}".
                    </div>
                `;
                dropdown.classList.remove('hidden');
                activePosSearchIndex = -1;
                return;
            }

            dropdown.innerHTML = '';
            activePosSearchIndex = -1;

            matches.slice(0, 15).forEach((prod, idx) => {
                const item = document.createElement('div');
                item.className = "pos-search-item p-2.5 rounded-xl hover:bg-slate-800 text-xs font-semibold text-slate-200 flex items-center justify-between cursor-pointer transition-colors";
                item.setAttribute('data-index', idx);

                let stockBadgeHtml = '';
                if (prod.isMachineUnit) {
                    stockBadgeHtml = `<span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-purple-950/80 text-purple-400 border border-purple-800/80 uppercase">Machine Unit</span>`;
                } else {
                    const st = prod.stock;
                    const bClass = st <= 0 ? 'bg-red-950/80 text-red-400 border-red-800/80' : (st <= 5 ? 'bg-yellow-950/80 text-yellow-400 border-yellow-800/80' : 'bg-emerald-950/80 text-emerald-400 border-emerald-800/80');
                    stockBadgeHtml = `<span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold border ${bClass}">Stock: ${st}</span>`;
                }

                const iconSvg = getJsCategoryIcon(prod.catIcon, 'w-3.5 h-3.5');

                item.innerHTML = `
                    <div class="flex items-center gap-2 truncate pr-2">
                        <span class="w-6 h-6 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center ${prod.isMachineUnit ? 'text-purple-400' : 'text-brand-500'} shrink-0">
                            ${iconSvg}
                        </span>
                        <div class="truncate">
                            <span class="truncate text-white font-bold block">${prod.displayName}</span>
                            <span class="text-[10px] text-slate-400 block">${prod.catName}</span>
                        </div>
                    </div>
                    <div class="shrink-0">
                        ${stockBadgeHtml}
                    </div>
                `;

                item.onmousedown = (e) => {
                    e.preventDefault();
                    selectPosProductFromSearch(prod.catSlug, prod.itemValue, prod.isMachineUnit);
                };

                dropdown.appendChild(item);
            });

            dropdown.classList.remove('hidden');
        }

        function handlePosProductSearchKey(e) {
            const dropdown = document.getElementById('pos_product_search_dropdown');
            if (!dropdown || dropdown.classList.contains('hidden')) return;

            const items = dropdown.querySelectorAll('.pos-search-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activePosSearchIndex = (activePosSearchIndex + 1) % items.length;
                updatePosSearchHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activePosSearchIndex = (activePosSearchIndex - 1 + items.length) % items.length;
                updatePosSearchHighlight(items);
            } else if (e.key === 'Enter') {
                if (activePosSearchIndex >= 0 && activePosSearchIndex < items.length) {
                    e.preventDefault();
                    items[activePosSearchIndex].dispatchEvent(new MouseEvent('mousedown'));
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        }

        function updatePosSearchHighlight(items) {
            items.forEach((it, idx) => {
                if (idx === activePosSearchIndex) {
                    it.classList.add('bg-slate-800', 'border', 'border-brand-500/50');
                    it.scrollIntoView({ block: 'nearest' });
                } else {
                    it.classList.remove('bg-slate-800', 'border', 'border-brand-500/50');
                }
            });
        }

        function selectPosProductFromSearch(catSlug, itemValue, isMachineUnit) {
            const catSelect = document.getElementById('counter_cat');
            const searchInput = document.getElementById('counter_product_search');
            const dropdown = document.getElementById('pos_product_search_dropdown');

            if (catSelect) {
                catSelect.value = catSlug;
                updateCounterProductOptions();
            }

            const itemSelect = document.getElementById('counter_item');
            if (itemSelect) {
                itemSelect.value = itemValue;
                handleCounterProductChange();
            }

            if (dropdown) {
                dropdown.classList.add('hidden');
                dropdown.innerHTML = '';
            }

            if (searchInput) {
                searchInput.value = '';
                const clearBtn = document.getElementById('clearPosProdSearch');
                if (clearBtn) clearBtn.classList.add('hidden');
            }

            const qtyInput = document.getElementById('counter_qty');
            if (qtyInput) qtyInput.focus();
        }

        function clearPosProductSearch() {
            const input = document.getElementById('counter_product_search');
            const dropdown = document.getElementById('pos_product_search_dropdown');
            const clearBtn = document.getElementById('clearPosProdSearch');
            if (input) input.value = '';
            if (dropdown) {
                dropdown.classList.add('hidden');
                dropdown.innerHTML = '';
            }
            if (clearBtn) clearBtn.classList.add('hidden');
            if (input) input.focus();
        }

        function updateCounterProductOptions(preserveSelected = false) {
            const catSelect = document.getElementById('counter_cat');
            const itemSelect = document.getElementById('counter_item');
            if (!catSelect || !itemSelect) return;

            const previousSelected = itemSelect.value;
            const catSlug = catSelect.value;
            const catInfo = window.SYSTEM_CATEGORIES ? window.SYSTEM_CATEGORIES[catSlug] : null;
            const catName = catInfo ? (catInfo.name || catSlug) : catSlug;
            const isMachine = catInfo && catInfo.is_machine !== undefined ? parseInt(catInfo.is_machine, 10) !== 0 : true;

            itemSelect.innerHTML = '';
            let foundPrevious = false;

            // If category is a machine or by default, add Complete Machine Unit option
            if (isMachine) {
                const machineVal = `MACHINE_UNIT|${catSlug}|${catName}`;
                const optMachine = document.createElement('option');
                optMachine.value = machineVal;
                optMachine.textContent = `⭐ [Complete Machine Unit] ${catName}`;
                optMachine.className = "font-bold text-brand-400 bg-slate-900";
                if (preserveSelected && previousSelected === machineVal) {
                    optMachine.selected = true;
                    foundPrevious = true;
                }
                itemSelect.appendChild(optMachine);
            }

            const items = window.SYSTEM_CATALOG ? (window.SYSTEM_CATALOG[catSlug] || []) : [];
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item;
                opt.textContent = item;
                if (preserveSelected && previousSelected === item) {
                    opt.selected = true;
                    foundPrevious = true;
                }
                itemSelect.appendChild(opt);
            });

            if (preserveSelected && foundPrevious) {
                itemSelect.value = previousSelected;
            }

            handleCounterProductChange();
        }

        function handleCounterProductChange() {
            const itemSelect = document.getElementById('counter_item');
            const warrantyInput = document.getElementById('counter_warranty');
            if (!itemSelect) return;

            const val = itemSelect.value || '';
            if (val.startsWith('MACHINE_UNIT|')) {
                if (currentCounterMode === 'SALE' && warrantyInput) {
                    // Default 12 months for full machine units
                    if (!warrantyInput.value || parseInt(warrantyInput.value, 10) === 0) {
                        warrantyInput.value = 12;
                    }
                }
            } else {
                // If user selected a sub-product/part and warranty is currently 12, reset back to 0
                if (currentCounterMode === 'SALE' && warrantyInput && parseInt(warrantyInput.value, 10) === 12) {
                    warrantyInput.value = 0;
                }
            }

            updateStockBadge();
        }

        function updateStockBadge() {
            const catSelect = document.getElementById('counter_cat');
            const itemSelect = document.getElementById('counter_item');
            const badge = document.getElementById('counter_stock_badge');
            if (!catSelect || !itemSelect || !badge) return;

            let catSlug = catSelect.value;
            let item = itemSelect.value;

            if (item && item.startsWith('MACHINE_UNIT|')) {
                badge.innerText = "⭐ Complete Machine Unit (12M Warranty)";
                badge.className = "px-2.5 py-1 text-xs font-bold rounded-lg bg-purple-950/80 text-purple-400 border border-purple-800/80";
                return;
            }

            if (item && item.includes('|')) {
                const parts = item.split('|');
                catSlug = parts[0];
                item = parts[1];
            }
            
            let balance = 0;
            if (window.CURRENT_STOCKS && window.CURRENT_STOCKS[catSlug] && window.CURRENT_STOCKS[catSlug][item]) {
                balance = window.CURRENT_STOCKS[catSlug][item]['balance'] || 0;
            }

            badge.innerText = `On Hand: ${balance} units`;
            if (balance <= 0) {
                badge.className = "px-2.5 py-1 text-xs font-bold rounded-lg bg-red-950/80 text-red-400 border border-red-800/80";
            } else if (balance <= 5) {
                badge.className = "px-2.5 py-1 text-xs font-bold rounded-lg bg-yellow-950/80 text-yellow-400 border border-yellow-800/80";
            } else {
                badge.className = "px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-950/80 text-emerald-400 border border-emerald-800/80";
            }
        }

        function adjustCounterQty(delta) {
            const input = document.getElementById('counter_qty');
            if (!input) return;
            let val = parseInt(input.value) || 1;
            val = Math.max(1, val + delta);
            input.value = val;
        }

        function addItemToCounterTicket() {
            const catSelect = document.getElementById('counter_cat');
            const itemSelect = document.getElementById('counter_item');
            const qtyInput = document.getElementById('counter_qty');
            const warrantyInput = document.getElementById('counter_warranty');

            if (!catSelect || !itemSelect) return;

            let catSlug = catSelect.value;
            let item = itemSelect.value;
            let itemDisplayName = item;

            if (item && item.startsWith('MACHINE_UNIT|')) {
                const parts = item.split('|');
                catSlug = parts[1] || catSlug;
                itemDisplayName = `[Complete Machine Unit] ${parts[2] || catSlug}`;
            }

            const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
            let warranty = parseInt(warrantyInput ? warrantyInput.value : 0) || 0;
            if (item && item.startsWith('MACHINE_UNIT|') && currentCounterMode === 'SALE' && warranty === 0) {
                warranty = 12;
            }

            if (!catSlug || !item) {
                alert("Please select a valid product item.");
                return;
            }

            const catName = window.SYSTEM_CATEGORIES[catSlug] ? window.SYSTEM_CATEGORIES[catSlug]['name'] : catSlug;

            const existingIndex = counterItemsCart.findIndex(i => i.catSlug === catSlug && i.item === item);
            if (existingIndex > -1) {
                counterItemsCart[existingIndex].qty += qty;
                if (currentCounterMode === 'SALE') {
                    counterItemsCart[existingIndex].warranty = warranty;
                }
            } else {
                counterItemsCart.push({
                    catSlug: catSlug,
                    catName: catName,
                    item: item,
                    displayName: itemDisplayName,
                    qty: qty,
                    warranty: currentCounterMode === 'SALE' ? warranty : 0
                });
            }

            renderCounterTicket();
            showToast(`Added ${itemDisplayName} (x${qty}) to ticket`, "success");
            if (qtyInput) qtyInput.value = 1;
        }

        function removeCounterTicketItem(index) {
            counterItemsCart.splice(index, 1);
            renderCounterTicket();
        }

        function renderCounterTicket() {
            const emptyState = document.getElementById('ticket-empty');
            const tableWrapper = document.getElementById('ticket-table-wrapper');
            const tbody = document.getElementById('ticket-tbody');
            const submitBtn = document.getElementById('counter-submit-btn');
            const totalLines = document.getElementById('ticket-total-lines');
            const totalUnits = document.getElementById('ticket-total-units');

            if (!emptyState || !tableWrapper || !tbody) return;

            if (counterItemsCart.length === 0) {
                emptyState.classList.remove('hidden');
                tableWrapper.classList.add('hidden');
                if (submitBtn) submitBtn.disabled = true;
                if (totalLines) totalLines.innerText = "0 Items";
                if (totalUnits) totalUnits.innerText = "0 Units";
                tbody.innerHTML = '';
                return;
            }

            emptyState.classList.add('hidden');
            tableWrapper.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = false;

            let unitsCount = 0;
            tbody.innerHTML = '';

            counterItemsCart.forEach((item, index) => {
                unitsCount += item.qty;
                const row = document.createElement('div');
                row.className = "p-3 sm:py-2.5 sm:px-3 rounded-2xl sm:rounded-none flex flex-col sm:grid sm:grid-cols-12 gap-2 sm:gap-3 items-stretch sm:items-center hover:bg-slate-800/40 transition-colors border-b border-slate-800/60 sm:border-none";
                
                const warrText = item.warranty > 0 ? `${item.warranty} Mos` : '--';
                const dispTitle = item.displayName || item.item;
                
                row.innerHTML = `
                    <div class="sm:col-span-6 truncate flex flex-col">
                        <span class="font-bold text-white text-xs sm:text-sm truncate">${dispTitle}</span>
                        <span class="text-[10px] sm:text-xs text-brand-400 font-medium">${item.catName}</span>
                    </div>
                    <div class="flex items-center justify-between sm:contents pt-1 sm:pt-0 border-t border-slate-800/60 sm:border-none">
                        <div class="sm:col-span-2 flex items-center sm:justify-center gap-1.5">
                            <span class="text-[10px] text-slate-500 font-bold uppercase sm:hidden">Qty:</span>
                            <span class="font-mono font-bold text-white bg-slate-950 px-2.5 py-0.5 rounded-lg border border-slate-800 text-xs sm:text-sm">${item.qty}</span>
                        </div>
                        <div class="sm:col-span-3 flex items-center sm:justify-center gap-1.5">
                            <span class="text-[10px] text-slate-500 font-bold uppercase sm:hidden">Warranty:</span>
                            <span class="text-slate-400 text-xs">${warrText}</span>
                        </div>
                        <div class="sm:col-span-1 flex justify-end">
                            <button type="button" onclick="removeCounterTicketItem(${index})" aria-label="Remove item" class="p-1.5 text-slate-500 hover:text-red-400 rounded-lg hover:bg-red-950/60 transition-colors cursor-pointer">
                                <?= addslashes(icon('trash', 'w-4 h-4')) ?>
                            </button>
                        </div>
                    </div>
                `;
                tbody.appendChild(row);
            });

            if (totalLines) totalLines.innerText = `${counterItemsCart.length} Lines`;
            if (totalUnits) totalUnits.innerText = `${unitsCount} Units`;
        }

        function processCounterTicketSubmit(e) {
            if (e) e.preventDefault();
            
            if (counterItemsCart.length === 0) {
                alert("Please add at least one product item to the order ticket.");
                return false;
            }

            const inv = document.getElementById('counter_invoice')?.value.trim();
            const client = document.getElementById('counter_client')?.value.trim();
            const date = document.getElementById('counter_date')?.value;

            if (!inv) {
                alert("Please specify a valid Invoice or Reference number.");
                document.getElementById('counter_invoice')?.focus();
                return false;
            }
            if (!client) {
                alert("Please provide the Customer / Client name.");
                document.getElementById('counter_client')?.focus();
                return false;
            }

            const submitBtn = document.getElementById('counter-submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = "Processing...";
            }

            const form = document.getElementById('counterOrderForm');
            const formData = new FormData(form);
            formData.append('type', currentCounterMode);
            formData.append('invoice', inv);
            formData.append('client_supplier', client);
            formData.append('log_date', date);
            formData.append('ajax', '1');

            counterItemsCart.forEach(i => {
                formData.append('item_category_arr[]', i.catSlug);
                formData.append('item_desc_arr[]', i.item);
                formData.append('item_qty_arr[]', i.qty);
                formData.append('item_warranty_arr[]', i.warranty);
            });

            if (!navigator.onLine) {
                queueTicketOffline(formData, inv, client, currentCounterMode, submitBtn);
                return false;
            }

            fetch('./', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast("Transaction saved successfully!", "success");
                    counterItemsCart = [];
                    renderCounterTicket();
                    document.getElementById('counter_invoice').value = '';
                    document.getElementById('counter_client').value = '';
                    const freebieContainer = document.querySelector('#counterOrderForm .freebie-container');
                    if (freebieContainer) freebieContainer.innerHTML = '';
                    broadcastAppMutation('TICKET_COMMITTED');
                    triggerLiveUpdateNow(true);
                } else {
                    alert(data.message || "Failed to process transaction.");
                }
            })
            .catch(err => {
                console.warn("Network error submitting transaction, queueing offline:", err);
                queueTicketOffline(formData, inv, client, currentCounterMode, submitBtn);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = "Process & Finalize Transaction";
                }
            });

            return false;
        }

        // --- UPGRADED HISTORY & SEARCH ENGINE (ADMIN ONLY) ---
        function filterAdminTable() {
            const query = (document.getElementById('adminSearch')?.value || '').toLowerCase().trim();
            const catFilter = (document.getElementById('historyCatFilter')?.value || '').toLowerCase().trim();
            const typeFilter = window.currentHistoryTypeFilter || 'ALL';
            const startDate = document.getElementById('historyStartDate')?.value || '';
            const endDate = document.getElementById('historyEndDate')?.value || '';

            const clearBtn = document.getElementById('clearHistorySearch');
            const countBadge = document.getElementById('history-search-count');
            if (clearBtn) {
                if (query.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            // Split search query into whitespace-delimited terms for universal multi-word search
            const terms = query ? query.split(/\s+/).filter(Boolean) : [];
            const isSearching = terms.length > 0;

            const parentContainers = document.querySelectorAll('.ledger-parent-container');
            let totalMatchingBatches = 0;
            let totalMatchingLines = 0;

            parentContainers.forEach(container => {
                const groupId = container.getAttribute('data-group-id');
                const parentInvoice = (container.getAttribute('data-invoice') || '').toLowerCase();
                const parentClient = (container.getAttribute('data-client') || '').toLowerCase();
                const parentDate = (container.getAttribute('data-date') || '').toLowerCase();
                const parentType = (container.getAttribute('data-group-type') || '').toLowerCase();
                
                // Parent header text (includes badge text, formatted date, timestamps, etc.)
                const headerEl = container.querySelector('.bg-slate-900\\/90') || container;
                const parentHeaderText = (headerEl ? headerEl.textContent : '').toLowerCase();

                const itemRows = container.querySelectorAll('.ledger-item-row');
                let matchingChildCount = 0;

                itemRows.forEach(row => {
                    const rowText = (row.textContent || '').toLowerCase();
                    const rowId = (row.getAttribute('data-id') || '').toLowerCase();
                    const rowDate = (row.getAttribute('data-date') || '').toLowerCase();
                    const rowType = (row.getAttribute('data-type') || '').toLowerCase();
                    const rowCat = (row.getAttribute('data-category') || '').toLowerCase();
                    const rowCatName = (row.getAttribute('data-cat-name') || '').toLowerCase();
                    const rowInvoice = (row.getAttribute('data-invoice') || '').toLowerCase();
                    const rowItem = (row.getAttribute('data-item') || '').toLowerCase();
                    const rowQty = (row.getAttribute('data-qty') || '').toLowerCase();
                    const rowClient = (row.getAttribute('data-client') || '').toLowerCase();
                    const rowWarranty = (row.getAttribute('data-warranty') || '').toLowerCase();

                    // Combined universal search blob covering ALL possible transaction attributes:
                    // Client, Supplier, Invoice, Description, Category, Sys ID (#211 or 211), Type, Date, Qty, Warranty, Header text
                    const searchBlob = `${parentClient} ${rowClient} ${parentInvoice} ${rowInvoice} ${rowItem} ${rowCat} ${rowCatName} #${rowId} ${rowId} ${rowType} ${parentType} ${rowDate} ${parentDate} ${rowQty} ${rowWarranty} ${rowText} ${parentHeaderText}`;

                    // Universal multi-token matching: all search terms must match somewhere in this record's search blob
                    let matchesQuery = true;
                    if (isSearching) {
                        for (let i = 0; i < terms.length; i++) {
                            if (!searchBlob.includes(terms[i])) {
                                matchesQuery = false;
                                break;
                            }
                        }
                    }

                    const matchesCat = !catFilter || rowCat === catFilter || rowCatName.includes(catFilter);
                    const matchesType = (typeFilter === 'ALL') || (rowType.toUpperCase() === typeFilter.toUpperCase());
                    const matchesDate = (!startDate || rowDate >= startDate) && (!endDate || rowDate <= endDate);

                    if (matchesQuery && matchesCat && matchesType && matchesDate) {
                        row.classList.remove('hidden');
                        matchingChildCount++;
                        totalMatchingLines++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                const childGroup = groupId ? document.getElementById(groupId) : null;
                const icon = groupId ? document.getElementById('icon-' + groupId) : null;

                if (matchingChildCount > 0) {
                    container.classList.remove('hidden');
                    totalMatchingBatches++;

                    // Auto-expand accordion so user sees the items & details immediately when searching!
                    if (isSearching) {
                        if (childGroup) childGroup.classList.remove('hidden');
                        if (icon) icon.classList.add('rotate-90');
                    }
                } else {
                    container.classList.add('hidden');
                    if (isSearching && childGroup) {
                        childGroup.classList.add('hidden');
                        if (icon) icon.classList.remove('rotate-90');
                    }
                }
            });

            if (countBadge) {
                if (isSearching || catFilter || typeFilter !== 'ALL' || startDate || endDate) {
                    countBadge.innerText = `${totalMatchingBatches} Batches (${totalMatchingLines} Lines)`;
                    countBadge.classList.remove('hidden');
                } else {
                    countBadge.classList.add('hidden');
                }
            }
        }

        function clearHistorySearch() {
            const input = document.getElementById('adminSearch');
            if (input) {
                input.value = '';
                filterAdminTable();
                // When cleared, collapse back auto-expanded groups
                document.querySelectorAll('.ledger-parent-container').forEach(container => {
                    const groupId = container.getAttribute('data-group-id');
                    if (groupId) {
                        const childGroup = document.getElementById(groupId);
                        const icon = document.getElementById('icon-' + groupId);
                        if (childGroup) childGroup.classList.add('hidden');
                        if (icon) icon.classList.remove('rotate-90');
                    }
                });
                input.focus();
            }
        }

        function setHistoryTypeFilter(type, btn) {
            window.currentHistoryTypeFilter = type;
            document.querySelectorAll('.history-type-pill').forEach(b => {
                b.classList.remove('bg-brand-600', 'text-white', 'border-brand-500');
                b.classList.add('bg-slate-900', 'text-slate-400', 'border-slate-800');
            });
            if (btn) {
                btn.classList.remove('bg-slate-900', 'text-slate-400', 'border-slate-800');
                btn.classList.add('bg-brand-600', 'text-white', 'border-brand-500');
            }
            filterAdminTable();
        }

        function setHistoryDatePreset(preset) {
            const startInput = document.getElementById('historyStartDate');
            const endInput = document.getElementById('historyEndDate');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            if (preset === 'today') {
                startInput.value = todayStr;
                endInput.value = todayStr;
            } else if (preset === 'week') {
                const lastWeek = new Date();
                lastWeek.setDate(lastWeek.getDate() - 7);
                const w_yyyy = lastWeek.getFullYear();
                const w_mm = String(lastWeek.getMonth() + 1).padStart(2, '0');
                const w_dd = String(lastWeek.getDate()).padStart(2, '0');
                startInput.value = `${w_yyyy}-${w_mm}-${w_dd}`;
                endInput.value = todayStr;
            } else if (preset === 'month') {
                startInput.value = `${yyyy}-${mm}-01`;
                endInput.value = todayStr;
            } else if (preset === 'all') {
                startInput.value = '';
                endInput.value = '';
            }

            filterAdminTable();
        }

        function toggleLedgerGroup(groupId) {
            const childGroup = document.getElementById(groupId);
            const icon = document.getElementById('icon-' + groupId);
            if (!childGroup) return;

            if (childGroup.classList.contains('hidden')) {
                childGroup.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-90');
            } else {
                childGroup.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-90');
            }
        }

        function toggleDashboardCategory(slug) {
            const content = document.getElementById('dash-content-' + slug);
            const icon = document.getElementById('dash-icon-' + slug);
            if (!content) return;

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        }

        function filterDashboard() {
            const query = (document.getElementById('dashboardSearch')?.value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.dashboard-category-card');
            const clearBtn = document.getElementById('clearDashSearch');
            const countBadge = document.getElementById('dash-search-count');
            let totalMatchCount = 0;

            if (clearBtn) {
                if (query.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            const terms = query ? query.split(/\s+/).filter(Boolean) : [];
            const isSearching = terms.length > 0;

            cards.forEach(card => {
                const slug = card.getAttribute('data-cat') || '';
                const catName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const rows = card.querySelectorAll('.dashboard-item-row');
                const content = document.getElementById('dash-content-' + slug);
                const icon = document.getElementById('dash-icon-' + slug);
                let matchCount = 0;

                rows.forEach(row => {
                    const itemName = row.querySelector('.item-name')?.textContent.toLowerCase() || '';
                    const rowText = (row.textContent || '').toLowerCase();
                    const combinedBlob = `${catName} ${itemName} ${rowText}`;

                    let matchesQuery = true;
                    if (isSearching) {
                        for (let i = 0; i < terms.length; i++) {
                            if (!combinedBlob.includes(terms[i])) {
                                matchesQuery = false;
                                break;
                            }
                        }
                    }

                    if (matchesQuery) {
                        row.classList.remove('hidden');
                        matchCount++;
                        totalMatchCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                if (matchCount > 0 || !isSearching) {
                    card.classList.remove('hidden');
                    // Auto-expand card if user is searching and items match
                    if (isSearching && content && content.classList.contains('hidden')) {
                        content.classList.remove('hidden');
                        if (icon) icon.classList.add('rotate-180');
                    }
                } else {
                    card.classList.add('hidden');
                }
            });

            if (countBadge) {
                if (isSearching) {
                    countBadge.innerText = `${totalMatchCount} lines found`;
                    countBadge.classList.remove('hidden');
                } else {
                    countBadge.classList.add('hidden');
                }
            }
        }

        function clearDashboardSearch() {
            const input = document.getElementById('dashboardSearch');
            if (input) {
                input.value = '';
                filterDashboard();
                input.focus();
            }
        }

        // --- MASTER CATALOG ADMIN SEARCH FILTER ---
        function filterCatalogAdmin() {
            const query = (document.getElementById('catalogAdminSearch')?.value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.admin-category-card');
            const clearBtn = document.getElementById('clearAdminSearch');
            let totalMatchingLines = 0;

            if (clearBtn) {
                if (query.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            const terms = query ? query.split(/\s+/).filter(Boolean) : [];
            const isSearching = terms.length > 0;

            cards.forEach(card => {
                const catTitle = (card.querySelector('.admin-cat-title')?.textContent || '').toLowerCase();
                const rows = card.querySelectorAll('.admin-item-row');
                let cardTitleMatches = isSearching && terms.every(t => catTitle.includes(t));
                let matchedRowsInCard = 0;

                rows.forEach(row => {
                    const inputVal = (row.querySelector('.admin-item-input')?.value || '').toLowerCase();
                    const combinedBlob = `${catTitle} ${inputVal}`;

                    let matchesQuery = true;
                    if (isSearching) {
                        if (cardTitleMatches) {
                            matchesQuery = true;
                        } else {
                            matchesQuery = terms.every(t => combinedBlob.includes(t));
                        }
                    }

                    if (matchesQuery) {
                        row.classList.remove('hidden');
                        matchedRowsInCard++;
                        totalMatchingLines++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                if (!isSearching || cardTitleMatches || matchedRowsInCard > 0) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        function clearCatalogAdminSearch() {
            const input = document.getElementById('catalogAdminSearch');
            if (input) {
                input.value = '';
                filterCatalogAdmin();
                input.focus();
            }
        }

        function editProductLineFromDashboard(catSlug, itemName) {
            if (!window.IS_ADMIN) return;

            // Clear any active search filter on Catalog Admin
            const adminSearch = document.getElementById('catalogAdminSearch');
            if (adminSearch && adminSearch.value) {
                adminSearch.value = '';
                filterCatalogAdmin();
            }

            // Switch to Master Catalog Admin tab
            showTab('tab-admin');

            // Wait for tab DOM rendering and layout
            setTimeout(() => {
                const catCard = document.querySelector(`.admin-category-card[data-cat="${catSlug}"]`);
                if (!catCard) return;

                // Ensure category card is visible
                catCard.classList.remove('hidden');

                // Search for the target item row inside the card
                const rows = catCard.querySelectorAll('.admin-item-row');
                let targetRow = null;
                let targetInput = null;

                const searchTarget = (itemName || '').trim().toLowerCase();
                rows.forEach(r => {
                    const inp = r.querySelector('input[name="item_name[]"]');
                    if (inp && inp.value.trim().toLowerCase() === searchTarget) {
                        targetRow = r;
                        targetInput = inp;
                    }
                });

                if (targetRow) {
                    targetRow.classList.remove('hidden');

                    // Smoothly scroll target row into center view
                    targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Visual ring pulse highlight animation
                    targetRow.classList.add('ring-2', 'ring-brand-500', 'bg-brand-950/40', 'rounded-2xl', 'transition-all');
                    setTimeout(() => {
                        targetRow.classList.remove('ring-2', 'ring-brand-500', 'bg-brand-950/40');
                    }, 2500);

                    // Focus & select item name input
                    if (targetInput) {
                        targetInput.focus();
                        targetInput.select();
                    }
                } else {
                    catCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 80);
        }

        // --- WARRANTY MANAGEMENT & CLIENT FLEET ENGINE ---
        function toggleWarrantyClient(cid) {
            const body = document.getElementById('warr-body-' + cid);
            const icon = document.getElementById('warr-icon-' + cid);
            if (!body) return;

            if (body.classList.contains('hidden')) {
                body.classList.remove('hidden');
                if (icon) icon.classList.remove('rotate-180');
            } else {
                body.classList.add('hidden');
                if (icon) icon.classList.add('rotate-180');
            }
        }

        function switchWarrantyView(mode) {
            const clientsView = document.getElementById('warranty-clients-view');
            const tableView = document.getElementById('warranty-table-view');
            const btnClients = document.getElementById('btn-warr-view-clients');
            const btnTable = document.getElementById('btn-warr-view-table');

            if (mode === 'clients') {
                if (clientsView) clientsView.classList.remove('hidden');
                if (tableView) tableView.classList.add('hidden');
                if (btnClients) {
                    btnClients.className = "px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white shadow-md transition-all cursor-pointer";
                }
                if (btnTable) {
                    btnTable.className = "px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-white transition-all cursor-pointer";
                }
            } else {
                if (clientsView) clientsView.classList.add('hidden');
                if (tableView) tableView.classList.remove('hidden');
                if (btnTable) {
                    btnTable.className = "px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white shadow-md transition-all cursor-pointer";
                }
                if (btnClients) {
                    btnClients.className = "px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-white transition-all cursor-pointer";
                }
            }
        }

        function filterWarranty() {
            const query = (document.getElementById('warrantySearch')?.value || '').toLowerCase().trim();
            const clearBtn = document.getElementById('clearWarrantySearch');

            if (clearBtn) {
                if (query.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            const terms = query ? query.split(/\s+/).filter(Boolean) : [];
            const isSearching = terms.length > 0;

            // 1. Filter Client Accordion Cards
            const clientCards = document.querySelectorAll('.warranty-client-card');
            clientCards.forEach(card => {
                const clientName = (card.getAttribute('data-client') || '').toLowerCase();
                const machineCards = card.querySelectorAll('.warranty-machine-card');
                let cardMatches = isSearching && terms.every(t => clientName.includes(t));
                let matchedMachinesInCard = 0;

                machineCards.forEach(mCard => {
                    const machineName = (mCard.getAttribute('data-machine') || '').toLowerCase();
                    const inv = (mCard.getAttribute('data-invoice') || '').toLowerCase();
                    const mText = (mCard.textContent || '').toLowerCase();
                    const combined = `${clientName} ${machineName} ${inv} ${mText}`;

                    let matchesQuery = true;
                    if (isSearching) {
                        if (cardMatches) {
                            matchesQuery = true;
                        } else {
                            matchesQuery = terms.every(t => combined.includes(t));
                        }
                    }

                    if (matchesQuery) {
                        mCard.classList.remove('hidden');
                        matchedMachinesInCard++;
                    } else {
                        mCard.classList.add('hidden');
                    }
                });

                const body = card.querySelector('.warr-client-content');
                const icon = card.querySelector('[id^="warr-icon-"]');

                if (!isSearching || cardMatches || matchedMachinesInCard > 0) {
                    card.classList.remove('hidden');
                    if (isSearching && body) {
                        body.classList.remove('hidden');
                        if (icon) icon.classList.remove('rotate-180');
                    }
                } else {
                    card.classList.add('hidden');
                }
            });

            // 2. Filter Master Table Rows
            const tableRows = document.querySelectorAll('.warranty-item-row');
            tableRows.forEach(row => {
                const text = (row.textContent || '').toLowerCase();
                let matchesQuery = true;
                if (isSearching) {
                    matchesQuery = terms.every(t => text.includes(t));
                }

                if (matchesQuery) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function clearWarrantySearch() {
            const input = document.getElementById('warrantySearch');
            if (input) {
                input.value = '';
                filterWarranty();
                input.focus();
            }
        }

        // --- WARRANTY CLAIM REPLACEMENT PART MODAL ENGINE ---
        function openWarrantyClaimModal(client, machineDesc, machineInvoice, expiryDate, defaultCat) {
            const modal = document.getElementById('warrantyClaimModal');
            if (!modal) return;

            const dispClient = document.getElementById('wclaim_client_display');
            const dispMachine = document.getElementById('wclaim_machine_display');
            const dispInv = document.getElementById('wclaim_inv_display');
            const dispExpiry = document.getElementById('wclaim_expiry_display');

            if (dispClient) dispClient.innerText = client || '--';
            if (dispMachine) dispMachine.innerText = machineDesc || '--';
            if (dispInv) dispInv.innerText = machineInvoice || '--';
            if (dispExpiry) dispExpiry.innerText = expiryDate || '--';

            const inClient = document.getElementById('wclaim_client_input');
            const inInv = document.getElementById('wclaim_machine_inv_input');
            const inDesc = document.getElementById('wclaim_machine_desc_input');

            if (inClient) inClient.value = client || '';
            if (inInv) inInv.value = machineInvoice || '';
            if (inDesc) inDesc.value = machineDesc || '';

            const catSelect = document.getElementById('wclaim_part_category');
            if (catSelect && window.SYSTEM_CATEGORIES) {
                catSelect.innerHTML = '';
                for (const [slug, cat] of Object.entries(window.SYSTEM_CATEGORIES)) {
                    const opt = document.createElement('option');
                    opt.value = slug;
                    opt.textContent = cat.name || slug;
                    if (defaultCat && slug === defaultCat) opt.selected = true;
                    catSelect.appendChild(opt);
                }
            }

            updateWarrantyModalPartOptions();
            modal.classList.remove('hidden');
        }

        function closeWarrantyClaimModal() {
            const modal = document.getElementById('warrantyClaimModal');
            if (modal) modal.classList.add('hidden');
        }

        function updateWarrantyModalPartOptions() {
            const catSelect = document.getElementById('wclaim_part_category');
            const partSelect = document.getElementById('wclaim_part_desc');
            if (!catSelect || !partSelect) return;

            const catSlug = catSelect.value;
            const items = window.SYSTEM_CATALOG ? (window.SYSTEM_CATALOG[catSlug] || []) : [];

            partSelect.innerHTML = '';
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item;
                opt.textContent = item;
                partSelect.appendChild(opt);
            });

            updateWarrantyModalStockBadge();
        }

        function updateWarrantyModalStockBadge() {
            const catSelect = document.getElementById('wclaim_part_category');
            const partSelect = document.getElementById('wclaim_part_desc');
            const badge = document.getElementById('wclaim_stock_badge');
            if (!catSelect || !partSelect || !badge) return;

            const catSlug = catSelect.value;
            const item = partSelect.value;

            let balance = 0;
            if (window.CURRENT_STOCKS && window.CURRENT_STOCKS[catSlug] && window.CURRENT_STOCKS[catSlug][item]) {
                balance = window.CURRENT_STOCKS[catSlug][item]['balance'] || 0;
            }

            badge.innerText = `Available: ${balance} units`;
            if (balance <= 0) {
                badge.className = "text-[10px] font-bold text-red-400 bg-red-950/80 px-2 py-0.5 rounded border border-red-800/80";
            } else if (balance <= 5) {
                badge.className = "text-[10px] font-bold text-yellow-400 bg-yellow-950/80 px-2 py-0.5 rounded border border-yellow-800/80";
            } else {
                badge.className = "text-[10px] font-bold text-emerald-400 bg-emerald-950/80 px-2 py-0.5 rounded border border-emerald-800/80";
            }
        }

        function handleWarrantyClaimSubmit(e, form) {
            if (e) e.preventDefault();
            const submitBtn = document.getElementById('wclaim_submit_btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span>Processing Claim...</span>`;
            }

            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('./', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    closeWarrantyClaimModal();
                    showToast("Warranty replacement part stocked out successfully!", "success");
                    broadcastAppMutation('WARRANTY_CLAIM_COMMITTED');
                    triggerLiveUpdateNow(true);
                } else {
                    alert(data.message || "Failed to process warranty claim.");
                }
            })
            .catch(err => {
                console.warn("Warranty claim error:", err);
                closeWarrantyClaimModal();
                showToast("Warranty claim logged.", "success");
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `<span>Stock Out Free Part</span>`;
                }
            });

            return false;
        }

        // --- PDF & CSV EXPORT SUITE (ADMIN ONLY) ---
        function generatePDFReport() {
            const startDate = document.getElementById('historyStartDate')?.value || '';
            const endDate = document.getElementById('historyEndDate')?.value || '';

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');

            doc.setFontSize(18);
            doc.setTextColor(249, 115, 22);
            doc.text("Al Rish Al Khaleeji", 14, 20);

            doc.setFontSize(11);
            doc.setTextColor(148, 163, 184);
            doc.text("Executive Inventory Transaction Report", 14, 27);

            const dateInfo = (startDate && endDate) ? `Date Range: ${startDate} to ${endDate}` : `Generated: ${new Date().toLocaleDateString()}`;
            doc.setFontSize(9);
            doc.setTextColor(100, 116, 139);
            doc.text(dateInfo, 14, 34);

            let pdfData = [];
            const visibleRows = document.querySelectorAll('.ledger-item-row:not(.hidden)');

            visibleRows.forEach(row => {
                let id = row.getAttribute("data-id") || '';
                let date = row.getAttribute("data-date") || '';
                let type = row.getAttribute("data-type") || '';
                let invoice = row.getAttribute("data-invoice") || '-';
                let item = row.getAttribute("data-item") || '';
                let qty = row.getAttribute("data-qty") || '0';
                let client = row.getAttribute("data-client") || '-';

                pdfData.push([id, date, type, invoice, item, qty, client]);
            });

            if (pdfData.length === 0) {
                alert("No visible records match the current filters.");
                return;
            }

            doc.autoTable({
                startY: 40,
                head: [['ID', 'Date', 'Type', 'Invoice #', 'Item Description', 'Qty', 'Client / Supplier']],
                body: pdfData,
                theme: 'striped',
                headStyles: { fillColor: [234, 88, 12], textColor: 255, fontStyle: 'bold' },
                styles: { fontSize: 8, cellPadding: 3, font: 'helvetica' },
                alternateRowStyles: { fillColor: [248, 250, 252] }
            });

            const fileName = `AlRish_Ledger_${startDate || 'All'}_to_${endDate || 'Latest'}.pdf`;
            doc.save(fileName);
        }

        function exportLedgerCSV() {
            let csv = "System ID,Date,Type,Invoice,Item Description,Quantity,Client/Supplier\n";
            const visibleRows = document.querySelectorAll('.ledger-item-row:not(.hidden)');

            visibleRows.forEach(row => {
                let id = (row.getAttribute("data-id") || '').replace('#', '');
                let date = row.getAttribute("data-date") || '';
                let type = row.getAttribute("data-type") || '';
                let invoice = `"${(row.getAttribute("data-invoice") || '').replace(/"/g, '""')}"`;
                let item = `"${(row.getAttribute("data-item") || '').replace(/"/g, '""')}"`;
                let qty = row.getAttribute("data-qty") || '0';
                let client = `"${(row.getAttribute("data-client") || '').replace(/"/g, '""')}"`;

                csv += `${id},${date},${type},${invoice},${item},${qty},${client}\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `AlRish_Ledger_${Date.now()}.csv`;
            a.click();
        }

        // --- TOAST NOTIFICATIONS ---
        function showToast(msg, type = "success") {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto px-5 py-3.5 rounded-2xl shadow-2xl font-bold text-sm flex items-center gap-3 transition-all duration-300 transform translate-y-2 opacity-0 ${
                type === 'success' ? 'bg-emerald-600 text-white border border-emerald-400/50' : 'bg-red-600 text-white border border-red-400/50'
            }`;
            toast.innerHTML = `<span>${msg}</span>`;
            container.appendChild(toast);

            setTimeout(() => toast.classList.remove('translate-y-2', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // --- CATEGORY ICON PICKER ENGINE ---
        function selectCategoryIcon(modalType, iconKey) {
            const inputId = modalType === 'add' ? 'add_cat_icon' : 'edit_cat_icon';
            const pickerId = modalType === 'add' ? 'add_icon_picker' : 'edit_icon_picker';
            
            const input = document.getElementById(inputId);
            if (input) input.value = iconKey;

            const picker = document.getElementById(pickerId);
            if (picker) {
                picker.querySelectorAll('.cat-icon-opt').forEach(btn => {
                    if (btn.getAttribute('data-icon') === iconKey) {
                        btn.className = "cat-icon-opt flex flex-col items-center justify-center p-2 rounded-xl border-2 border-brand-500 bg-brand-950/60 text-brand-400 shadow-md transition-all cursor-pointer";
                    } else {
                        btn.className = "cat-icon-opt flex flex-col items-center justify-center p-2 rounded-xl border border-slate-800 bg-slate-900/60 text-slate-400 hover:border-slate-700 hover:bg-slate-800 transition-all cursor-pointer";
                    }
                });
            }
        }

        // --- MODAL UTILITIES ---
        function openEditModal(id, type, date, invoice, client, item, qty, warranty) {
            document.getElementById('edit_log_id').value = id;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_log_date').value = date;
            document.getElementById('edit_invoice').value = invoice;
            document.getElementById('edit_client_supplier').value = client;
            document.getElementById('edit_item_desc').innerHTML = `<option value="${item}">${item}</option>`;
            document.getElementById('edit_quantity').value = qty;
            document.getElementById('edit_warranty').value = warranty;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openEditCategoryModal(slug, name, icon) {
            document.getElementById('edit_cat_slug').value = slug;
            document.getElementById('edit_cat_name').value = name;
            selectCategoryIcon('edit', icon || 'box');
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }

        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }

        function handleAsyncFormSubmit(e, form) {
            if (e) e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            if (!navigator.onLine) {
                queueFormOffline(form, formData);
                return false;
            }

            fetch('./', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    closeEditModal();
                    closeEditCategoryModal();
                    const addModal = document.getElementById('addCategoryModal');
                    if (addModal) addModal.classList.add('hidden');
                    const catNameInput = document.getElementById('add_cat_name');
                    if (catNameInput) catNameInput.value = '';

                    const adminAction = formData.get('admin_action');
                    const catSlug = formData.get('category');
                    if (adminAction === 'bulk_category_update' && catSlug) {
                        markAdminFormClean(catSlug);
                        const tbody = document.getElementById('admin-tbody-' + catSlug);
                        if (tbody) {
                            adminFormSnapshots[catSlug] = tbody.innerHTML;
                        }
                    }

                    showToast("Changes saved successfully!", "success");
                    broadcastAppMutation('FORM_SUBMITTED');
                    triggerLiveUpdateNow(true);
                } else {
                    alert(data.message || "Action failed.");
                }
            })
            .catch(err => {
                console.warn("Network error during form submit, queueing offline:", err);
                queueFormOffline(form, formData);
            });
            return false;
        }

        function handleAsyncDeleteSubmit(e, form) {
            if (e) e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            if (!navigator.onLine) {
                queueFormOffline(form, formData);
                return false;
            }

            fetch('./', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                showToast("Record deleted permanently.", "success");
                broadcastAppMutation('DELETE_COMMITTED');
                triggerLiveUpdateNow(true);
            })
            .catch(err => {
                console.warn("Network error during delete submit, queueing offline:", err);
                queueFormOffline(form, formData);
            });
            return false;
        }

        const adminFormSnapshots = {};

        function captureAdminSnapshots() {
            document.querySelectorAll('[id^="admin-tbody-"]').forEach(tbody => {
                const slug = tbody.id.replace('admin-tbody-', '');
                const actions = document.getElementById('admin-actions-' + slug);
                if (!actions || actions.classList.contains('hidden') || !adminFormSnapshots[slug]) {
                    adminFormSnapshots[slug] = tbody.innerHTML;
                }
            });
        }

        function addAdminRow(cat) {
            const tbody = document.getElementById('admin-tbody-' + cat);
            if (!tbody) return;

            if (!adminFormSnapshots[cat]) {
                adminFormSnapshots[cat] = tbody.innerHTML;
            }

            const div = document.createElement('div');
            div.className = "admin-item-row p-2.5 md:px-0 grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-3 items-center hover:bg-slate-800/20 transition-colors";
            div.innerHTML = `
                <div class="md:col-span-7">
                    <input type="hidden" name="old_name[]" value="">
                    <input type="text" name="item_name[]" oninput="markAdminFormDirty('${cat}')" placeholder="New Product Line Name" aria-label="Product Line Name" class="w-full bg-slate-950 border border-slate-700/80 focus:border-brand-500 rounded-xl px-3 py-2 text-xs sm:text-sm text-white outline-none transition-all admin-item-input" required>
                </div>
                <div class="md:col-span-3 flex justify-between md:justify-center items-center">
                    <span class="text-xs text-slate-500 font-bold md:hidden">Stock:</span>
                    <input type="number" name="item_stock[]" value="0" min="0" oninput="markAdminFormDirty('${cat}')" aria-label="Stock Balance" class="w-24 bg-slate-950 border border-slate-700/80 rounded-xl px-2 py-1.5 text-center text-xs sm:text-sm font-bold text-white focus:outline-none focus:border-brand-500 font-mono" required>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="button" onclick="this.closest('.admin-item-row').remove(); markAdminFormDirty('${cat}');" aria-label="Remove Line" class="p-2 text-slate-500 hover:text-red-400 transition-colors cursor-pointer">
                        <?= addslashes(icon('trash', 'w-4 h-4')) ?>
                    </button>
                </div>
            `;
            tbody.appendChild(div);
            markAdminFormDirty(cat);
            const input = div.querySelector('input[name="item_name[]"]');
            if (input) input.focus();
        }

        function markAdminFormDirty(slug) {
            const tbody = document.getElementById('admin-tbody-' + slug);
            if (tbody && !adminFormSnapshots[slug]) {
                adminFormSnapshots[slug] = tbody.innerHTML;
            }
            const actions = document.getElementById('admin-actions-' + slug);
            if (actions) actions.classList.remove('hidden');
        }

        function markAdminFormClean(slug) {
            const actions = document.getElementById('admin-actions-' + slug);
            if (actions) actions.classList.add('hidden');
        }

        function revertAdminForm(cat) {
            const tbody = document.getElementById('admin-tbody-' + cat);
            if (tbody) {
                if (adminFormSnapshots[cat] !== undefined) {
                    tbody.innerHTML = adminFormSnapshots[cat];
                } else {
                    tbody.querySelectorAll('.admin-item-row').forEach(row => {
                        const oldInput = row.querySelector('input[name="old_name[]"]');
                        const nameInput = row.querySelector('input[name="item_name[]"]');
                        const stockInput = row.querySelector('input[name="item_stock[]"]');
                        if (oldInput) {
                            if (oldInput.value === "") {
                                row.remove();
                            } else {
                                if (nameInput) nameInput.value = oldInput.value;
                                if (stockInput) {
                                    stockInput.value = stockInput.getAttribute('value') || stockInput.defaultValue || '0';
                                }
                            }
                        }
                    });
                }
            }
            markAdminFormClean(cat);
            if (typeof filterCatalogAdmin === 'function') {
                filterCatalogAdmin();
            }
            showToast("Form reverted.", "success");
        }

        function addFreebieRow(btn) {
            const wrapper = btn.closest('.freebie-wrapper');
            const container = wrapper.querySelector('.freebie-container');
            const tmpl = document.getElementById('freebie-template').content.cloneNode(true);
            container.appendChild(tmpl);
        }

        // --- DOM INITIALIZER & PWA OFFLINE PERSISTENCE ---
        document.addEventListener('DOMContentLoaded', () => {
            let savedTab = localStorage.getItem('activeInventoryTab') || 'tab-dashboard';
            showTab(savedTab);

            // Capture Baseline Clean Snapshots for Admin Forms
            captureAdminSnapshots();

            // Setup Default Add Category Icon Picker Active State
            selectCategoryIcon('add', 'box');

            if (window.history.replaceState) {
                const url = new URL(window.location);
                if (url.searchParams.has('msg')) {
                    url.searchParams.delete('msg');
                    window.history.replaceState({}, document.title, url.toString());
                }
            }

            // Register PWA Service Worker for full offline caching
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('sw.js', { scope: './' }).catch(() => {});
            }

            // Hydrate state from localStorage cache immediately
            try {
                const cachedStocks = localStorage.getItem('alrish_cached_stocks');
                if (cachedStocks) {
                    window.CURRENT_STOCKS = JSON.parse(cachedStocks);
                    applyOptimisticStocksToDom();
                }
                const cachedCat = localStorage.getItem('alrish_cached_catalog');
                if (cachedCat && (!window.SYSTEM_CATALOG || Object.keys(window.SYSTEM_CATALOG).length === 0)) {
                    window.SYSTEM_CATALOG = JSON.parse(cachedCat);
                }
                const cachedCats = localStorage.getItem('alrish_cached_categories');
                if (cachedCats && (!window.SYSTEM_CATEGORIES || Object.keys(window.SYSTEM_CATEGORIES).length === 0)) {
                    window.SYSTEM_CATEGORIES = JSON.parse(cachedCats);
                }
            } catch(e) {}

            try {
                if (window.CURRENT_STOCKS) localStorage.setItem('alrish_cached_stocks', JSON.stringify(window.CURRENT_STOCKS));
                if (window.SYSTEM_CATALOG) localStorage.setItem('alrish_cached_catalog', JSON.stringify(window.SYSTEM_CATALOG));
                if (window.SYSTEM_CATEGORIES) localStorage.setItem('alrish_cached_categories', JSON.stringify(window.SYSTEM_CATEGORIES));
            } catch(e) {}

            // Initialize Real-Time Event-Driven Push Stream (SSE)
            initRealtimeEventStream();

            // Auto sync & reconnect event stream when connection is restored
            window.addEventListener('online', () => {
                initRealtimeEventStream();
                processOfflineQueue();
            });

            window.addEventListener('offline', () => {
                if (realtimeEventSource) {
                    try { realtimeEventSource.close(); } catch(e) {}
                    realtimeEventSource = null;
                }
            });

            // Initial sync check on launch if online
            if (navigator.onLine && getOfflineOutbox().length > 0) {
                processOfflineQueue();
            }

            // Fallback sync interval
            setInterval(() => {
                if (navigator.onLine) {
                    triggerLiveUpdateNow(false);
                    if (getOfflineOutbox().length > 0 && !window.IS_SYNCING) {
                        processOfflineQueue();
                    }
                }
            }, 6000);
        });
    </script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans flex flex-col md:flex-row h-screen overflow-hidden select-none">

    <!-- Freebie Template -->
    <template id="freebie-template">
        <div class="freebie-row grid grid-cols-1 md:grid-cols-12 gap-2 items-center mt-2.5 pt-2.5 border-t border-slate-800">
            <div class="md:col-span-8">
                <select name="freebie_data[]" aria-label="Freebie Product Selection" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-brand-500">
                    <option value="">Select Freebie Item</option>
                    <?php foreach ($categories_db as $f_slug => $f_cat): ?>
                        <optgroup label="<?= htmlspecialchars($f_cat['name']) ?>">
                            <?php foreach($catalog[$f_slug] as $item): ?>
                               <option value="<?= $f_slug ?>|<?= htmlspecialchars($item) ?>"><?= htmlspecialchars($item) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-3">
                <input type="number" name="freebie_qty[]" min="1" value="1" placeholder="Qty" aria-label="Freebie Quantity" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white text-center font-mono focus:border-brand-500">
            </div>
            <div class="md:col-span-1">
                <button type="button" onclick="this.closest('.freebie-row').remove()" aria-label="Remove Freebie" class="w-full p-2 bg-red-950/60 hover:bg-red-900 border border-red-800/80 text-red-300 rounded-xl flex items-center justify-center transition-colors cursor-pointer">
                    <?= icon('close', 'w-4 h-4') ?>
                </button>
            </div>
        </div>
    </template>

    <!-- Sidebar Navigation (Desktop Only) -->
    <aside class="hidden md:flex w-64 bg-slate-900 flex-col border-r border-slate-800 shrink-0 z-30 shadow-2xl">
        <!-- Brand Header with Full Clean Logo -->
        <div class="h-16 flex items-center px-4 bg-slate-950 border-b border-slate-800/80 shrink-0">
            <div class="w-10 h-10 rounded-xl bg-white p-0.5 border border-slate-700/80 shadow-md flex items-center justify-center mr-3 shrink-0 overflow-hidden">
                <img src="logo.png" alt="Al Rish Logo" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                <span class="hidden"><?= icon('box', 'w-6 h-6 text-brand-500') ?></span>
            </div>
            <div class="truncate">
                <span class="text-sm font-extrabold text-white tracking-tight truncate block">Al Rish Al Khaleeji</span>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">Inventory System</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 py-6 px-3 space-y-1.5 overflow-y-auto custom-scrollbar">
            
            <button id="btn-desktop-tab-dashboard" onclick="showTab('tab-dashboard')" class="tab-btn-desktop w-full flex items-center px-4 py-3 rounded-2xl transition-all hover:bg-slate-800/80 font-bold gap-3 text-slate-400 cursor-pointer">
                <?= icon('dashboard', 'w-5 h-5') ?>
                <span class="text-sm whitespace-nowrap">Dashboard</span>
            </button>

            <button id="btn-desktop-tab-counter" onclick="showTab('tab-counter')" class="tab-btn-desktop w-full flex items-center px-4 py-3 rounded-2xl transition-all hover:bg-slate-800/80 font-bold gap-3 text-slate-400 cursor-pointer">
                <?= icon('cart', 'w-5 h-5') ?>
                <span class="text-sm whitespace-nowrap">POS Counter</span>
            </button>

            <button id="btn-desktop-tab-history" onclick="showTab('tab-history')" class="tab-btn-desktop w-full flex items-center px-4 py-3 rounded-2xl transition-all hover:bg-slate-800/80 font-bold gap-3 text-slate-400 cursor-pointer">
                <?= icon('history', 'w-5 h-5') ?>
                <span class="text-sm whitespace-nowrap">Global History</span>
            </button>

            <button id="btn-desktop-tab-warranty" onclick="showTab('tab-warranty')" class="tab-btn-desktop w-full flex items-center px-4 py-3 rounded-2xl transition-all hover:bg-slate-800/80 font-bold gap-3 text-slate-400 cursor-pointer">
                <?= icon('shield', 'w-5 h-5') ?>
                <span class="text-sm whitespace-nowrap">Warranty</span>
            </button>

            <button id="btn-desktop-tab-alerts" onclick="showTab('tab-alerts')" class="tab-btn-desktop w-full flex items-center px-4 py-3 rounded-2xl transition-all hover:bg-slate-800/80 font-bold gap-3 text-slate-400 relative cursor-pointer">
                <?= icon('bell', 'w-5 h-5') ?>
                <span class="text-sm whitespace-nowrap">Alerts</span>
                <span id="desktop-alerts-badge" class="<?= count($low_stock_alerts) > 0 ? '' : 'hidden' ?> bg-brand-500 text-white text-[10px] font-bold px-2 py-0.5 ml-auto rounded-full">
                    <?= count($low_stock_alerts) ?>
                </span>
            </button>

            <?php if ($is_admin): ?>
                <div class="my-3 border-t border-slate-800/80"></div>
                <button id="btn-desktop-tab-admin" onclick="showTab('tab-admin')" class="tab-btn-desktop w-full flex items-center px-4 py-3 rounded-2xl transition-all hover:bg-slate-800/80 font-bold gap-3 text-slate-400 cursor-pointer">
                    <?= icon('settings', 'w-5 h-5') ?>
                    <span class="text-sm whitespace-nowrap">Catalog Admin</span>
                </button>
            <?php endif; ?>

        </nav>

        <!-- Sidebar Footer / Adminer Link -->
        <?php if ($is_admin): ?>
        <div class="flex flex-col p-4 border-t border-slate-800/80 mt-auto bg-slate-950/50 space-y-2">
            <a href="adminer.php" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors">
                <?= icon('database', 'w-4 h-4 text-brand-500') ?>
                <span>Adminer DB Manager</span>
            </a>
            <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-[11px] text-slate-500">
                <span>Driver: <strong class="text-slate-400 uppercase"><?= Database::getActiveDriver() ?></strong></span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
        </div>
        <?php endif; ?>
    </aside>

    <!-- Main Application Viewport -->
    <main class="flex-1 flex flex-col relative overflow-y-auto custom-scrollbar">
        <!-- Top Sticky Header (Clean Responsive & Full Edge Logo) -->
        <header class="h-16 bg-slate-900/95 backdrop-blur-md flex items-center px-3 sm:px-6 border-b border-slate-800 sticky top-0 z-30 justify-between shrink-0 shadow-lg">
            <div class="flex items-center gap-2.5 sm:gap-3 truncate">
                <div class="w-9 h-9 rounded-xl bg-white p-0.5 border border-slate-700/80 shadow-md flex items-center justify-center shrink-0 overflow-hidden md:hidden">
                    <img src="logo.png" alt="Logo" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <span class="hidden"><?= icon('box', 'w-5 h-5 text-brand-500') ?></span>
                </div>
                <div class="truncate">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-extrabold text-white tracking-tight truncate">Al Rish Al Khaleeji</span>
                        <?php if ($is_admin): ?>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-950/80 text-purple-400 border border-purple-800/80 uppercase tracking-wider">Admin</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700 uppercase tracking-wider">Staff</span>
                        <?php endif; ?>
                    </div>
                    <span class="text-[10px] text-slate-400 hidden sm:block md:hidden">Stock Control Terminal</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="?logout=true" aria-label="Logout" class="bg-red-950/60 hover:bg-red-900 border border-red-800/80 text-red-300 text-xs font-bold py-1.5 px-3 sm:py-2 sm:px-3.5 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                    <?= icon('logout', 'w-3.5 h-3.5') ?>
                    <span class="hidden sm:inline">Logout</span>
                </a>
            </div>
        </header>

        <!-- App Body Content -->
        <div class="p-3 sm:p-5 md:p-8 pb-28 md:pb-16 max-w-7xl mx-auto w-full space-y-5 sm:space-y-6">

            <!-- 1. DASHBOARD TAB -->
            <section id="tab-dashboard" class="tab-content space-y-4 sm:space-y-5">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-1 gap-2">
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-white flex items-center gap-2">
                            Detailed Stock Balance
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Real-time stock on hand and inbound/outbound totals across all catalog categories.</p>
                    </div>
                </div>

                <!-- STICKY FIXED SEARCH BAR (Pinned while scrolling down) -->
                <div class="sticky top-16 z-20 bg-slate-950/95 backdrop-blur-xl py-2.5 px-3 sm:px-4 rounded-2xl border border-slate-800 shadow-xl flex items-center justify-between gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <?= icon('search', 'w-4 h-4') ?>
                        </div>
                        <input type="text" id="dashboardSearch" oninput="filterDashboard()" onkeyup="filterDashboard()" placeholder="Search product lines or categories..." aria-label="Search product lines or categories" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-10 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500 transition-colors">
                        <button type="button" id="clearDashSearch" onclick="clearDashboardSearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-white cursor-pointer" aria-label="Clear search">
                            <?= icon('close', 'w-4 h-4') ?>
                        </button>
                    </div>
                    <div id="dash-search-count" class="hidden text-xs text-brand-400 font-bold shrink-0"></div>
                </div>
                
                <div id="dashboard-cards-container">
                    <?= renderDashboardCards($categories_db, $catalog, $pdo, $is_admin) ?>
                </div>
            </section>

            <!-- 2. POS COUNTER TERMINAL TAB -->
            <section id="tab-counter" class="tab-content hidden space-y-4 sm:space-y-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-3 sm:pb-4 border-b border-slate-800 gap-3">
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-white flex items-center gap-2">
                            POS Counter Terminal
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Build and process multi-product Client Sales or Supplier Receipts under a unified reference.</p>
                    </div>
                    <div id="ticket-mode-badge" class="px-3 py-1 text-xs font-bold rounded-full bg-brand-950/80 text-brand-400 border border-brand-800/80 uppercase tracking-wider self-start sm:self-auto">
                        Client Sale Ticket
                    </div>
                </div>

                <!-- Mode Selector Bar -->
                <div class="bg-slate-900 p-1.5 sm:p-2 rounded-2xl border border-slate-800 flex gap-2">
                    <button type="button" id="btn-mode-sale" onclick="setCounterMode('SALE')" class="flex-1 py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm bg-brand-600 text-white shadow-lg shadow-brand-600/20 border border-brand-500 transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer">
                        <?= icon('outbound', 'w-4 h-4') ?>
                        <span>Stock Out (Sale)</span>
                    </button>
                    <button type="button" id="btn-mode-order" onclick="setCounterMode('ORDER')" class="flex-1 py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm bg-slate-900 text-slate-400 border border-slate-800 hover:text-white transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer">
                        <?= icon('inbound', 'w-4 h-4') ?>
                        <span>Stock In (Order)</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
                    <!-- Left: Header & Item Launcher -->
                    <div class="lg:col-span-5 space-y-4 sm:space-y-6">
                        <!-- Transaction Info Form -->
                        <div class="bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-800 shadow-xl space-y-3.5 sm:space-y-4">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800 pb-2">1. Transaction Details</h3>
                            
                            <div>
                                <label for="counter_date" class="text-xs text-slate-400 font-semibold block mb-1">Transaction Date</label>
                                <input type="date" id="counter_date" value="<?= date('Y-m-d') ?>" required aria-label="Transaction Date" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="counter_invoice" class="text-xs text-slate-400 font-semibold block">Invoice / Reference #</label>
                                    <button type="button" onclick="generateAutoInvoice()" class="text-[11px] font-bold text-brand-400 hover:text-brand-300 cursor-pointer">Generate #</button>
                                </div>
                                <input type="text" id="counter_invoice" required placeholder="e.g. INV-20260918-1001" aria-label="Invoice Reference Number" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-200 focus:outline-none focus:border-brand-500">
                            </div>

                            <div>
                                <label id="lbl-client-supplier" for="counter_client" class="text-xs text-slate-400 font-semibold block mb-1">Client / Customer Name</label>
                                <div class="relative">
                                    <input type="text" id="counter_client" required autocomplete="off" oninput="handleClientAutocomplete(this)" onkeydown="handleClientAutocompleteKey(event)" onfocus="handleClientAutocomplete(this)" placeholder="e.g. Al Faisal Trading" aria-label="Client or Supplier Name" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                                    <div id="counter_client_suggestions" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-slate-900 border border-slate-700/90 rounded-2xl shadow-2xl z-40 max-h-56 overflow-y-auto custom-scrollbar p-1.5 space-y-1"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Selector -->
                        <div class="bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-800 shadow-xl space-y-3.5 sm:space-y-4">
                            <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">2. Add Product Line</h3>
                                <span id="counter_stock_badge" class="px-2.5 py-0.5 text-xs font-bold rounded-lg bg-emerald-950/80 text-emerald-400 border border-emerald-800/80">On Hand: 0 units</span>
                            </div>

                            <!-- Live Unified Fast Search Bar -->
                            <div>
                                <label for="counter_product_search" class="text-xs text-slate-400 font-semibold flex items-center justify-between mb-1">
                                    <span class="flex items-center gap-1.5 text-slate-300">
                                        <?= icon('search', 'w-3.5 h-3.5 text-brand-500') ?> Quick Fast Search
                                    </span>
                                    <span class="text-[10px] text-brand-400 font-semibold">Across all models & parts</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="counter_product_search" autocomplete="off" oninput="handlePosProductSearch(this)" onkeydown="handlePosProductSearchKey(event)" onfocus="handlePosProductSearch(this)" placeholder="Type machine model or spare part (e.g. Bruno, i3200)..." aria-label="Quick fast product search" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl pl-3.5 pr-8 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500 placeholder:text-slate-600">
                                    <button type="button" id="clearPosProdSearch" onclick="clearPosProductSearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-white cursor-pointer" aria-label="Clear product search">
                                        <?= icon('close', 'w-3.5 h-3.5') ?>
                                    </button>
                                    <div id="pos_product_search_dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-slate-900 border border-slate-700/90 rounded-2xl shadow-2xl z-40 max-h-64 overflow-y-auto custom-scrollbar p-1.5 space-y-1"></div>
                                </div>
                            </div>

                            <div class="relative flex py-1 items-center">
                                <div class="flex-grow border-t border-slate-800"></div>
                                <span class="flex-shrink mx-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">or browse category</span>
                                <div class="flex-grow border-t border-slate-800"></div>
                            </div>

                            <div>
                                <label for="counter_cat" class="text-xs text-slate-400 font-semibold block mb-1">Select Category / Machine Model</label>
                                <select id="counter_cat" onchange="updateCounterProductOptions()" aria-label="Product Category" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 font-medium focus:border-brand-500">
                                    <?php foreach ($categories_db as $slug => $cat_info): ?>
                                        <option value="<?= $slug ?>"><?= htmlspecialchars($cat_info['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="counter_item" class="text-xs text-slate-400 font-semibold block mb-1">Select Product / Machine Line</label>
                                <select id="counter_item" onchange="handleCounterProductChange()" aria-label="Product Line Item" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 font-semibold focus:border-brand-500">
                                    <!-- Populated by JS -->
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                <div>
                                    <label for="counter_qty" class="text-xs text-slate-400 font-semibold block mb-1">Quantity</label>
                                    <div class="flex items-center">
                                        <button type="button" onclick="adjustCounterQty(-1)" aria-label="Decrease Quantity" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-3 py-2.5 rounded-l-2xl border border-slate-700 text-sm cursor-pointer">-</button>
                                        <input type="number" id="counter_qty" min="1" value="1" inputmode="numeric" aria-label="Quantity" class="w-full bg-slate-950 border-t border-b border-slate-700 text-center text-sm font-bold text-white py-2.5 focus:outline-none font-mono">
                                        <button type="button" onclick="adjustCounterQty(1)" aria-label="Increase Quantity" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-3 py-2.5 rounded-r-2xl border border-slate-700 text-sm cursor-pointer">+</button>
                                    </div>
                                </div>

                                <div id="counter_warranty_container">
                                    <label for="counter_warranty" class="text-xs text-slate-400 font-semibold block mb-1">Warranty (Mos)</label>
                                    <input type="number" id="counter_warranty" min="0" value="0" placeholder="0 = None" inputmode="numeric" aria-label="Warranty in Months" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 focus:border-brand-500 font-mono">
                                </div>
                            </div>

                            <button type="button" onclick="addItemToCounterTicket()" class="w-full bg-slate-800 hover:bg-slate-750 text-white font-bold py-3.5 rounded-2xl border border-slate-700 shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer mt-2">
                                <?= icon('plus-circle', 'w-4 h-4 text-brand-500') ?>
                                <span>Add to Order Ticket</span>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Ticket Table & Execution -->
                    <div class="lg:col-span-7 flex flex-col space-y-4 sm:space-y-6">
                        <div class="bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-800 shadow-xl flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center border-b border-slate-800 pb-3 mb-3 sm:pb-4 sm:mb-4">
                                    <h3 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                                        Active Order Ticket
                                    </h3>
                                    <div class="flex gap-2 text-xs font-bold">
                                        <span id="ticket-total-lines" class="bg-slate-950 px-2.5 py-1 rounded-xl text-brand-400 border border-slate-800">0 Items</span>
                                        <span id="ticket-total-units" class="bg-slate-950 px-2.5 py-1 rounded-xl text-emerald-400 border border-slate-800 font-mono">0 Units</span>
                                    </div>
                                </div>

                                <!-- Empty Placeholder -->
                                <div id="ticket-empty" class="py-12 sm:py-16 text-center border-2 border-dashed border-slate-800 rounded-2xl my-3">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-950 flex items-center justify-center text-slate-600 mb-3">
                                        <?= icon('cart', 'w-6 h-6') ?>
                                    </div>
                                    <p class="text-slate-400 font-medium text-xs sm:text-sm">Order ticket is currently empty.</p>
                                    <p class="text-[11px] sm:text-xs text-slate-600 mt-1">Select product lines from the launcher on the left to build this transaction.</p>
                                </div>

                                <!-- Table -->
                                <div id="ticket-table-wrapper" class="hidden max-h-[380px] overflow-y-auto custom-scrollbar mb-4">
                                    <div class="hidden md:grid grid-cols-12 gap-3 pb-2.5 px-3 border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider sticky top-0 bg-slate-900 z-10">
                                        <div class="col-span-6">Product & Category</div>
                                        <div class="col-span-2 text-center">Qty</div>
                                        <div class="col-span-3 text-center">Warranty</div>
                                        <div class="col-span-1 text-right pr-2">Action</div>
                                    </div>
                                    <div id="ticket-tbody" class="divide-y divide-slate-800/60">
                                        <!-- JS dynamic rendering -->
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Form Submission -->
                            <form id="counterOrderForm" method="POST" onsubmit="return processCounterTicketSubmit(event)" class="space-y-3 sm:space-y-4 pt-3 sm:pt-4 border-t border-slate-800">
                                <!-- Optional Freebies -->
                                <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl freebie-wrapper">
                                    <div class="flex justify-between items-center">
                                        <label class="text-xs font-bold text-purple-400 flex items-center gap-1.5">
                                            <?= icon('gift', 'w-4 h-4') ?> Attached Freebies
                                        </label>
                                        <button type="button" onclick="addFreebieRow(this)" class="bg-purple-950/80 hover:bg-purple-900 text-purple-300 border border-purple-800/80 text-xs px-2.5 py-1 rounded-xl font-bold transition-colors cursor-pointer">
                                            + Freebie
                                        </button>
                                    </div>
                                    <div class="freebie-container"></div>
                                </div>

                                <button type="submit" id="counter-submit-btn" disabled class="w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-600 disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed text-white font-bold py-4 rounded-2xl shadow-xl shadow-brand-600/20 text-sm md:text-base flex items-center justify-center gap-2 transition-all cursor-pointer">
                                    <?= icon('check', 'w-5 h-5') ?>
                                    <span>Process & Finalize Transaction</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. UPGRADED GLOBAL HISTORY & LEDGER TAB (READ-ONLY FOR STAFF, EDITABLE FOR ADMIN) -->
            <section id="tab-history" class="tab-content hidden space-y-4 sm:space-y-5">
                <!-- Header with Summary KPI Metrics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-slate-900 p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl border border-slate-800 shadow-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Logs</span>
                        <div class="flex items-baseline gap-2">
                            <span id="kpi-total-transactions" class="text-xl sm:text-2xl font-black text-white font-mono"><?= $total_transactions ?></span>
                            <span class="text-[10px] sm:text-xs text-slate-500">Batches</span>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl border border-slate-800 shadow-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-emerald-400 uppercase tracking-wider block mb-1">Inbound</span>
                        <div class="flex items-baseline gap-2">
                            <span id="kpi-total-inbound" class="text-xl sm:text-2xl font-black text-emerald-400 font-mono">+<?= $total_inbound ?></span>
                            <span class="text-[10px] sm:text-xs text-slate-500">Units</span>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl border border-slate-800 shadow-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-brand-400 uppercase tracking-wider block mb-1">Outbound</span>
                        <div class="flex items-baseline gap-2">
                            <span id="kpi-total-outbound" class="text-xl sm:text-2xl font-black text-brand-400 font-mono">-<?= $total_outbound ?></span>
                            <span class="text-[10px] sm:text-xs text-slate-500">Units</span>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl border border-slate-800 shadow-lg">
                        <span class="text-[10px] sm:text-xs font-bold text-blue-400 uppercase tracking-wider block mb-1">Warranties</span>
                        <div class="flex items-baseline gap-2">
                            <span id="kpi-active-warranties" class="text-xl sm:text-2xl font-black text-blue-400 font-mono"><?= $active_warranties_count ?></span>
                            <span class="text-[10px] sm:text-xs text-slate-500">Active</span>
                        </div>
                    </div>
                </div>

                <!-- STICKY FIXED HISTORY SEARCH BAR ONLY -->
                <div class="sticky top-16 z-20 bg-slate-950/95 backdrop-blur-xl py-2.5 px-3 sm:px-4 rounded-2xl border border-slate-800 shadow-xl flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2.5">
                    <!-- Search Box -->
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <?= icon('search', 'w-4 h-4') ?>
                        </div>
                        <input type="text" id="adminSearch" oninput="filterAdminTable()" onkeyup="filterAdminTable()" placeholder="Search Invoice, Client, Product, or Date..." aria-label="Search transaction ledger" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-10 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500 transition-colors">
                        <button type="button" id="clearHistorySearch" onclick="clearHistorySearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-white cursor-pointer" aria-label="Clear history search">
                            <?= icon('close', 'w-4 h-4') ?>
                        </button>
                    </div>

                    <div id="history-search-count" class="hidden text-xs text-brand-400 font-bold shrink-0 self-center"></div>

                    <!-- Category Dropdown Filter -->
                    <div class="w-full sm:w-56">
                        <select id="historyCatFilter" onchange="filterAdminTable()" aria-label="Filter by Category" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500 font-medium">
                            <option value="">All Categories</option>
                            <?php foreach ($categories_db as $slug => $cat_info): ?>
                                <option value="<?= $slug ?>"><?= htmlspecialchars($cat_info['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Non-Sticky Filter Controls & Export Toolbar (Scrolls normally with page) -->
                <div class="bg-slate-900 p-3 sm:p-4 rounded-2xl border border-slate-800 shadow-lg space-y-3">
                    <!-- Filter Pills & Date Range Toolbar -->
                    <div class="flex flex-col xl:flex-row justify-between items-stretch xl:items-center gap-2.5">
                        <!-- Transaction Type Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1 shrink-0">Type:</span>
                            <button type="button" onclick="setHistoryTypeFilter('ALL', this)" class="history-type-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-brand-600 text-white border border-brand-500 transition-all cursor-pointer shrink-0">All</button>
                            <button type="button" onclick="setHistoryTypeFilter('SALE', this)" class="history-type-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-950 text-slate-400 border border-slate-800 hover:text-white transition-all cursor-pointer shrink-0">Sales</button>
                            <button type="button" onclick="setHistoryTypeFilter('ORDER', this)" class="history-type-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-950 text-slate-400 border border-slate-800 hover:text-white transition-all cursor-pointer shrink-0">Orders In</button>
                            <button type="button" onclick="setHistoryTypeFilter('WARRANTY', this)" class="history-type-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-950 text-slate-400 border border-slate-800 hover:text-white transition-all cursor-pointer shrink-0">Warranty</button>
                            <button type="button" onclick="setHistoryTypeFilter('FREE', this)" class="history-type-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-950 text-slate-400 border border-slate-800 hover:text-white transition-all cursor-pointer shrink-0">Free Gifts</button>
                            <button type="button" onclick="setHistoryTypeFilter('SYS-ADJUST', this)" class="history-type-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-950 text-slate-400 border border-slate-800 hover:text-white transition-all cursor-pointer shrink-0">Adjustments</button>
                        </div>

                        <!-- Date Presets & Custom Pickers -->
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1">Date:</span>
                            <button type="button" onclick="setHistoryDatePreset('today')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-950 border border-slate-800 text-slate-300 hover:text-white cursor-pointer">Today</button>
                            <button type="button" onclick="setHistoryDatePreset('week')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-950 border border-slate-800 text-slate-300 hover:text-white cursor-pointer">7 Days</button>
                            <button type="button" onclick="setHistoryDatePreset('month')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-950 border border-slate-800 text-slate-300 hover:text-white cursor-pointer">Month</button>
                            <button type="button" onclick="setHistoryDatePreset('all')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-950 border border-slate-800 text-slate-300 hover:text-white cursor-pointer">All</button>
                            <input type="date" id="historyStartDate" onchange="filterAdminTable()" aria-label="History Start Date" class="bg-slate-950 border border-slate-700/80 rounded-xl px-2.5 py-1 text-xs text-slate-200">
                            <span class="text-slate-600 text-xs">to</span>
                            <input type="date" id="historyEndDate" onchange="filterAdminTable()" aria-label="History End Date" class="bg-slate-950 border border-slate-700/80 rounded-xl px-2.5 py-1 text-xs text-slate-200">
                        </div>
                    </div>

                    <!-- Export Actions Bar -->
                    <div class="pt-2 border-t border-slate-800/80 flex justify-between items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="generatePDFReport()" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl shadow-md transition-colors flex items-center gap-1.5 cursor-pointer">
                                <?= icon('download', 'w-3.5 h-3.5') ?> PDF Report
                            </button>
                            <button type="button" onclick="exportLedgerCSV()" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-bold px-3.5 py-1.5 rounded-xl transition-colors flex items-center gap-1.5 cursor-pointer">
                                <?= icon('file-text', 'w-3.5 h-3.5') ?> CSV
                            </button>
                        </div>
                        <?php if ($is_admin): ?>
                        <form method="POST" onsubmit="return confirm('WARNING: This will wipe all transaction history. Proceed?')" class="inline">
                            <input type="hidden" name="admin_action" value="reset">
                            <button type="submit" aria-label="Reset History" class="text-xs bg-red-950/60 hover:bg-red-900 border border-red-800/80 text-red-300 px-3 py-1.5 rounded-xl transition-colors font-semibold cursor-pointer">
                                Reset History
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ledger Records Stream -->
                <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
                    <div id="adminTableBody" class="divide-y divide-slate-800/80">
                        <?= renderLedgerTableBody($grouped_logs, $categories_db, $is_admin) ?>
                    </div>
                </div>
            </section>

            <!-- 4. WARRANTY TRACKING TAB -->
            <section id="tab-warranty" class="tab-content hidden space-y-4 sm:space-y-5">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-1 gap-2">
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-white flex items-center gap-2">
                            Warranty Management
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Track machine and parts warranty expiration periods and active guarantees.</p>
                    </div>
                </div>

                <!-- STICKY FIXED WARRANTY SEARCH BAR -->
                <div class="sticky top-16 z-20 bg-slate-950/95 backdrop-blur-xl py-2.5 px-3 sm:px-4 rounded-2xl border border-slate-800 shadow-xl flex items-center justify-between gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <?= icon('search', 'w-4 h-4') ?>
                        </div>
                        <input type="text" id="warrantySearch" oninput="filterWarranty()" onkeyup="filterWarranty()" placeholder="Search by Invoice, Client, or Item..." aria-label="Search warranties" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-10 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500 transition-colors">
                        <button type="button" id="clearWarrantySearch" onclick="clearWarrantySearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-white cursor-pointer" aria-label="Clear search">
                            <?= icon('close', 'w-4 h-4') ?>
                        </button>
                    </div>
                </div>

                <div id="warranty-container" class="space-y-4">
                    <?= renderWarrantySection($pdo) ?>
                </div>
            </section>

            <!-- 5. SYSTEM ALERTS TAB -->
            <section id="tab-alerts" class="tab-content hidden space-y-4 sm:space-y-6">
                <div class="border-b border-slate-800 pb-3 sm:pb-4">
                    <h1 class="text-lg sm:text-2xl font-bold text-white flex items-center gap-2">
                        System Stock Alerts
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Critical low-stock threshold notifications (<?= LOW_STOCK_THRESHOLD ?> or fewer units remaining).</p>
                </div>

                <div id="alerts-container">
                    <?= renderAlerts($low_stock_alerts) ?>
                </div>
            </section>

            <!-- 6. MASTER CATALOG ADMIN TAB (ADMIN ONLY) -->
            <?php if ($is_admin): ?>
            <section id="tab-admin" class="tab-content hidden space-y-4 sm:space-y-5">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-1 gap-2">
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-white">Master Inventory Catalog Manager</h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Manage product categories, rename catalog items, and conduct inline stock corrections.</p>
                    </div>
                </div>

                <!-- STICKY FIXED CATALOG SEARCH & ACTION BAR -->
                <div class="sticky top-16 z-20 bg-slate-950/95 backdrop-blur-xl py-2.5 px-3 sm:px-4 rounded-2xl border border-slate-800 shadow-xl flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2.5">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <?= icon('search', 'w-4 h-4') ?>
                        </div>
                        <input type="text" id="catalogAdminSearch" oninput="filterCatalogAdmin()" onkeyup="filterCatalogAdmin()" placeholder="Search product lines or categories to edit..." aria-label="Search master catalog" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-10 py-2.5 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-brand-500 transition-colors">
                        <button type="button" id="clearAdminSearch" onclick="clearCatalogAdminSearch()" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-white cursor-pointer" aria-label="Clear search">
                            <?= icon('close', 'w-4 h-4') ?>
                        </button>
                    </div>
                    <button type="button" onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0">
                        <?= icon('plus', 'w-4 h-4') ?>
                        <span>Add Category</span>
                    </button>
                </div>
                
                <div id="admin-categories-container">
                    <?= renderAdminCategoryCards($categories_db, $catalog, $pdo) ?>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </main>

    <!-- Bottom Navigation Bar for Mobile Phones (Strictly Guarded) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-40 bg-slate-900/95 backdrop-blur-xl border-t border-slate-800 py-1 px-1 pb-[calc(env(safe-area-inset-bottom)+0.35rem)] shadow-[0_-4px_20px_rgba(0,0,0,0.6)]">
        <div class="flex items-center justify-around">
            <button id="btn-mobile-tab-dashboard" onclick="showTab('tab-dashboard')" aria-label="Stock Dashboard" class="tab-btn-mobile flex-1 flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all font-semibold gap-1 text-slate-400 cursor-pointer">
                <?= icon('dashboard', 'w-5 h-5') ?>
                <span class="text-[10px] leading-tight">Stock</span>
            </button>

            <button id="btn-mobile-tab-counter" onclick="showTab('tab-counter')" aria-label="POS Counter" class="tab-btn-mobile flex-1 flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all font-semibold gap-1 text-slate-400 cursor-pointer">
                <?= icon('cart', 'w-5 h-5') ?>
                <span class="text-[10px] leading-tight">POS</span>
            </button>

            <button id="btn-mobile-tab-history" onclick="showTab('tab-history')" aria-label="Global History" class="tab-btn-mobile flex-1 flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all font-semibold gap-1 text-slate-400 cursor-pointer">
                <?= icon('history', 'w-5 h-5') ?>
                <span class="text-[10px] leading-tight">History</span>
            </button>

            <button id="btn-mobile-tab-warranty" onclick="showTab('tab-warranty')" aria-label="Warranty Tracking" class="tab-btn-mobile flex-1 flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all font-semibold gap-1 text-slate-400 cursor-pointer">
                <?= icon('shield', 'w-5 h-5') ?>
                <span class="text-[10px] leading-tight">Warranty</span>
            </button>

            <button id="btn-mobile-tab-alerts" onclick="showTab('tab-alerts')" aria-label="Stock Alerts" class="tab-btn-mobile flex-1 flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all font-semibold gap-1 text-slate-400 relative cursor-pointer">
                <span class="relative">
                    <?= icon('bell', 'w-5 h-5') ?>
                    <span id="mobile-alerts-badge" class="<?= count($low_stock_alerts) > 0 ? '' : 'hidden' ?> absolute -top-1 -right-2 bg-brand-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full">
                        <?= count($low_stock_alerts) ?>
                    </span>
                </span>
                <span class="text-[10px] leading-tight">Alerts</span>
            </button>

            <?php if ($is_admin): ?>
            <button id="btn-mobile-tab-admin" onclick="showTab('tab-admin')" aria-label="Catalog Admin" class="tab-btn-mobile flex-1 flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all font-semibold gap-1 text-slate-400 cursor-pointer">
                <?= icon('settings', 'w-5 h-5') ?>
                <span class="text-[10px] leading-tight">Admin</span>
            </button>
            <?php endif; ?>
        </div>
    </nav>

    <!-- ADMIN MODALS WITH VISUAL ICON PICKER -->
    <?php if ($is_admin): ?>
    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
        <div class="bg-slate-900 w-full max-w-lg rounded-3xl border border-slate-800 shadow-2xl p-5 sm:p-6 space-y-4 my-auto">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <?= icon('plus', 'w-5 h-5 text-emerald-500') ?>
                <span>Create New Category</span>
            </h3>
            <form method="POST" onsubmit="return handleAsyncFormSubmit(event, this)" class="space-y-4">
                <input type="hidden" name="admin_action" value="add_category">
                <div>
                    <label for="add_cat_name" class="text-xs font-semibold text-slate-400 block mb-1">Category Title</label>
                    <input type="text" id="add_cat_name" name="cat_name" required placeholder="e.g. Sublimation & Heat Press" aria-label="Category Title" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-400 block mb-1.5">Choose Category Icon</label>
                    <input type="hidden" name="cat_icon" id="add_cat_icon" value="box">
                    <div class="grid grid-cols-4 sm:grid-cols-4 gap-2 p-2.5 bg-slate-950 rounded-2xl border border-slate-800 max-h-44 overflow-y-auto custom-scrollbar" id="add_icon_picker">
                        <?php foreach (getAvailableCategoryIcons() as $iconKey => $iconLabel): ?>
                        <button type="button" onclick="selectCategoryIcon('add', '<?= $iconKey ?>')" aria-label="<?= htmlspecialchars($iconLabel) ?>" class="cat-icon-opt flex flex-col items-center justify-center p-2 rounded-xl border border-slate-800 bg-slate-900/60 text-slate-400 hover:border-slate-700 hover:bg-slate-800 transition-all cursor-pointer" data-icon="<?= $iconKey ?>" title="<?= htmlspecialchars($iconLabel) ?>">
                            <?= icon($iconKey, 'w-5 h-5') ?>
                            <span class="text-[9px] mt-1 truncate w-full text-center"><?= htmlspecialchars(explode('/', $iconLabel)[0]) ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-2xl text-xs font-bold text-slate-300 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-emerald-600/20 cursor-pointer">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
        <div class="bg-slate-900 w-full max-w-lg rounded-3xl border border-slate-800 shadow-2xl p-5 sm:p-6 space-y-4 my-auto">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <?= icon('edit', 'w-5 h-5 text-brand-500') ?>
                <span>Edit Category Details</span>
            </h3>
            <form method="POST" onsubmit="return handleAsyncFormSubmit(event, this)" class="space-y-4">
                <input type="hidden" name="admin_action" value="edit_category">
                <input type="hidden" name="cat_slug" id="edit_cat_slug">
                <div>
                    <label for="edit_cat_name" class="text-xs font-semibold text-slate-400 block mb-1">Category Title</label>
                    <input type="text" name="cat_name" id="edit_cat_name" required aria-label="Category Title" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-400 block mb-1.5">Choose Category Icon</label>
                    <input type="hidden" name="cat_icon" id="edit_cat_icon" value="box">
                    <div class="grid grid-cols-4 sm:grid-cols-4 gap-2 p-2.5 bg-slate-950 rounded-2xl border border-slate-800 max-h-44 overflow-y-auto custom-scrollbar" id="edit_icon_picker">
                        <?php foreach (getAvailableCategoryIcons() as $iconKey => $iconLabel): ?>
                        <button type="button" onclick="selectCategoryIcon('edit', '<?= $iconKey ?>')" aria-label="<?= htmlspecialchars($iconLabel) ?>" class="cat-icon-opt flex flex-col items-center justify-center p-2 rounded-xl border border-slate-800 bg-slate-900/60 text-slate-400 hover:border-slate-700 hover:bg-slate-800 transition-all cursor-pointer" data-icon="<?= $iconKey ?>" title="<?= htmlspecialchars($iconLabel) ?>">
                            <?= icon($iconKey, 'w-5 h-5') ?>
                            <span class="text-[9px] mt-1 truncate w-full text-center"><?= htmlspecialchars(explode('/', $iconLabel)[0]) ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-2xl text-xs font-bold text-slate-300 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-brand-600/20 cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Log Record Modal -->
    <div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
        <div class="bg-slate-900 w-full max-w-lg rounded-3xl border border-slate-800 shadow-2xl p-5 sm:p-6 space-y-4 my-auto">
            <h3 class="text-lg font-bold text-white">Edit Transaction Record</h3>
            <form method="POST" onsubmit="return handleAsyncFormSubmit(event, this)" class="space-y-4">
                <input type="hidden" name="admin_action" value="edit">
                <input type="hidden" name="log_id" id="edit_log_id">
       
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label for="edit_type" class="text-xs font-semibold text-slate-400 block mb-1">Transaction Type</label>
                        <select name="type" id="edit_type" aria-label="Transaction Type" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white">
                            <option value="SALE">SALE</option>
                            <option value="ORDER">ORDER</option>
                            <option value="WARRANTY">WARRANTY</option>
                            <option value="FREE">FREE</option>
                            <option value="SYS-ADJUST">SYS-ADJUST</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_log_date" class="text-xs font-semibold text-slate-400 block mb-1">Date</label>
                        <input type="date" name="log_date" id="edit_log_date" aria-label="Transaction Date" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white">
                    </div>
                    <div>
                        <label for="edit_invoice" class="text-xs font-semibold text-slate-400 block mb-1">Invoice / Ref #</label>
                        <input type="text" name="invoice" id="edit_invoice" aria-label="Invoice Number" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white font-mono">
                    </div>
                    <div>
                        <label for="edit_quantity" class="text-xs font-semibold text-slate-400 block mb-1">Quantity</label>
                        <input type="number" name="quantity" id="edit_quantity" aria-label="Quantity" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white font-mono">
                    </div>
                    <div class="col-span-2">
                        <label for="edit_client_supplier" class="text-xs font-semibold text-slate-400 block mb-1">Client / Supplier</label>
                        <input type="text" name="client_supplier" id="edit_client_supplier" aria-label="Client or Supplier" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white">
                    </div>
                    <div class="col-span-2">
                        <label for="edit_item_desc" class="text-xs font-semibold text-slate-400 block mb-1">Item Description</label>
                        <select name="item_desc" id="edit_item_desc" aria-label="Product Description" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white font-semibold"></select>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_warranty" class="text-xs font-semibold text-slate-400 block mb-1">Warranty (Months)</label>
                        <input type="number" name="warranty" id="edit_warranty" aria-label="Warranty in Months" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-sm text-white font-mono">
                    </div>
                </div>
     
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-2xl text-xs font-bold text-slate-300 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-brand-600/20 cursor-pointer">Save Record</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
 
    <!-- Warranty Claim Replacement Part Modal (Staff & Admin Accessible) -->
    <div id="warrantyClaimModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
        <div class="bg-slate-900 w-full max-w-lg rounded-3xl border border-blue-900/60 shadow-2xl p-5 sm:p-6 space-y-4 my-auto relative">
            <div class="flex justify-between items-start border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-blue-950/80 border border-blue-800/80 flex items-center justify-center text-blue-400 shrink-0 shadow-inner">
                        <?= icon('shield', 'w-5 h-5') ?>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white leading-tight">Order Parts Under Warranty</h3>
                        <p class="text-[11px] text-slate-400">Stock out a free replacement part covered under active warranty guarantee.</p>
                    </div>
                </div>
                <button type="button" onclick="closeWarrantyClaimModal()" class="p-1.5 text-slate-500 hover:text-white rounded-xl hover:bg-slate-800 transition-colors cursor-pointer">
                    <?= icon('close', 'w-4 h-4') ?>
                </button>
            </div>

            <!-- Machine & Customer Info Banner -->
            <div class="bg-slate-950 p-3.5 rounded-2xl border border-slate-800 space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">Client Name:</span>
                    <span id="wclaim_client_display" class="font-extrabold text-white text-sm">--</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">Machine Model:</span>
                    <span id="wclaim_machine_display" class="font-bold text-brand-400">--</span>
                </div>
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-slate-500 font-mono">Invoice: <strong id="wclaim_inv_display" class="text-slate-300">--</strong></span>
                    <span class="text-slate-500 font-mono">Valid Until: <strong id="wclaim_expiry_display" class="text-emerald-400">--</strong></span>
                </div>
            </div>

            <form id="warrantyClaimForm" method="POST" onsubmit="return handleWarrantyClaimSubmit(event, this)" class="space-y-3.5">
                <input type="hidden" name="admin_action" value="warranty_claim_part">
                <input type="hidden" name="client_name" id="wclaim_client_input">
                <input type="hidden" name="machine_invoice" id="wclaim_machine_inv_input">
                <input type="hidden" name="machine_desc" id="wclaim_machine_desc_input">

                <div>
                    <label for="wclaim_part_category" class="text-xs font-semibold text-slate-400 block mb-1">Part Category</label>
                    <select id="wclaim_part_category" name="part_category" onchange="updateWarrantyModalPartOptions()" required class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 font-medium focus:border-blue-500">
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="wclaim_part_desc" class="text-xs font-semibold text-slate-400 block">Replacement Part Item</label>
                        <span id="wclaim_stock_badge" class="text-[10px] font-bold text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">Stock: --</span>
                    </div>
                    <select id="wclaim_part_desc" name="part_desc" onchange="updateWarrantyModalStockBadge()" required class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-200 font-semibold focus:border-blue-500">
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="wclaim_qty" class="text-xs font-semibold text-slate-400 block mb-1">Quantity</label>
                        <input type="number" id="wclaim_qty" name="quantity" min="1" value="1" required class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2 text-xs sm:text-sm text-white font-mono text-center focus:border-blue-500">
                    </div>
                    <div>
                        <label for="wclaim_date" class="text-xs font-semibold text-slate-400 block mb-1">Stock-Out Date</label>
                        <input type="date" id="wclaim_date" name="log_date" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2 text-xs sm:text-sm text-slate-200 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label for="wclaim_notes" class="text-xs font-semibold text-slate-400 block mb-1">Reason / Issue Notes (Optional)</label>
                    <input type="text" id="wclaim_notes" name="claim_notes" placeholder="e.g. Damaged nozzle line replaced under warranty" class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2 text-xs text-slate-200 focus:border-blue-500">
                </div>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" onclick="closeWarrantyClaimModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-2xl text-xs font-bold text-slate-300 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="wclaim_submit_btn" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-blue-600/25 flex items-center gap-1.5 cursor-pointer">
                        <?= icon('check', 'w-4 h-4') ?>
                        <span>Stock Out Free Part</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>