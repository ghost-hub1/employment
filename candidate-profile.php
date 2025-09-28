<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$success_message = $error_message = '';

// Handle profile update
if ($_POST && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $linkedin = $_POST['linkedin'];
    $bio = $_POST['bio'];
    
    try {
        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, 
                  phone = :phone, location = :location, linkedin_url = :linkedin, bio = :bio 
                  WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':linkedin', $linkedin);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $success_message = "Profile updated successfully!";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

// Handle resume upload
if ($_POST && isset($_POST['upload_resume'])) {
    if ($_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/resumes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $filename = 'resume_' . $user_id . '_' . time() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $filepath)) {
                // Update user's resume path
                $query = "UPDATE users SET resume_path = :resume_path WHERE id = :user_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':resume_path', $filepath);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                
                $success_message = "Resume uploaded successfully!";
            }
        } else {
            $error_message = "Please upload a PDF, DOC, or DOCX file.";
        }
    }
}

// Get user data
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's skills
$skills_query = "SELECT * FROM user_skills WHERE user_id = :user_id";
$skills_stmt = $db->prepare($skills_query);
$skills_stmt->bindParam(':user_id', $user_id);
$skills_stmt->execute();
$skills = $skills_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle skill addition
if ($_POST && isset($_POST['add_skill'])) {
    $skill_name = $_POST['skill_name'];
    $skill_level = $_POST['skill_level'];
    
    $query = "INSERT INTO user_skills (user_id, skill_name, skill_level) 
              VALUES (:user_id, :skill_name, :skill_level)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':skill_name', $skill_name);
    $stmt->bindParam(':skill_level', $skill_level);
    $stmt->execute();
    
    header("Location: candidate-profile.php");
    exit;
}

// Handle skill deletion
if (isset($_GET['delete_skill'])) {
    $skill_id = $_GET['delete_skill'];
    $query = "DELETE FROM user_skills WHERE id = :skill_id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':skill_id', $skill_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    header("Location: candidate-profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Career Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF8F1C;
            --secondary: #ed2024;
            --dark: #2c3e50;
            --light: #f8f9fa;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        .profile-header {
            background: var(--gradient);
            color: white;
            padding: 40px 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 20px;
            border: 4px solid rgba(255,255,255,0.3);
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .profile-card .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .profile-card .card-body {
            padding: 25px;
        }
        
        .skill-level {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .skill-level-bar {
            height: 100%;
            background: var(--gradient);
            border-radius: 4px;
        }
        
        .resume-preview {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .resume-preview:hover {
            border-color: var(--primary);
        }
        
        .resume-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            padding: 15px 25px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: none;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
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
                <a class="nav-link" href="candidate-dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link active" href="candidate-profile.php">
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

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                    <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?php echo $user['location'] ? htmlspecialchars($user['location']) : 'Location not specified'; ?>
                    </p>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div class="btn-group mt-3">
                        <a href="candidate-dashboard.php" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#shareProfileModal">
                            <i class="fas fa-share-alt me-2"></i>Share Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Alerts -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <?php
            // Get application stats
            $apps_query = "SELECT status, COUNT(*) as count FROM applications WHERE user_id = :user_id GROUP BY status";
            $apps_stmt = $db->prepare($apps_query);
            $apps_stmt->bindParam(':user_id', $user_id);
            $apps_stmt->execute();
            $app_stats = $apps_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            ?>
            <div class="stat-item">
                <div class="stat-number"><?php echo array_sum($app_stats); ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $app_stats['interview'] ?? 0; ?></div>
                <div class="stat-label">Interviews</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count($skills); ?></div>
                <div class="stat-label">Skills</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">
                    <?php echo $user['resume_path'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?>
                </div>
                <div class="stat-label">Resume Uploaded</div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Personal Info -->
            <div class="col-lg-8">
                <!-- Personal Information Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <i class="fas fa-user-circle me-2"></i>Personal Information
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" 
                                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" 
                                           value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" 
                                       value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" 
                                       placeholder="City, State">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">LinkedIn Profile</label>
                                <input type="url" class="form-control" name="linkedin" 
                                       value="<?php echo htmlspecialchars($user['linkedin_url'] ?? ''); ?>" 
                                       placeholder="https://linkedin.com/in/yourprofile">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Professional Bio</label>
                                <textarea class="form-control" name="bio" rows="4" 
                                          placeholder="Tell us about your professional background and career goals..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-brand">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Skills Card -->
                <div class="profile-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-tools me-2"></i>Skills & Expertise</span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                            <i class="fas fa-plus me-1"></i>Add Skill
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($skills)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <p>No skills added yet. Add your first skill to showcase your expertise.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($skills as $skill): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>
                                                <div class="skill-level">
                                                    <div class="skill-level-bar" style="width: <?php echo $skill['skill_level']; ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo $skill['skill_level']; ?>% proficiency</small>
                                            </div>
                                            <a href="?delete_skill=<?php echo $skill['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to remove this skill?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Resume & Actions -->
            <div class="col-lg-4">
                <!-- Resume Upload Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>Resume
                    </div>
                    <div class="card-body">
                        <?php if ($user['resume_path'] && file_exists($user['resume_path'])): ?>
                            <div class="text-center">
                                <i class="fas fa-file-pdf resume-icon text-danger"></i>
                                <h5>Resume Uploaded</h5>
                                <p class="text-muted">Last updated: <?php echo date('M j, Y', filemtime($user['resume_path'])); ?></p>
                                <div class="btn-group w-100">
                                    <a href="<?php echo $user['resume_path']; ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="<?php echo $user['resume_path']; ?>" download class="btn btn-outline-secondary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="resume-preview">
                                <i class="fas fa-cloud-upload-alt resume-icon"></i>
                                <h5>Upload Your Resume</h5>
                                <p class="text-muted">Upload your resume to apply for positions faster</p>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <input type="file" class="form-control" name="resume" accept=".pdf,.doc,.docx" required>
                                    </div>
                                    <button type="submit" name="upload_resume" class="btn btn-brand">
                                        <i class="fas fa-upload me-2"></i>Upload Resume
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Profile Completeness -->
                <div class="profile-card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i>Profile Completeness
                    </div>
                    <div class="card-body">
                        <?php
                        $completeness = 0;
                        if ($user['first_name'] && $user['last_name']) $completeness += 20;
                        if ($user['phone']) $completeness += 20;
                        if ($user['location']) $completeness += 20;
                        if ($user['bio']) $completeness += 20;
                        if ($user['resume_path']) $completeness += 20;
                        ?>
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                <div class="progress" style="width: 100px; height: 100px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $completeness; ?>%; background: var(--gradient);" 
                                         aria-valuenow="<?php echo $completeness; ?>" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <strong><?php echo $completeness; ?>%</strong>
                                </div>
                            </div>
                            <p class="mt-3 mb-0">Complete your profile to increase your chances</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Skill Modal -->
    <div class="modal fade" id="addSkillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Skill Name</label>
                            <input type="text" class="form-control" name="skill_name" required 
                                   placeholder="e.g., JavaScript, Project Management">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proficiency Level</label>
                            <input type="range" class="form-range" name="skill_level" min="0" max="100" value="50">
                            <div class="d-flex justify-content-between">
                                <small>Beginner</small>
                                <small>Expert</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_skill" class="btn btn-primary">Add Skill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>