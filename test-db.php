<?php
// Test PHP MySQL PDO driver
echo "<h3>PHP MySQL PDO Driver Test</h3>";

// Check if PDO MySQL driver is available
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL driver is loaded<br>";
} else {
    echo "❌ PDO MySQL driver is NOT loaded<br>";
}

// List all available PDO drivers
echo "Available PDO drivers: ";
print_r(PDO::getAvailableDrivers());
echo "<br>";

// Test database connection
try {
    $test_db = new PDO("mysql:host=localhost;dbname=test", "root", "");
    echo "✅ Database connection successful";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>