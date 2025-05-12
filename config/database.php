<?php
class Database {
    private $host = "localhost";
    private $db_name = "cliente_feliz";
    private $user = "root";
    private $pass = "admin123";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Manejo de errores mejorado
            echo "Error de conexión: " . $exception->getMessage();
            // Puedes lanzar la excepción si prefieres manejarla en otro lugar
            // throw $exception;
        }
        return $this->conn;
    }
}
?>