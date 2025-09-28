<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

// Get user applications
$user_id = $_SESSION['user_id'];
$query = "SELECT a.*, j.title, j.department, j.location 
          FROM applications a 
          JOIN jobs j ON a.job_id = j.id 
          WHERE a.user_id = :user_id 
          ORDER BY a.applied_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$total_applications = count($applications);
$interview_count = count(array_filter($applications, function($app) { 
    return $app['status'] == 'interview'; 
}));
$review_count = count(array_filter($applications, function($app) { 
    return $app['status'] == 'under_review'; 
}));
$active_count = count(array_filter($applications, function($app) { 
    return in_array($app['status'], ['applied', 'under_review', 'interview']); 
}));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Dashboard - Career Portal</title>
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
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-applied { background: #e3f2fd; color: #1976d2; }
        .status-review { background: #fff3e0; color: #f57c00; }
        .status-interview { background: #e8f5e9; color: #388e3c; }
        .status-rejected { background: #ffebee; color: #d32f2f; }
        .status-accepted { background: #4caf50; color: white; }
        
        .table-hover tbody tr:hover {
            background-color: rgba(255, 143, 28, 0.05);
        }
        
        .btn-primary {
            background: var(--gradient);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
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
    </style>
</head>
<body>
    <!-- Navigation -->
    <!-- Updated Navigation Section -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand brand-gradient" href="candidate-dashboard.php">
                <i class="fas fa-briefcase me-2"></i>CareerPortal
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="candidate-dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
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
                    <h1>Welcome back, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>! 👋</h1>
                    <p class="mb-0">Here's your application dashboard</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="jobs.html" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Apply for Jobs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Total Applications</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                    <i class="fas fa-eye"></i>
                    <h3><?php echo $review_count; ?></h3>
                    <p>Under Review</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                    <i class="fas fa-calendar-check"></i>
                    <h3><?php echo $interview_count; ?></h3>
                    <p>Interviews</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                    <i class="fas fa-chart-line"></i>
                    <h3><?php echo $active_count; ?></h3>
                    <p>Active</p>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="dashboard-card p-4 mt-4">
            <h3 class="section-title">My Applications</h3>
            
            <?php if (empty($applications)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                    <h4>No applications yet</h4>
                    <p class="text-muted mb-4">Start your journey by applying to exciting opportunities</p>
                    <a href="jobs.html" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Browse Open Positions
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Location</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                <i class="fas fa-briefcase text-primary"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($application['title']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($application['department']); ?></td>
                                    <td>
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <?php echo htmlspecialchars($application['location']); ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($application['applied_at'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $application['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $application['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="job-detail.php?id=<?php echo $application['job_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>