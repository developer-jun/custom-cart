<?php
namespace Cart;

class DB
{
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;

    private array $error_messages;

    // Constructor to initialize the database credentials
    public function __construct($host, $username, $password, $database, $port = 3306)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;

        $this->error_messages = [];
        
        // Establish connection when the class is instantiated
        $this->connect();
    }

    // Establish a connection to the MySQL database
    private function connect()
    {
        $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        
        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    // Execute a query (useful for INSERT, UPDATE, DELETE)
    public function execute($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);

        if ($params) {
            $this->bindParams($stmt, $params);
        }

        return $stmt->execute();
    }

    // Fetch a single row from the result set
    public function fetch($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);

        if ($params) {
            $this->bindParams($stmt, $params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // Fetch all rows from the result set
    public function fetchAll($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);

        if ($params) {
            $this->bindParams($stmt, $params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get the last inserted ID
    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    // Close the database connection
    public function close()
    {
        $this->connection->close();
    }

    public function hasError(): bool {
        return !empty($this->error_messages);
    }

    public function getErrors(): array {
        return $this->error_messages;
    }

    // Bind parameters for prepared statements
    private function bindParams($stmt, $params)
    {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob and other types
            }
        }

        $stmt->bind_param($types, ...$params);
    }

    
}

