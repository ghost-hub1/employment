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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #FF8F1C;
            --secondary: #ed2024;
            --dark: #323e48;
            --light: #f8f9fa;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            margin: 20px auto;
        }
        
        .login-header {
            background: var(--gradient);
            color: white;
            padding: 25px 20px; /* Reduced padding */
            text-align: center;
            position: relative;
        }
        
        .brand-logo-login {
            height: 50px;
            width: auto;
            margin-bottom: 15px;
            /* FIXED: Gray logo for better visibility */
            filter: brightness(0) invert(0.8); /* Gray color */
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem; /* Reduced size */
        }
        
        .login-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 25px; /* Reduced padding */
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(255, 143, 28, 0.25);
        }
        
        .btn-login {
            background: var(--gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 143, 28, 0.4);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
        }
        
        .form-control:focus + .input-group-text {
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container">
                    <div class="login-header">
                        <!-- FIXED: Gray logo for better visibility -->
                        <img src="assets/images/logo.png" alt="Paysphere Logo" class="brand-logo-login">
                        <h2>Career Portal</h2>
                        <p>Sign in to your account</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-login w-100 mb-3">Sign In</button>
                            
                            <div class="text-center">
                                <a href="#" class="text-decoration-none">Forgot your password?</a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="login-footer">
                        <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Sign up here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
