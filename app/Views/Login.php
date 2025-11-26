<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ChakaNoks • Login</title>
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height:100vh; background: linear-gradient(135deg,#0f172a,#111827); display:flex; align-items:center; justify-content:center; }
    .card { width: 380px; border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.35); overflow:hidden; }
    .brand { text-align:center; padding-top: 18px; }
    .brand img { width:110px; height:auto; }
    .title { font-weight:800; color:#111827; }
    .muted { color:#6b7280; }
    .input-group-text { background:#f8fafc; }
    .toggle { cursor:pointer; user-select:none; }
  </style>
  <script>
    function togglePwd() {
      const el = document.getElementById('password');
      el.type = el.type === 'password' ? 'text' : 'password';
      const t = document.getElementById('toggleText');
      if (t) t.textContent = el.type === 'password' ? 'Show' : 'Hide';
    }
  </script>
  </head>
<body>
  <div class="card">
    <div class="brand">
      <img src="<?= base_url('Chakanoks logo.jpg') ?>" alt="ChakaNoks" onerror="this.style.display='none'">
    </div>
    <div class="card-body p-4">
      <h4 class="title mb-1 text-center">Welcome to SCMS</h4>
      <p class="muted mb-4 text-center">Sign in to continue</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('login') ?>" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label" for="username">Email or Username</label>
          <div class="input-group">
            <span class="input-group-text">@</span>
            <input type="text" class="form-control" id="username" name="username" placeholder="central@chakanoks.com" required autofocus>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label" for="password">Password</label>
          <div class="input-group">
            <span class="input-group-text">***</span>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            <button class="btn btn-outline-secondary toggle" type="button" onclick="togglePwd()" id="toggleText">Show</button>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" disabled>
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <a class="link-primary" href="#" onclick="return false;">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-dark w-100">Login</button>
      </form>
    </div>
    <div class="text-center pb-3">
      <small class="text-muted"> <?= date('Y') ?> ChakaNoks</small>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
