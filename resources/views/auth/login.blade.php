<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StreetPOS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin:0; padding:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #800000, #FFD700);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .auth-container {
            width: 100%;
            max-width: 380px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.3);
            border-top: 6px solid #FFD700;
        }
        h2 {
            text-align: center;
            color: #800000;
            margin-bottom: 20px;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        button {
            background: #800000;
            color: #fff;
            font-weight: bold;
            border: none;
            transition: 0.3s;
            cursor: pointer;
        }
        button:hover { background: #a00000; }
        .input-group { position: relative; }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 12px;
            cursor: pointer;
            color: #800000;
        }
        .switch-link {
            text-align: center;
            margin-top: 12px;
            font-size: 14px;
        }
        .switch-link a {
            color: #800000;
            font-weight: bold;
            text-decoration: none;
        }
        .switch-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="auth-container">
    <h2>🔑 Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <div class="input-group">
            <input type="password" name="password" id="loginPassword" placeholder="Password" required>
            <i class="fa fa-eye toggle-password" onclick="togglePassword('loginPassword', this)"></i>
        </div>
        <button type="submit">Login</button>
    </form>
    <div class="switch-link">
        Don’t have an account? <a href="{{ route('register') }}">Register here</a>
    </div>
</div>

<script>
    function togglePassword(inputId, el) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            el.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            el.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
</body>
</html>
