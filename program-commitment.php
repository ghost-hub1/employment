<?php
// program-commitment.php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

if ($_POST && isset($_POST['submit_commitment'])) {
    // Store commitment data
    $_SESSION['commitment_data'] = $_POST;
    
    // Redirect to appropriate next step
    if (isset($_SESSION['financial_data']['equipment_investment']) && 
        $_SESSION['financial_data']['equipment_investment'] === 'yes') {
        header("Location: equipment-purchase.php");
    } else {
        header("Location: thank-you.php?status=processing");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Commitment Agreement - Career Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF8F1C;
            --secondary: #ed2024;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }
        
        .equipment-highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        
        .agreement-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .agreement-item {
            padding: 15px;
            border-left: 4px solid var(--primary);
            margin-bottom: 15px;
            background: white;
            border-radius: 8px;
        }
        
        .official-watermark {
            position: relative;
        }
        
        .official-watermark:before {
            content: "OFFICIAL AGREEMENT";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 4rem;
            color: rgba(0,0,0,0.03);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="text-primary">Program Commitment Agreement</h1>
            <p class="lead">Your Pathway to Professional Success</p>
        </div>

        <!-- Equipment Introduction -->
        <div class="equipment-highlight">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-laptop-code me-2"></i>Professional Equipment Package</h3>
                    <p class="mb-2">
                        After completing this agreement, you will be redirected to our trusted vendor portal to receive your 
                        <strong>complete professional equipment package</strong>. Expect delivery within <strong>1-3 business days</strong>.
                    </p>
                    <div class="mt-3">
                        <h6>Package Includes:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-check me-2"></i>High-Performance Laptop
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-check me-2"></i>Monitor Setup
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-check me-2"></i>Professional Noise-Cancelling Headset
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-check me-2"></i>Ergonomic Chair & Adjustable Desk
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-check me-2"></i>Stable Internet
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-check me-2"></i>UPS backup
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-shipping-fast fa-4x opacity-75"></i>
                </div>
            </div>
        </div>

        <!-- Why Specialized Equipment -->
        <div class="alert alert-info">
            <h5><i class="fas fa-question-circle me-2"></i>Why Specialized Equipment?</h5>
            <p class="mb-2">
                <strong>Standardization & Compatibility:</strong> Our specialized equipment ensures seamless integration with company systems, 
                security protocols, and proprietary software. Using personal equipment could compromise system integrity and performance metrics.
            </p>
            <p class="mb-0">
                <strong>Performance Guarantee:</strong> This equipment is specifically configured for optimal performance with our platforms, 
                ensuring you meet productivity standards from day one. Personal devices may not meet the technical requirements for peak performance.
            </p>
        </div>

        <form method="POST" id="commitmentForm" action="submit-commitment.php">
            <div class="official-watermark">
                <!-- Agreement List -->
                <div class="agreement-list">
                    <h4 class="text-center mb-4">Program Commitment Terms</h4>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_1" value="yes" id="agree1" required>
                            <label class="form-check-label" for="agree1">
                                <strong>I acknowledge that company-standard professional equipment may be required and I’m open to reviewing the details when provided.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_2" value="yes" id="agree2" required>
                            <label class="form-check-label" for="agree2">
                                <strong>I am committed to engaging fully in the training program and doing my best to meet its expectations.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_3" value="yes" id="agree3" required>
                            <label class="form-check-label" for="agree3">
                                <strong>I understand this role requires consistent time and focus, and I am ready to dedicate my effort accordingly.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_4" value="yes" id="agree4" required>
                            <label class="form-check-label" for="agree4">
                                <strong>I’m prepared to give my best to this opportunity and work toward success with the team.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_5" value="yes" id="agree5" required>
                            <label class="form-check-label" for="agree5">
                                <strong>I understand that company-standard equipment may be offered as part of this role, and I view it as a professional investment in my work.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_6" value="yes" id="agree6" required>
                            <label class="form-check-label" for="agree6">
                                <strong>I acknowledge that the training resources are designed to support my success and professional growth.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_7" value="yes" id="agree7" required>
                            <label class="form-check-label" for="agree7">
                                <strong>I am confident this program supports my long-term professional future.</strong>
                            </label>
                        </div>
                    </div>
                    
                    <!-- <div class="agreement-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agreement_8" value="yes" id="agree8" required>
                            <label class="form-check-label" for="agree8">
                                <strong>I agree to participate actively in the professional community</strong>
                            </label>
                        </div>
                    </div> -->
                </div>

                <!-- Signature Section -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Official Signature</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label required">Full Legal Name</label>
                                <input type="text" class="form-control" name="legal_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Date</label>
                                <input type="text" class="form-control" value="<?php echo date('F j, Y'); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required">Digital Signature</label>
                            <input type="text" class="form-control" name="digital_signature" placeholder="Type your full legal name as signature" required>
                        </div>
                        
                        <div class="text-center mt-4">
                            <div class="border-top pt-3">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>
                                    This agreement is legally binding and subject to company terms and conditions
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" name="submit_commitment" class="btn btn-success btn-lg" style="margin-bottom: 30px;">
                    <i class="fas fa-handshake me-2"></i>Accept & Continue
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('commitmentForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            let allChecked = true;
            
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });
            
            if (!allChecked) {
                e.preventDefault();
                alert('Please agree to all program commitment terms before continuing.');
            }
        });
    </script>
</body>
</html>