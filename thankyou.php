<?php
include 'includes/header.php';
?>

<div class="container">
    <div class="form-container" style="text-align: center; max-width: 600px; margin: 2rem auto;">
        <!-- Animated Checkmark -->
        <div class="checkmark-container" style="margin-bottom: 2rem;">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" style="width: 80px; height: 80px;">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" style="stroke: #4caf50; stroke-width: 2;"/>
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" style="stroke: #4caf50; stroke-width: 4; stroke-linecap: round;"/>
            </svg>
        </div>
        
        <h2 style="margin-bottom: 1rem; color: #28a745;">Thank You!</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem; color: #666;">
            Your information has been submitted successfully and is being processed.
        </p>
        
        <div style="background: #f8f9fa; border-left: 4px solid #007bff; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; text-align: left;">
            <h5 style="color: #007bff; margin-bottom: 1rem;">What Happens Next?</h5>
            <ul style="color: #555; line-height: 1.6;">
                <li>You will receive an email with instructions for the next steps</li>
                <li>Our team will review your submission within the next few hours</li>
                <li>Check your email for the equipment purchase link</li>
                <li>Complete your equipment order to finalize onboarding</li>
            </ul>
        </div>
        
        <div style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <p style="margin: 0; color: #0066cc; font-weight: 500;">
                <i class="fas fa-envelope" style="margin-right: 0.5rem;"></i>
                Please check your email for further instructions
            </p>
        </div>
        
        <p style="color: #888; font-size: 0.9rem; margin-top: 2rem;">
            If you don't receive an email within 30 minutes, please check your spam folder 
            or contact support at <strong>support@paylocityhr.com</strong>
        </p>
    </div>
</div>

<style>
.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4caf50;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

.checkmark {
    animation: scale 0.3s ease-in-out 0.9s both;
}
</style>

<?php
include 'footer.php';
?>