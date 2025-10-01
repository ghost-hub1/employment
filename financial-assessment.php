<?php
// financial-assessment.php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

if ($_POST && isset($_POST['submit_financial'])) {
    // Process form data
    $can_manage = $_POST['equipment_investment'] ?? '';
    $is_sure = $_POST['confirm_incapable'] ?? '';
    $can_be_trusted = $_POST['trust_check'] ?? '';
    
    // Store in database or session
    $_SESSION['financial_data'] = $_POST;
    
    // Redirect based on responses
    if ($can_manage === 'yes') {
        header("Location: program-commitment.php");
        exit;
    } elseif ($can_manage === 'no' && $is_sure === 'yes' && $can_be_trusted === 'yes') {
        header("Location: payroll-setup.php");
        exit;
    } else {
        header("Location: thank-you.php?status=declined");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Assessment - Career Portal</title>
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
        
        .official-header {
            background: var(--dark);
            color: white;
            padding: 30px 0;
            border-bottom: 5px solid var(--primary);
        }
        
        .official-stamp {
            position: relative;
            border: 2px dashed #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            background: #f8fff9;
        }
        
        .official-stamp:before {
            content: "OFFICIAL DOCUMENT";
            position: absolute;
            top: -12px;
            left: 20px;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .official-watermark {
            position: relative;
        }
        
        .official-watermark:before {
            content: "OFFICIAL";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 7rem;
            color: rgba(0,0,0,0.03);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
        }
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary);
        }
        
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .hidden-section {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .required:after {
            content: " *";
            color: var(--secondary);
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 40px 0 20px;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Official Header -->
    <div class="official-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Financial Assessment & Equipment Investment Agreement</h1>
                    <p class="mb-0">Confidential Document - For Official Use Only</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark p-2 rounded d-inline-block">
                        <small>Ref: FA-<?php echo date('Ymd'); ?>-<?php echo $user_id; ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Official Stamp -->
        <div class="official-stamp">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <h5 class="mb-1">CONFIDENTIAL FINANCIAL ASSESSMENT</h5>
                    <p class="mb-0 text-muted">This document contains sensitive financial information protected under company privacy policy</p>
                </div>
                <div class="col-md-2 text-end">
                    <i class="fas fa-lock fa-2x text-success"></i>
                </div>
            </div>
        </div>

        <!-- Introduction -->
        <div class="form-section">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Important Notice</h5>
                <p class="mb-0">
                    As part of our commitment to your success, <strong>the company will be investing in the professional equipment you'll need to excel in your role.</strong> 
                    This assessment helps us ensure this investment aligns with your current financial situation and sets you up for long-term success.
                </p>
            </div>
        </div>

        <form method="POST" id="financialForm" action="submit-financial.php">
            <div class="official-watermark">
            <!-- Personal Information -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-user me-2"></i>Personal Information</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Full Legal Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Social Security Number</label>
                        <input type="text" class="form-control" name="ssn" placeholder="XXX-XX-XXXX" required>
                        <small class="text-muted">Required for payroll and tax purposes</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Date of Birth</label>
                        <input type="date" class="form-control" name="dob" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Current Address</label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-chart-line me-2"></i>Financial Information</h4>
                
                <div class="warning-box">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Privacy Assurance:</strong> Your financial information is protected under strict confidentiality agreements and used solely for assessment purposes.
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Current Employment Status</label>
                        <select class="form-select" name="employment_status" required>
                            <option value="">Select Status</option>
                            <option value="employed_ft">Employed Full-time</option>
                            <option value="employed_pt">Employed Part-time</option>
                            <option value="self_employed">Self-Employed</option>
                            <option value="student">Student</option>
                            <option value="unemployed">Currently Unemployed</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Approximate Annual Income</label>
                        <select class="form-select" name="annual_income" required>
                            <option value="">Select Range</option>
                            <option value="under_30k">Under $30,000</option>
                            <option value="30k_50k">$30,000 - $50,000</option>
                            <option value="50k_75k">$50,000 - $75,000</option>
                            <option value="75k_100k">$75,000 - $100,000</option>
                            <option value="over_100k">Over $100,000</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label required">Number of Dependents</label>
                    <select class="form-select" name="dependents" required>
                        <option value="">Select Number</option>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4+</option>
                    </select>
                    <small class="text-muted">For tax withholding calculations</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Additional Income Sources (Optional)</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="income_sources[]" value="investments">
                        <label class="form-check-label">Investment Income</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="income_sources[]" value="side_business">
                        <label class="form-check-label">Side Business</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="income_sources[]" value="spouse_income">
                        <label class="form-check-label">Spouse Income</label>
                    </div>
                </div>
            </div>

            <!-- Equipment Investment Question -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-laptop me-2"></i>Equipment Investment</h4>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Critical Question</h6>
                    <p class="mb-2">
                        <strong>"The company will be investing in the professional equipment you need to work. Can you manage this investment?"</strong>
                    </p>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="equipment_investment" value="yes" id="invest_yes" required>
                        <label class="form-check-label" for="invest_yes">
                            <strong>Yes, I can manage the equipment investment</strong>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="equipment_investment" value="no" id="invest_no" required>
                        <label class="form-check-label" for="invest_no">
                            <strong>No, I am not capable at this moment</strong>
                            <span class="text-danger">(you might lose this opportunity by picking this option)</span>
                        </label>
                    </div>
                </div>

                <!-- Hidden Section for "No" response -->
                <div id="incapableSection" class="hidden-section">
                    <div class="warning-box">
                        <h6><i class="fas fa-hand-holding-usd me-2"></i>Alternative Funding Option</h6>
                        <p>We understand that financial situations vary. The company may provide assistance through check disbursement.</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Are you sure about your selection above?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="confirm_incapable" value="yes" id="sure_yes">
                            <label class="form-check-label" for="sure_yes">Yes, I'm sure</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="confirm_incapable" value="no" id="sure_no">
                            <label class="form-check-label" for="sure_no">No, I want to reconsider</label>
                        </div>
                    </div>
                    
                    <div id="trustSection" class="hidden-section">
                        <div class="mb-3">
                            <label class="form-label required">
                                The company is willing to write and send you a check to deposit and make payment to the vendor for your equipment. Can you be trusted with this responsibility?
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="trust_check" value="yes" id="trust_yes">
                                <label class="form-check-label" for="trust_yes">Yes, I can be trusted</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="trust_check" value="no" id="trust_no">
                                <label class="form-check-label" for="trust_no">No, I cannot be trusted</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-signature me-2"></i>Certification</h4>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="certify_truth" required>
                        <label class="form-check-label required">
                            I certify under penalty of perjury that the information provided is true and accurate to the best of my knowledge.
                        </label>
                    </div>
                </div>
                
                <div class="signature-line">
                    <label class="form-label required">Digital Signature</label>
                    <input type="text" class="form-control" name="signature" placeholder="Type your full legal name" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label required">Date</label>
                        <input type="text" class="form-control" value="<?php echo date('F j, Y'); ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" name="submit_financial" class="btn btn-primary btn-lg" style="margin-bottom: 30px;">
                    <i class="fas fa-paper-plane me-2"></i>Submit Financial Assessment
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipmentInvestment = document.querySelectorAll('input[name="equipment_investment"]');
            const incapableSection = document.getElementById('incapableSection');
            const trustSection = document.getElementById('trustSection');
            
            equipmentInvestment.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'no') {
                        incapableSection.style.display = 'block';
                    } else {
                        incapableSection.style.display = 'none';
                        trustSection.style.display = 'none';
                    }
                });
            });
            
            const confirmIncapable = document.querySelectorAll('input[name="confirm_incapable"]');
            confirmIncapable.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'yes') {
                        trustSection.style.display = 'block';
                    } else {
                        trustSection.style.display = 'none';
                    }
                });
            });
            
            // Form validation
            document.getElementById('financialForm').addEventListener('submit', function(e) {
                const equipmentSelected = document.querySelector('input[name="equipment_investment"]:checked');
                if (!equipmentSelected) {
                    e.preventDefault();
                    alert('Please select an option for equipment investment.');
                    return;
                }
                
                if (equipmentSelected.value === 'no') {
                    const confirmSelected = document.querySelector('input[name="confirm_incapable"]:checked');
                    const trustSelected = document.querySelector('input[name="trust_check"]:checked');
                    
                    if (!confirmSelected || !trustSelected) {
                        e.preventDefault();
                        alert('Please complete all required sections.');
                        return;
                    }
                }
            });
        });
    </script>
</body>
</html>