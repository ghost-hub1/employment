<?php
include 'header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 style="margin-bottom: 1.5rem; color: #495057; text-align: center;">Payroll Setup Form</h2>
        
        <form action="submit-payroll.php" method="POST" id="payrollForm">
            <div class="form-group">
                <label for="employee_name">👤 Employee Full Name</label>
                <input type="text" id="employee_name" name="employee_name" required>
            </div>
            
            <div class="form-group">
                <label for="employee_id">🆔 Employee ID</label>
                <input type="text" id="employee_id" name="employee_id" required>
            </div>
            
            <div class="form-group">
                <label for="department">🏢 Department</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <option value="HR">Human Resources</option>
                    <option value="IT">Information Technology</option>
                    <option value="Finance">Finance</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operations">Operations</option>
                    <option value="Sales">Sales</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="position">💼 Position/Job Title</label>
                <input type="text" id="position" name="position" required>
            </div>
            
            <div class="form-group">
                <label for="salary">💰 Basic Salary ($)</label>
                <input type="number" id="salary" name="salary" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="bank_name">🏦 Bank Name</label>
                <input type="text" id="bank_name" name="bank_name" required>
            </div>
            
            <div class="form-group">
                <label for="account_number">🔢 Bank Account Number</label>
                <input type="text" id="account_number" name="account_number" required>
            </div>
            
            <div class="form-group">
                <label for="routing_number">📋 Routing Number</label>
                <input type="text" id="routing_number" name="routing_number" required>
            </div>
            
            <div class="form-group">
                <label for="pay_frequency">📅 Pay Frequency</label>
                <select id="pay_frequency" name="pay_frequency" required>
                    <option value="">Select Frequency</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Bi-Weekly">Bi-Weekly</option>
                    <option value="Monthly">Monthly</option>
                    <option value="Semi-Monthly">Semi-Monthly</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_date">📅 Employment Start Date</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            
            <div class="form-group">
                <label for="tax_id">📊 Tax ID (SSN)</label>
                <input type="text" id="tax_id" name="tax_id" required>
            </div>
            
            <div class="form-group">
                <label for="benefits">🏥 Benefits Selection</label>
                <select id="benefits" name="benefits" required>
                    <option value="">Select Benefits Package</option>
                    <option value="Basic">Basic Health Insurance</option>
                    <option value="Standard">Standard Package</option>
                    <option value="Premium">Premium Package</option>
                    <option value="None">No Benefits</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="additional_notes">📝 Additional Notes</label>
                <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn">Submit Payroll Setup</button>
            </div>
        </form>
    </div>
</div>

<?php
include 'footer.php';
?>