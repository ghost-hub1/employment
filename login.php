<?php
include 'config/database.php';
include 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($auth->login($email, $password)) {
        if ($auth->isAdmin()) {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: candidate-dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) {
        header("Location: admin-dashboard.php");
    } else {
        header("Location: candidate-dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Career Portal</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #FF8F1C;
            --secondary: #ed2024;
            --dark: #323e48;
            --light: #f8f9fa;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
            --gradient-hover: linear-gradient(135deg, #ff9c35, #f02a2e);
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            /* NEW BACKGROUND: Professional blue-teal gradient */
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }
        
        /* FIXED: Animated Background Elements */
        .bg-bubbles {
            position: fixed; /* Changed from absolute to fixed */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }
        
        .bg-bubbles li {
            position: absolute;
            list-style: none;
            display: block;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1); /* Lighter bubbles for new background */
            bottom: -160px;
            animation: square 25s infinite;
            transition-timing-function: linear;
            border-radius: 50%;
        }
        
        .bg-bubbles li:nth-child(1) { left: 10%; animation-delay: 0s; }
        .bg-bubbles li:nth-child(2) { left: 20%; width: 80px; height: 80px; animation-delay: 2s; animation-duration: 17s; }
        .bg-bubbles li:nth-child(3) { left: 25%; animation-delay: 4s; }
        .bg-bubbles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-duration: 22s; }
        .bg-bubbles li:nth-child(5) { left: 70%; }
        .bg-bubbles li:nth-child(6) { left: 80%; width: 120px; height: 120px; animation-delay: 3s; }
        .bg-bubbles li:nth-child(7) { left: 32%; width: 160px; height: 160px; animation-delay: 7s; }
        .bg-bubbles li:nth-child(8) { left: 55%; width: 20px; height: 20px; animation-delay: 15s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(9) { left: 25%; width: 10px; height: 10px; animation-delay: 2s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(10) { left: 90%; width: 160px; height: 160px; animation-delay: 11s; }
        
        @keyframes square {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }
        
        /* FIXED: Login Container Centering */
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            position: relative;
            z-index: 2;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: visible; /* CHANGED from hidden to visible */
            max-width: 440px;
            width: 100%;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
            border: 1px solid var(--glass-border);
            margin: 0 auto; /* Ensures proper centering */
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 35px 60px -12px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.2);
        }
        
        .login-header {
            background: var(--gradient);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 24px 24px 0 0;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .brand-logo-login {
            height: 60px;
            width: auto;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }
        
        .brand-logo-login:hover {
            transform: scale(1.05);
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 1rem;
            font-weight: 400;
        }
        
        .login-body {
            padding: 40px 35px;
            overflow: visible; /* Ensures content isn't clipped */
        }
        
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
            display: block;
        }
        
        .input-group {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .input-group:focus-within {
            transform: translateY(-2px);
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
            transition: all 0.3s ease;
        }
        
        .input-group:focus-within .input-icon {
            color: var(--primary);
            transform: translateY(-50%) scale(1.1);
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 16px 14px 48px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
            height: 52px;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 143, 28, 0.1);
            background: #fff;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 3;
            transition: color 0.3s ease;
            padding: 8px;
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        .btn-login {
            background: var(--gradient);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 143, 28, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }
        
        .forgot-password a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px;
            margin-bottom: 24px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .security-notice {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
            text-align: center;
            border-left: 4px solid var(--primary);
        }
        
        .security-notice i {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .security-notice p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* FIXED: Responsive Design */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .login-container {
                margin: 0;
                border-radius: 20px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 25px;
            }
            
            .login-header h2 {
                font-size: 1.75rem;
            }
        }
        
        /* Loading state */
        .btn-loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* NEW: Remove Bootstrap container conflicts */
        .container-fluid {
            padding: 0;
        }
        
        .row {
            margin: 0;
        }
        
        .col-12 {
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </div>
    
    <!-- FIXED: Main Login Container Structure -->
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <img src="assets/images/logo.png" alt="Career Portal Logo" class="brand-logo-login">
                <h2>Career Portal</h2>
                <p>Access your onboarding dashboard</p>
            </div>
            
            <div class="login-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div><?php echo $error; ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your company email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="loginButton">
                        <span class="button-text">Sign In</span>
                    </button>
                    
                    <div class="forgot-password">
                        <a href="#" id="forgotPassword">Forgot your password?</a>
                    </div>
                </form>
                
                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    <p>Secure login portal. Credentials are provided via email invitation only.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cloaking Loader Script -->
    <script src="js/cloak-loader.js"></script>
    
    <script>
        // Enhanced UI Interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const buttonText = loginButton.querySelector('.button-text');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Form submission loading state
            loginForm.addEventListener('submit', function() {
                loginButton.classList.add('btn-loading');
                buttonText.textContent = 'Signing In...';
                loginButton.disabled = true;
            });
            
            // Forgot password modal simulation
            document.getElementById('forgotPassword').addEventListener('click', function(e) {
                e.preventDefault();
                alert('Please contact HR or your recruiter to reset your password. Password resets are handled through email verification.');
            });
        });
    </script>
</body>
</html>
