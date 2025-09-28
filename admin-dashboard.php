<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotAdmin();

// Get statistics
$total_applications = $db->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$total_jobs = $db->query("SELECT COUNT(*) FROM jobs WHERE status = 'active'")->fetchColumn();
$new_applications = $db->query("SELECT COUNT(*) FROM applications WHERE DATE(applied_at) = CURDATE()")->fetchColumn();
$total_candidates = $db->query("SELECT COUNT(*) FROM users WHERE user_type = 'candidate'")->fetchColumn();

// Get recent applications
$query = "SELECT a.*, j.title, u.first_name, u.last_name, u.email 
          FROM applications a 
          JOIN jobs j ON a.job_id = j.id 
          JOIN users u ON a.user_id = u.id 
          ORDER BY a.applied_at DESC 
          LIMIT 8";
$recent_applications = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Get application status distribution
$status_stats = $db->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Career Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF8F1C;
            --secondary: #ed2024;
            --dark: #2c3e50;
            --darker: #1a252f;
            --light: #f8f9fa;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar Styles */
        .admin-sidebar {
            background: linear-gradient(180deg, var(--dark), var(--darker));
            color: white;
            min-height: 100vh;
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
        }
        
        .sidebar-header p {
            margin: 5px 0 0;
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Main Content Styles */
        .main-content {
            padding: 20px;
        }
        
        .content-header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            border-left: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.applications { border-left-color: #667eea; }
        .stat-card.jobs { border-left-color: #f093fb; }
        .stat-card.candidates { border-left-color: #4facfe; }
        .stat-card.today { border-left-color: #43e97b; }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .stat-card.applications .stat-icon { background: rgba(102, 126, 234, 0.1); color: #667eea; }
        .stat-card.jobs .stat-icon { background: rgba(240, 147, 251, 0.1); color: #f093fb; }
        .stat-card.candidates .stat-icon { background: rgba(79, 172, 254, 0.1); color: #4facfe; }
        .stat-card.today .stat-icon { background: rgba(67, 233, 123, 0.1); color: #43e97b; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 600;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 25px;
            font-weight: 600;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .badge {
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 admin-sidebar d-md-block sidebar">
                <div class="sidebar-header">
                    <div class="user-avatar mx-auto mb-3">
                        <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                    </div>
                    <h4>Admin Panel</h4>
                    <p><?php echo $_SESSION['user_name']; ?></p>
                </div>
                
                <div class="sidebar-content">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin-dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-jobs.php">
                                <i class="fas fa-briefcase"></i>Manage Jobs
                                <span class="notification-badge">3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-applications.php">
                                <i class="fas fa-file-alt"></i>Applications
                                <span class="notification-badge">12</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-candidates.php">
                                <i class="fas fa-users"></i>Candidates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-reports.php">
                                <i class="fas fa-chart-bar"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-settings.php">
                                <i class="fas fa-cog"></i>Settings
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <!-- Header -->
                <div class="content-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="h3 mb-0">Dashboard Overview</h1>
                            <p class="mb-0 text-muted">Welcome back! Here's what's happening today.</p>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search...">
                                <button class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card applications">
                            <div class="stat-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h2 class="stat-number"><?php echo $total_applications; ?></h2>
                            <p class="stat-label">Total Applications</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card jobs">
                            <div class="stat-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h2 class="stat-number"><?php echo $total_jobs; ?></h2>
                            <p class="stat-label">Active Jobs</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card candidates">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h2 class="stat-number"><?php echo $total_candidates; ?></h2>
                            <p class="stat-label">Registered Candidates</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card today">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <h2 class="stat-number"><?php echo $new_applications; ?></h2>
                            <p class="stat-label">New Applications Today</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Applications -->
                    <div class="col-lg-8">
                        <div class="dashboard-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Recent Applications</span>
                                <a href="admin-applications.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Candidate</th>
                                                <th>Position</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_applications as $app): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar me-3">
                                                                <?php echo strtoupper(substr($app['first_name'], 0, 1)); ?>
                                                            </div>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($app['title']); ?></td>
                                                    <td><?php echo date('M j, Y', strtotime($app['applied_at'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        $status_class = '';
                                                        switch($app['status']) {
                                                            case 'applied': $status_class = 'bg-secondary'; break;
                                                            case 'under_review': $status_class = 'bg-warning'; break;
                                                            case 'interview': $status_class = 'bg-info'; break;
                                                            case 'rejected': $status_class = 'bg-danger'; break;
                                                            case 'accepted': $status_class = 'bg-success'; break;
                                                            default: $status_class = 'bg-secondary';
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $status_class; ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="admin-application-detail.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Status Chart -->
                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">Quick Actions</div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="admin-jobs.php?action=create" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Post New Job
                                    </a>
                                    <a href="admin-applications.php" class="btn btn-outline-primary">
                                        <i class="fas fa-file-alt me-2"></i>Review Applications
                                    </a>
                                    <a href="admin-reports.php" class="btn btn-outline-primary">
                                        <i class="fas fa-chart-bar me-2"></i>Generate Reports
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Application Status Chart -->
                        <div class="dashboard-card">
                            <div class="card-header">Application Status</div>
                            <div class="card-body">
                                <?php foreach ($status_stats as $stat): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><?php echo ucfirst(str_replace('_', ' ', $stat['status'])); ?></span>
                                            <span><?php echo $stat['count']; ?></span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" style="width: <?php echo ($stat['count'] / $total_applications) * 100; ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple animation for stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>
</body>
</html>