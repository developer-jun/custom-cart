<?php
namespace Cart;


class Database {
    private array $error_messages;
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private $conn;

    // Constructor, 
    public function __construct($host = 'localhost', $db_name = 'beta', $username = 'root', $password = '') {
        $this->host = $host;
        $this->db_name = $db_name;
        $this->username = $username;
        $this->password = $password;
        $this->error_messages = [];

        // set DB info
        $this->connect();
    }

    private function connect() {
        $this->conn = new \mysqli($this->host, $this->username, $this->password, $this->db_name);
        
        if ($this->conn->connect_error) {
            $this->error_messages[] = 'Connection Error: ' . $this->conn->connect_error;
        } else {
            $this->conn->set_charset('utf8mb4');
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->error_messages[] = 'Prepare Error: ' . $this->conn->error;
            return false;
        }

        if ($params) {
            $types = str_repeat('s', count($params)); // Assumes all params are strings, adjust as needed
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $this->error_messages[] = 'Execute Error: ' . $stmt->error;
            return false;
        }

        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            $result = $stmt->get_result();
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }
        return [];
    }

    public function fetch($sql, $params = []): ?array {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            $result = $stmt->get_result();
            return $result ? $result->fetch_assoc() : null;
        }
        return null;
    }

    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->affected_rows : 0;
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    public function hasError(): bool {
        return !empty($this->error_messages);
    }

    public function getErrors(): array {
        return $this->error_messages;
    }
}
