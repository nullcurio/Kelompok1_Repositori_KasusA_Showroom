<?php
/**
 * Class Database
 * Menangani koneksi PDO ke database 'pbo_dbshowroom'[cite: 1].
 */
class Database {
    private $host = "localhost";
    private $db_name = "pbo_dbshowroom"; //[cite: 1]
    private $username = "root";
    private $password = "";
    public $conn;

    public function __construct() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Koneksi database gagal: " . $exception->getMessage();
        }
    }
}