<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ChakaNok's - Login</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #fef250; /* Solid yellow background */
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
    .links {
      margin-top: 10px;
      font-size: 13px;
    }
    .links a {
      text-decoration: none;
      color: #1d4ed8;
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
