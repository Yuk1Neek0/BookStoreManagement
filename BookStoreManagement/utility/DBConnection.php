<?php
class DBConnection {
    private $server = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "db_bookstore";
    public $conn;
    private static $instance = null; // Changed to private for proper Singleton

    // Private constructor to prevent direct instantiation
    public function __construct() {
        try {
            $this->conn = new mysqli($this->server, $this->username, $this->password, $this->db);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            // Optional: Echo only during development
            // echo "Connected successfully!";
        } catch (Exception $e) {
            echo "Connection failed: " . $e->getMessage();
            exit;
        }
    }

    // Singleton method to get the single instance
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    // Get the connection (optional, for cleaner access)
    public function getConnection() {
        return $this->conn;
    }

    // Close the connection
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            self::$instance = null; // Reset instance for future use
        }
    }
}
?>