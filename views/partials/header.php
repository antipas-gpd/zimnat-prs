<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'PRS') ?> – Zimnat PRS</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --zim-green:   #1a6b3a;
            --zim-green-d: #134f2b;
            --zim-green-l: #e8f5ed;
            --zim-gold:    #d4a017;
            --sidebar-w:   250px;
            --header-h:    60px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
            margin: 0;
            font-size: 0.9rem;
        }

        /* ── Sidebar ─────────────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--zim-green-d);
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: transform .25s ease;
        }
        #sidebar .sidebar-brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            gap: .65rem;
            min-height: var(--header-h);
        }
        #sidebar .sidebar-brand img { width: 32px; }
        #sidebar .sidebar-brand span {
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -.3px;
        }
        #sidebar .sidebar-brand small {
            display: block;
            font-size: .65rem;
            font-weight: 400;
            opacity: .65;
            letter-spacing: .3px;
            text-transform: uppercase;
        }
        #sidebar nav { flex: 1; padding: .75rem 0; overflow-y: auto; }
        #sidebar .nav-section {
            padding: .4rem 1.25rem .2rem;
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: rgba(255,255,255,.4);
        }
        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .55rem 1.25rem;
            color: rgba(255,255,255,.78);
            border-radius: 0;
            text-decoration: none;
            transition: background .15s, color .15s;
            font-size: .875rem;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        #sidebar .nav-link.active {
            border-left: 3px solid var(--zim-gold);
        }
        #sidebar .nav-link i { width: 18px; text-align: center; }
        #sidebar .sidebar-footer {
            padding: .9rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.12);
            font-size: .8rem;
            color: rgba(255,255,255,.55);
        }

        /* ── Main content ────────────────────────────────── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #topbar {
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid #e3e6ea;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        #topbar .topbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1c1c2e;
        }
        #topbar .ms-auto { display: flex; align-items: center; gap: 1rem; }
        .user-badge {
            display: flex; align-items: center; gap: .5rem;
            background: var(--zim-green-l);
            color: var(--zim-green-d);
            padding: .3rem .75rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 500;
        }
        .role-pill {
            font-size: .65rem;
            font-weight: 600;
            padding: .15rem .5rem;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .role-admin          { background: #fef3c7; color: #92400e; }
        .role-policy_officer { background: #dbeafe; color: #1e40af; }
        .role-viewer         { background: #f3f4f6; color: #374151; }

        #page-content { flex: 1; padding: 1.5rem; }

        /* ── Cards / Stats ───────────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-card .icon-box {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: .75rem; color: #6b7280; margin-top: .15rem; }
        .bg-green-soft  { background: #d1fae5; color: #065f46; }
        .bg-blue-soft   { background: #dbeafe; color: #1e40af; }
        .bg-red-soft    { background: #fee2e2; color: #991b1b; }
        .bg-yellow-soft { background: #fef3c7; color: #92400e; }

        /* ── Tables ──────────────────────────────────────── */
        .data-table { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        .data-table thead th {
            background: #f9fafb;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding: .75rem 1rem;
            white-space: nowrap;
        }
        .data-table tbody td { padding: .75rem 1rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: #fafafa; }

        /* ── Status badges ───────────────────────────────── */
        .status-badge {
            font-size: .7rem; font-weight: 600;
            padding: .25rem .65rem;
            border-radius: 999px;
            display: inline-block;
            white-space: nowrap;
        }
        .status-Active          { background: #d1fae5; color: #065f46; }
        .status-Expired         { background: #fee2e2; color: #991b1b; }
        .status-Pending-Renewal { background: #fef3c7; color: #92400e; }

        /* ── Section cards ───────────────────────────────── */
        .section-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .section-card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex; align-items: center; justify-content: space-between;
        }
        .section-card-header h6 { margin: 0; font-weight: 600; font-size: .9rem; }
        .section-card-body { padding: 1.25rem; }

        /* ── Alerts ──────────────────────────────────────── */
        .flash-alert { border-radius: 10px; border: none; font-size: .875rem; }

        /* ── Forms ───────────────────────────────────────── */
        .form-label { font-weight: 500; font-size: .82rem; color: #374151; margin-bottom: .3rem; }
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #d1d5db;
            font-size: .875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--zim-green);
            box-shadow: 0 0 0 3px rgba(26,107,58,.15);
        }
        .btn-primary {
            background: var(--zim-green);
            border-color: var(--zim-green);
        }
        .btn-primary:hover { background: var(--zim-green-d); border-color: var(--zim-green-d); }
        .btn-outline-primary { color: var(--zim-green); border-color: var(--zim-green); }
        .btn-outline-primary:hover { background: var(--zim-green); border-color: var(--zim-green); }

        /* ── Page header ─────────────────────────────────── */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .page-header h4 { margin: 0; font-weight: 700; font-size: 1.1rem; color: #111827; }
        .page-header .breadcrumb { margin: 0; font-size: .78rem; }

        /* ── Mobile ──────────────────────────────────────── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #main { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar overlay for mobile -->
<div id="sidebar-overlay" onclick="toggleSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1040;"></div>

<!-- ── Sidebar ─────────────────────────────────────────── -->
<div id="sidebar">
    <div class="sidebar-brand">
        <div style="width:36px;height:36px;background:var(--zim-gold);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:#fff;flex-shrink:0;">Z</div>
        <div>
            <span>Zimnat PRS</span>
            <small>Policy Renewal System</small>
        </div>
    </div>

    <nav>
        <?php $cur = $segments[0] ?? ''; $curAction = $segments[1] ?? 'index'; ?>

        <div class="nav-section">Main</div>
        <a class="nav-link <?= $cur === 'dashboard' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section mt-1">Policies</div>
        <a class="nav-link <?= ($cur === 'policies') ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/policies/index">
            <i class="bi bi-file-earmark-text"></i> All Policies
        </a>
        <?php if (AuthMiddleware::hasRole('admin', 'policy_officer')): ?>
        <a class="nav-link <?= ($cur === 'policies' && $curAction === 'create') ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/policies/create">
            <i class="bi bi-plus-circle"></i> Add Policy
        </a>
        <?php endif; ?>

        <?php if (AuthMiddleware::hasRole('admin')): ?>
        <div class="nav-section mt-1">Administration</div>
        <a class="nav-link <?= $cur === 'users' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/users/index">
            <i class="bi bi-people"></i> Manage Users
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div style="font-weight:500;color:rgba(255,255,255,.8);margin-bottom:.2rem;">
            <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
        </div>
        <span class="role-pill role-<?= htmlspecialchars($_SESSION['user_role'] ?? '') ?>">
            <?= str_replace('_', ' ', htmlspecialchars($_SESSION['user_role'] ?? '')) ?>
        </span>
    </div>
</div>

<!-- ── Main ───────────────────────────────────────────────── -->
<div id="main">
    <div id="topbar">
        <button class="btn btn-sm btn-light d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="topbar-title"><?= htmlspecialchars($pageTitle ?? '') ?></span>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div id="page-content">
        <?php include ROOT . '/views/partials/flash.php'; ?>
