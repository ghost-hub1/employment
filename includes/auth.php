<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct($db = null) {
        if (!$db) {
            // Try to create a database connection if none provided
            try {
                require_once __DIR__ . '/config/database.php';
                $database = new Database();
                $db = $database->getConnection();
            } catch (Exception $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        $this->conn = $db;
    }

    public function login($email, $password) {
        try {
            $query = "SELECT id, email, password, first_name, last_name, user_type 
                      FROM " . $this->table_name . " 
                      WHERE email = :email LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    // ... rest of your Auth methods remain the same
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';
    }

    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    public function redirectIfNotLoggedIn() {
        if (!$this->isLoggedIn()) {
            header("Location: login.php");
            exit;
        }
    }

    public function redirectIfNotAdmin() {
        $this->redirectIfNotLoggedIn();
        if (!$this->isAdmin()) {
            header("Location: candidate-dashboard.php");
            exit;
        }
    }
}
?>