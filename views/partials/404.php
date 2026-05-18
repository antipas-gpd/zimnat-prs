<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Not Found – Zimnat PRS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif;background:#f4f6f9;display:flex;align-items:center;justify-content:center;min-height:100vh;}</style>
</head>
<body>
<div class="text-center p-4">
    <div style="font-size:4rem;">🔍</div>
    <h3 class="mt-3 fw-bold">Page Not Found</h3>
    <p class="text-muted">The page you're looking for doesn't exist.</p>
    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary me-2">Go Back</a>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/dashboard" class="btn btn-sm btn-primary">Dashboard</a>
</div>
</body>
</html>
