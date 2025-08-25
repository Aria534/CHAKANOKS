<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<<<<<<< HEAD
  <title>ChakaNok's - Login</title>
=======
  <title>Chakanok's - Login</title>
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
>>>>>>> a82a43f (Fixed login page and updated login form database)
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
<<<<<<< HEAD
      background: #fef250; /* Solid yellow background */
=======
      background: rgb(64, 64, 63);
>>>>>>> a82a43f (Fixed login page and updated login form database)
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .login-box {
      background: white;
      padding: 25px;
      border-radius: 12px;
      width: 320px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      text-align: center;
    }
    .login-box img {
      width: 120px;   /* adjust size */
      height: auto;
      margin-bottom: 10px;
    }
    h1 {
      margin-bottom: 15px;
      font-size: 20px;
      font-weight: bold;
      background: #ffffff;
      padding: 32px 28px;
      border-radius: 14px;
      width: 420px;
      box-shadow: 0 14px 40px rgba(0,0,0,0.28);
      text-align: left;
      border: 1px solid #e5e7eb;
    }
    .login-box img {
      width: 120px;
      height: auto;
      margin: 6px auto 12px;
      display: block;
    }
    h1 {
      margin: 8px 0 18px 0;
      font-size: 24px;
      font-weight: bold;
      color: #111827;

    }
    label {
      display: block;
      text-align: left;
      font-weight: bold;

      margin-top: 8px;
      margin-bottom: 4px;
      font-size: 14px;
      color: darkblue;
    }
    input {
      width: 100%;
      padding: 6px;
      margin-bottom: 10px;
      border: 1px solid #333;
      border-radius: 4px;
      font-size: 14px;
    }
    button {
      width: 100px;
      padding: 8px;
      background: #222;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      cursor: pointer;
    }
    button:hover {
      background: #4610daff;
    }
      margin-top: 10px;
      margin-bottom: 6px;
      font-size: 13px;
      color: #1f2937;
    }
    input {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 12px;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      font-size: 14px;
      background: #ffffff;
      transition: border-color .15s, box-shadow .15s;
      box-sizing: border-box;
    }
    input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
    }
    button {
      width: 100%;
      padding: 11px 14px;
      background: #111827;
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 8px 18px rgba(0,0,0,0.2);
      transition: background .15s, transform .05s;
    }
    button:hover { background: #1f2937; }
    button:active { transform: translateY(1px); }
    .links {
      margin-top: 10px;
      font-size: 13px;
    }
    .links a {
      text-decoration: none;

      color: #1d4ed8;
      color: #2563eb;
      margin: 0 3px;
    }
    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <!-- LOGO -->
    <img src="/images/logo.png" alt="ChakaNok's Logo">
    <h1>Welcome To ChakaNok's</h1>
    
    <form>
    <img src="<?= base_url('images/chakanoks-logo.jpg') ?>" alt="ChakaNok\'s Logo">
    <h1>Welcome To Chakanok's SCMS</h1>
    
    <form method="get" action="<?= site_url('dashboard') ?>">
      <label>Username</label>
      <input type="text" required>
      
      <label>Password</label>
      <input type="password" required>
      
      <br><br>
      <button type="submit">Login</button>
    </form>
    <div class="links">
      <p><a href="forgot-password.html">Forgot Password?</a></p>
    </div>
  </div>
</body>
</html>
