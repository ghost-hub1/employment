<?php
class Database {
    private $host = "mysql-emp-paylocity0-00.b.aivencloud.com";
    private $port = "16646"; // use actual port from Aiven
    private $db_name = "defaultdb";
    private $username = "avnadmin";
    private $password = "AVNS_XTwQCB7wiuay88BHwOB";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_SSL_CA => '/var/www/html/config/ca.pem', // download from Aiven
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
