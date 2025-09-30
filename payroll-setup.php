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

        .official-watermark {
            position: relative;
        }
        
        .official-watermark:before {
            content: "OFFICIAL";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 5rem;
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
            font-size: 1.2rem;
            margin-right: 8px;
        }
        
        .confidential-badge {
            background: var(--secondary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Official Header -->
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
        <!-- Security Notice -->
        <div class="alert alert-info">
            <h5><i class="fas fa-shield-alt me-2"></i>Secure Payroll Information</h5>
            <p class="mb-0">
                Your payroll information is protected by bank-level encryption. All data is transmitted securely 
                and accessed only by authorized HR personnel.
            </p>
        </div>

        <form action="submit-payroll.php" method="POST" id="payrollForm">
            <div class="official-watermark">
            <!-- Employee Information Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-user-tie me-2"></i>Employee Information</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">👤</span>Employee Full Name
                        </label>
                        <input type="text" class="form-control" name="employee_name" required>
                    </div>
                    
                    <!-- <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">🆔</span>Employee ID
                        </label>
                        <input type="text" class="form-control" name="employee_id" required>
                    </div> -->
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">🏢</span>Department
                        </label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <option value="HR">Human Resources</option>
                            <option value="IT">Information Technology</option>
                            <option value="Finance">Finance</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Operations">Operations</option>
                            <option value="Sales">Sales</option>
                            <option value="Customer Service">Customer Service</option>
                            <option value="Research & Development">Research & Development</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">💼</span>Position/Job Title
                        </label>
                        <input type="text" class="form-control" name="position" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">💰</span>Basic Salary ($)
                        </label>
                        <input type="number" class="form-control" name="salary" step="0.01" min="0" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">📅</span>Employment Start Date
                        </label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                </div>
            </div>

            <!-- Banking Information Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-university me-2"></i>Banking Information</h4>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Double-check your banking information</strong> to ensure accurate and timely payments.
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">🏦</span>Bank Name
                        </label>
                        <input type="text" class="form-control" name="bank_name" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">🔢</span>Bank Account Number
                        </label>
                        <input type="text" class="form-control" name="account_number" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">📋</span>Routing Number
                        </label>
                        <input type="text" class="form-control" name="routing_number" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">📅</span>Pay Frequency
                        </label>
                        <select class="form-select" name="pay_frequency" required>
                            <option value="">Select Frequency</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Bi-Weekly">Bi-Weekly</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Semi-Monthly">Semi-Monthly</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tax & Benefits Information -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-file-invoice-dollar me-2"></i>Tax & Benefits Information</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">📊</span>Tax ID (SSN)
                        </label>
                        <input type="text" class="form-control" name="tax_id" placeholder="XXX-XX-XXXX" required>
                        <small class="text-muted">Required for tax withholding purposes</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            <span class="field-icon">🏥</span>Benefits Selection
                        </label>
                        <select class="form-select" name="benefits" required>
                            <option value="">Select Benefits Package</option>
                            <option value="Basic">Basic Health Insurance</option>
                            <option value="Standard">Standard Package (Health + Dental)</option>
                            <option value="Premium">Premium Package (Health + Dental + Vision)</option>
                            <option value="Executive">Executive Package (Full Coverage)</option>
                            <option value="None">Waive Benefits</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        <span class="field-icon">📝</span>Additional Notes
                    </label>
                    <textarea class="form-control" name="additional_notes" rows="4" placeholder="Any special payroll instructions or additional information..."></textarea>
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
            // Form validation
            document.getElementById('payrollForm').addEventListener('submit', function(e) {
                const certifyCheckbox = document.getElementById('certify');
                if (!certifyCheckbox.checked) {
                    e.preventDefault();
                    alert('Please certify that all information provided is true and accurate.');
                    return;
                }
                
                // Additional validation can be added here
                const salary = document.querySelector('input[name="salary"]');
                if (salary.value && parseFloat(salary.value) <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid salary amount.');
                    return;
                }
            });
            
            // Set minimum date for start date to today
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="start_date"]').min = today;
        });
    </script>
</body>
</html>
