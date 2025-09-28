<?php
// submit-commitment.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Telegram Bot Configuration - EDIT THESE
    $telegramBotToken = ''; // Add your bot token here
    $telegramChatID = '';   // Add your chat ID here
    
    // Collect form data
    $legal_name = htmlspecialchars($_POST['legal_name']);
    $digital_signature = htmlspecialchars($_POST['digital_signature']);
    
    // Collect all agreement checkboxes
    $agreements = [];
    for ($i = 1; $i <= 8; $i++) {
        $agreement_key = "agreement_$i";
        $agreements[$agreement_key] = isset($_POST[$agreement_key]) ? '✅ Agreed' : '❌ Not Agreed';
    }
    
    // Create formatted message with emojis
    $message = "🤝 **NEW PROGRAM COMMITMENT AGREEMENT** 🤝\n\n";
    
    $message .= "👤 **Applicant Information:**\n";
    $message .= "📛 **Legal Name:** $legal_name\n";
    $message .= "📝 **Digital Signature:** $digital_signature\n\n";
    
    $message .= "✅ **AGREEMENT TERMS ACCEPTANCE** ✅\n";
    $message .= "1. {$agreements['agreement_1']} - Understands equipment cost and package\n";
    $message .= "2. {$agreements['agreement_2']} - Committed to training completion\n"; 
    $message .= "3. {$agreements['agreement_3']} - Understands time investment\n";
    $message .= "4. {$agreements['agreement_4']} - Ready to apply fully\n";
    $message .= "5. {$agreements['agreement_5']} - Equipment ownership understood\n";
    $message .= "6. {$agreements['agreement_6']} - Training materials acknowledged\n";
    $message .= "7. {$agreements['agreement_7']} - Confident in investment\n";
    $message .= "8. {$agreements['agreement_8']} - Will participate in community\n\n";
    
    $message .= "⏰ **Submitted on:** " . date('Y-m-d H:i:s');
    $message .= "\n\n🚀 **Next Step:** Equipment Purchase Portal";
    
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
    
    // Store in session and redirect
    session_start();
    $_SESSION['commitment_data'] = $_POST;
    
    // Redirect to appropriate next step
    if (isset($_SESSION['financial_data']['equipment_investment']) && 
        $_SESSION['financial_data']['equipment_investment'] === 'yes') {
        header("Location: equipment-purchase.php");
    } else {
        header("Location: thankyou.php?status=processing");
    }
    exit;
} else {
    // If someone tries to access directly, redirect to form
    header('Location: program-commitment.php');
    exit();
}
?>