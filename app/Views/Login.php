<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chakanok's - Login</title>
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: rgb(64, 64, 63);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .login-box {
      background: white;
      padding: 40px;
      border-radius: 12px;
      width: 400px;
      box-shadow: 0 14px 40px rgba(0,0,0,0.28);
      text-align: center;
    }
    .login-box img {
      width: 120px;
      height: auto;
      margin: 0 auto 20px;
      display: block;
    }
    h1 {
      margin: 0 0 30px 0;
      font-size: 24px;
      font-weight: bold;
      color: #111827;
    }
    label {
      display: block;
      text-align: left;
      font-weight: bold;
      margin-top: 15px;
      margin-bottom: 8px;
      font-size: 14px;
      color: #374151;
    }
    input {
      width: 100%;
      padding: 12px 16px;
      margin-bottom: 15px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-size: 14px;
      background: #ffffff;
      transition: border-color .15s, box-shadow .15s;
      box-sizing: border-box;
    }
    input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
    }
    button {
      width: 100%;
      padding: 12px 16px;
      background: #111827;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transition: background .15s, transform .05s;
      margin-top: 10px;
    }
    button:hover { 
      background: #1f2937; 
    }
    button:active { 
      transform: translateY(1px); 
    }
    .links {
      margin-top: 20px;
      font-size: 13px;
    }
    .links a {
      text-decoration: none;
      color: #2563eb;
    }
    .links a:hover {
      text-decoration: underline;
    }
    .error-message {
      background: #fef2f2;
      color: #dc2626;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <img src="<?= base_url('Chakanoks logo.jpg') ?>" alt="Chakanok's Logo" onerror="this.style.display='none'">
    <h1>Welcome To Chakanok's SCMS</h1>
    
    <?php if (session()->getFlashdata('error')): ?>
      <div class="error-message">
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>
    
    <form method="post" action="<?= site_url('login') ?>">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required>
      
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      
      <button type="submit">Login</button>
    </form>
    
    <div class="links">
      <p><a href="#">Forgot Password?</a></p>
    </div>
  </div>
</body>
</html>
