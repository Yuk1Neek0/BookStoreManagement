<?php 
class DBConnection {
    private $server = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "db_bookstore";

    public $conn;

    public function __construct() {
        try{
            $this->conn = new mysqli($this->server, $this->username, $this->password, $this->db);
            
            if($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            echo "Connection failed: " . $e->getMessage(); 
        }
    }
}
?>