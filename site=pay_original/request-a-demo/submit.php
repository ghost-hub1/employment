<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Telegram Bot credentials
    $botToken = "7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY";
    $chatId = "1325797388";
    
    // Collect form data
    $fields = [
        "email" => "Business Email",
        "phone" => "Phone",
        "first-name" => "First Name",
        "last-name" => "Last Name",
        "company-name" => "Company",
        "zip" => "Business Zip Code",
        "employee-count" => "Employee Count",
        "job-title" => "Job Title"
    ];
    
    $message = "New Form Submission:\n";
    
    foreach ($fields as $key => $label) {
        if (!empty($_POST[$key])) {
            $message .= "$label: " . htmlspecialchars($_POST[$key]) . "\n";
        }
    }
    
    // Send message to Telegram
    $telegramUrl = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = [
        "chat_id" => $chatId,
        "text" => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Redirect to a success page or back to the form
    header("Location: index.html");
    exit();
}
?>
