<?php
include 'config/database.php';
include 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

// Get user information and progress
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate onboarding progress
$progress_steps = [
    'offer_accepted' => ['completed' => (bool)$user['offer_accepted'], 'title' => 'Offer Accepted'],
    'financial_completed' => ['completed' => (bool)$user['financial_completed'], 'title' => 'Financial Assessment'],
    'payroll_completed' => ['completed' => (bool)$user['payroll_completed'], 'title' => 'Payroll Setup'],
    'commitment_completed' => ['completed' => (bool)$user['commitment_completed'], 'title' => 'Program Commitment'],
    'equipment_ordered' => ['completed' => (bool)$user['equipment_ordered'], 'title' => 'Equipment Ordered']
];

$completed_steps = array_filter($progress_steps, function($step) {
    return $step['completed'];
});

$progress_percentage = count($completed_steps) / count($progress_steps) * 100;

// Determine next step
$next_step = null;
foreach ($progress_steps as $key => $step) {
    if (!$step['completed']) {
        $next_step = $key;
        break;
    }
}

// Map next steps to URLs
$step_urls = [
    'offer_accepted' => 'financial-assessment.php',
    'financial_completed' => 'payroll-setup.php', 
    'payroll_completed' => 'program-commitment.php',
    'commitment_completed' => 'equipment-purchase.php',
    'equipment_ordered' => 'thankyou.php?status=complete'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding Dashboard - Career Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .brand-gradient {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card {
            text-align: center;
            padding: 25px 15px;
            color: white;
            border-radius: 15px;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin: 0;
            font-weight: 700;
        }
        
        .stat-card p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        
        .btn-primary {
            background: var(--gradient);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 143, 28, 0.3);
        }
        
        .welcome-section {
            background: var(--gradient);
            color: white;
            padding: 40px 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 30px;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--gradient);
            border-radius: 3px;
        }
        
        .progress-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
        }
        
        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .step-completed {
            background: #4caf50;
            color: white;
        }
        
        .step-current {
            background: var(--primary);
            color: white;
            animation: pulse 2s infinite;
        }
        
        .step-pending {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-action {
            margin-left: 15px;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-active { background: #e8f5e9; color: #388e3c; }
        .status-pending { background: #fff3e0; color: #f57c00; }
        .status-complete { background: #4caf50; color: white; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand brand-gradient" href="candidate-dashboard.php">
                <i class="fas fa-briefcase me-2"></i>CareerPortal
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="candidate-dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Onboarding
                </a>
                <a class="nav-link" href="candidate-profile.php">
                    <i class="fas fa-user me-1"></i>Profile
                </a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <span><?php echo $_SESSION['user_name']; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="candidate-profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>Welcome to Your Onboarding, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>! 🎉</h1>
                    <p class="mb-0">
                        Position: <strong><?php echo htmlspecialchars($user['position_applied'] ?? 'Your Position'); ?></strong> | 
                        Status: <span class="status-badge status-active">Onboarding in Progress</span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($next_step && isset($step_urls[$next_step])): ?>
                        <a href="<?php echo $step_urls[$next_step]; ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-arrow-right me-2"></i>Continue Onboarding
                        </a>
                    <?php else: ?>
                        <span class="btn btn-success btn-lg">
                            <i class="fas fa-check me-2"></i>Onboarding Complete
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Progress Overview -->
        <div class="dashboard-card p-4">
            <h3 class="section-title">Onboarding Progress</h3>
            
            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 20px;">
                <div class="progress-bar" role="progressbar" 
                     style="width: <?php echo number_format($progress_percentage, 0); ?>%; background: var(--gradient);"
                     aria-valuenow="<?php echo $progress_percentage; ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <?php echo round($progress_percentage); ?>%
                </div>
            </div>
            
            <!-- Progress Steps -->
            <div class="progress-container">
                <?php foreach ($progress_steps as $key => $step): ?>
                    <div class="progress-step">
                        <div class="step-icon <?php echo $step['completed'] ? 'step-completed' : ($key === $next_step ? 'step-current' : 'step-pending'); ?>">
                            <?php if ($step['completed']): ?>
                                <i class="fas fa-check"></i>
                            <?php elseif ($key === $next_step): ?>
                                <i class="fas fa-play"></i>
                            <?php else: ?>
                                <i class="fas fa-clock"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="step-content">
                            <h5 class="mb-1"><?php echo $step['title']; ?></h5>
                            <p class="mb-0 text-muted">
                                <?php if ($step['completed']): ?>
                                    Completed on <?php echo date('M j, Y', strtotime($user[$key . '_at'])); ?>
                                <?php elseif ($key === $next_step): ?>
                                    <strong>Next step - Click continue to proceed</strong>
                                <?php else: ?>
                                    Pending completion of previous steps
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="step-action">
                            <?php if ($key === $next_step && isset($step_urls[$key])): ?>
                                <a href="<?php echo $step_urls[$key]; ?>" class="btn btn-primary btn-sm">
                                    Continue <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            <?php elseif ($step['completed']): ?>
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Complete
                                </span>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <i class="fas fa-tasks"></i>
                    <h3><?php echo count($completed_steps); ?>/<?php echo count($progress_steps); ?></h3>
                    <p>Steps Completed</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php echo $user['position_applied'] ?: 'Offer Made'; ?></h3>
                    <p>Your Position</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                    <i class="fas fa-calendar-check"></i>
                    <h3><?php echo $user['offer_accepted_at'] ? date('M j', strtotime($user['offer_accepted_at'])) : 'Now'; ?></h3>
                    <p>Start Date</p>
                </div>
            </div>
        </div>

        <!-- Next Steps Information -->
        <div class="dashboard-card p-4 mt-4">
            <h3 class="section-title">What to Expect Next</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-laptop me-2"></i>Equipment Delivery</h5>
                        <p class="mb-2">After completing the Program Commitment, you'll receive your professional equipment package within 1-3 business days.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-graduation-cap me-2"></i>Training Program</h5>
                        <p class="mb-2">Once equipment is received, you'll begin our comprehensive training program to prepare you for success.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <p class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Estimated time to complete onboarding: <strong>3-5 business days</strong>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>