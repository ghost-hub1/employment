<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

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

// Calculate onboarding progress - Offer Accepted is always true if user is logged in
$progress_steps = [
    'offer_accepted' => [
        'completed' => true, // Always true since user is logged in
        'title' => 'Offer Accepted',
        'completed_at' => $user['offer_accepted_at'] ?? date('Y-m-d H:i:s'),
        'url' => '#' // No URL needed as this step is always completed
    ],
    'financial_completed' => [
        'completed' => (!empty($user['financial_completed']) && $user['financial_completed'] == 1) || 
                      (!empty($user['financial_completed_at'])),
        'title' => 'Financial Assessment',
        'completed_at' => $user['financial_completed_at'] ?? null,
        'url' => 'financial-assessment.php' // Fixed: Points to its own page
    ],
    'payroll_completed' => [
        'completed' => (!empty($user['payroll_completed']) && $user['payroll_completed'] == 1) || 
                      (!empty($user['payroll_completed_at'])),
        'title' => 'Payroll Setup',
        'completed_at' => $user['payroll_completed_at'] ?? null,
        'url' => 'payroll-setup.php' // Fixed: Points to its own page
    ],
    'commitment_completed' => [
        'completed' => (!empty($user['commitment_completed']) && $user['commitment_completed'] == 1) || 
                      (!empty($user['commitment_completed_at'])),
        'title' => 'Program Commitment',
        'completed_at' => $user['commitment_completed_at'] ?? null,
        'url' => 'program-commitment.php' // Fixed: Points to its own page
    ],
    'equipment_ordered' => [
        'completed' => (!empty($user['equipment_ordered']) && $user['equipment_ordered'] == 1) || 
                      (!empty($user['equipment_ordered_at'])),
        'title' => 'Equipment Ordered',
        'completed_at' => $user['equipment_ordered_at'] ?? null,
        'url' => 'equipment-purchase.php' // Fixed: Points to its own page
    ]
];

$completed_steps = array_filter($progress_steps, function($step) {
    return $step['completed'];
});

$progress_percentage = count($completed_steps) / count($progress_steps) * 100;

// Determine next step (skip offer_accepted as it's always completed)
$next_step = null;
$step_keys = array_keys($progress_steps);

foreach ($step_keys as $key) {
    if ($key === 'offer_accepted') continue; // Skip offer_accepted as it's always completed
    
    if (!$progress_steps[$key]['completed']) {
        $next_step = $key;
        break;
    }
}

// Check if all steps are completed
$all_completed = ($next_step === null);

// Set page title for header
$page_title = "Onboarding Dashboard - Career Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        :root {
            --primary: #FF8F1C;
            --secondary: #ed2024;
            --success: #28a745;
            --dark: #323e48;
            --light: #f8f9fa;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 0;
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
        
        .btn-reset {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
        }
        
        .btn-reset:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
            color: white;
        }
        
        .welcome-section {
            background: var(--gradient);
            color: white;
            padding: 30px 0; /* REDUCED from 40px */
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
            width: 60px;
            height: 60px;
            min-width: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            border: 3px solid transparent;
        }
        
        .step-completed {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }
        
        .step-current {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            animation: pulse 2s infinite;
        }
        
        .step-pending {
            background: #e9ecef;
            color: #6c757d;
            border-color: #dee2e6;
        }
        
        .step-content {
            flex: 1;
            min-width: 0;
        }
        
        .step-action {
            margin-left: 15px;
            min-width: 140px; /* Increased width for better button fit */
            text-align: right;
        }
        
        /* FIXED: Larger Continue Button */
        .btn-continue {
            background: var(--gradient);
            border: none;
            border-radius: 8px;
            padding: 10px 20px; /* Better padding */
            font-weight: 600;
            font-size: 0.95rem; /* Slightly larger font */
            color: white;
            min-width: 110px; /* Ensure consistent width */
            transition: all 0.3s ease;
        }
        
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 143, 28, 0.3);
            color: white;
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

        /* Mobile Responsive Fixes */
        @media (max-width: 768px) {
            .progress-step {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            
            .step-icon {
                margin-right: 0;
                margin-bottom: 15px;
                width: 70px;
                height: 70px;
            }
            
            .step-content {
                margin-bottom: 15px;
                text-align: center;
            }
            
            .step-action {
                margin-left: 0;
                text-align: center;
                min-width: auto; /* Reset for mobile */
            }
            
            .welcome-section .btn {
                margin-top: 15px;
            }
            
            .welcome-section {
                padding: 20px 0; /* Further reduced for mobile */
            }
            
            .btn-continue {
                min-width: 100px; /* Smaller but still visible on mobile */
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Use the proper header instead of hardcoded navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="hero-title">Welcome to Your Onboarding, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>! 🎉</h1>
                    <p class="hero-subtitle mb-0">
                        Position: <strong><?php echo htmlspecialchars($user['position_applied'] ?? 'Your Position'); ?></strong> | 
                        Status: <span class="status-badge status-active">Onboarding in Progress</span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <?php if ($all_completed): ?>
                        <span class="btn btn-success btn-lg">
                            <i class="fas fa-check me-2"></i>Onboarding Complete
                        </span>
                    <?php else: ?>
                        <!-- Changed from Continue button to Reset button -->
                        <button type="button" class="btn btn-reset btn-lg" data-bs-toggle="modal" data-bs-target="#resetModal">
                            <i class="fas fa-redo me-2"></i>Reset Progress
                        </button>
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
                                <?php if ($step['completed'] && !empty($step['completed_at'])): ?>
                                    Completed on <?php echo date('M j, Y', strtotime($step['completed_at'])); ?>
                                <?php elseif ($step['completed']): ?>
                                    <strong class="text-success">Completed</strong>
                                <?php elseif ($key === $next_step): ?>
                                    <strong>Next step - Ready to continue</strong>
                                <?php else: ?>
                                    Pending completion of previous steps
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="step-action">
                            <?php if ($key === $next_step && isset($step['url'])): ?>
                                <!-- FIXED: Better Continue Button -->
                                <a href="<?php echo $step['url']; ?>" class="btn btn-continue">
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
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <i class="fas fa-tasks"></i>
                    <h3><?php echo count($completed_steps); ?>/<?php echo count($progress_steps); ?></h3>
                    <p>Steps Completed</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php echo $user['position_applied'] ?: 'Offer Made'; ?></h3>
                    <p>Your Position</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
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
                <div class="col-md-6 mb-3">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-laptop me-2"></i>Equipment Delivery</h5>
                        <p class="mb-2">After completing the Program Commitment, you'll receive your professional equipment package within 1-3 business days.</p>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
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

    <!-- Reset Progress Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Reset Onboarding Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reset your onboarding progress? This will clear all completed steps and you'll need to start over.</p>
                    <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="reset-progress.php" method="POST">
                        <button type="submit" class="btn btn-danger">Yes, Reset Progress</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="js/cloak-loader.js"></script> -->
</body>
</html>
