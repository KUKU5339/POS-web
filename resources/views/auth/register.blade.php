<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StreetPOS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin:0; padding:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #FFD700, #800000);
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
            border-top: 6px solid #800000;
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
            background: #FFD700;
            color: #800000;
            font-weight: bold;
            border: none;
            transition: 0.3s;
            cursor: pointer;
        }
        button:hover { background: #e6c200; }
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
    <h2>📝 Register</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <div class="input-group">
            <input type="password" name="password" id="registerPassword" placeholder="Password" required>
            <i class="fa fa-eye toggle-password" onclick="togglePassword('registerPassword', this)"></i>
        </div>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm Password" required>
            <i class="fa fa-eye toggle-password" onclick="togglePassword('confirmPassword', this)"></i>
        </div>
        <button type="submit">Register</button>
    </form>
    <div class="switch-link">
        Already have an account? <a href="{{ route('login') }}">Login here</a>
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
