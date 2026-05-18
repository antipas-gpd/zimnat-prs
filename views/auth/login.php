<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Zimnat PRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --zim-green: #1a6b3a; --zim-green-d: #134f2b; --zim-gold: #d4a017; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--zim-green-d) 0%, #1a6b3a 60%, #2e8b57 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
        }
        .login-logo {
            width: 56px; height: 56px;
            background: var(--zim-gold);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; font-weight: 800; color: #fff;
            margin: 0 auto 1rem;
        }
        .login-title { font-size: 1.4rem; font-weight: 700; color: #111827; text-align: center; }
        .login-sub { font-size: .82rem; color: #6b7280; text-align: center; margin-bottom: 1.75rem; }
        .form-label { font-weight: 500; font-size: .82rem; color: #374151; }
        .form-control {
            border-radius: 8px;
            border-color: #d1d5db;
            font-size: .875rem;
            padding: .6rem .9rem;
        }
        .form-control:focus {
            border-color: var(--zim-green);
            box-shadow: 0 0 0 3px rgba(26,107,58,.15);
        }
        .btn-login {
            background: var(--zim-green);
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: .65rem;
            font-weight: 600;
            font-size: .9rem;
            width: 100%;
            transition: background .2s;
        }
        .btn-login:hover { background: var(--zim-green-d); }
        .login-footer { text-align: center; font-size: .75rem; color: #9ca3af; margin-top: 1.5rem; }
        .input-group-text {
            background: #f9fafb;
            border-color: #d1d5db;
            border-radius: 0 8px 8px 0 !important;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">Z</div>
        <h1 class="login-title">Zimnat PRS</h1>
        <p class="login-sub">Policy Renewal Reminder System<br>Sign in to your account</p>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-sm py-2 px-3 mb-3" style="font-size:.82rem;border-radius:8px;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/doLogin" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="mb-3">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="you@zimnat.co.zw" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="••••••••" required style="border-radius:8px 0 0 8px;">
                    <span class="input-group-text" onclick="togglePwd()">
                        <i class="bi bi-eye" id="pwd-icon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        <div class="login-footer">
            &copy; <?= date('Y') ?> Zimnat Life Assurance Company Limited
        </div>
    </div>
</div>

<script>
function togglePwd() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('pwd-icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
