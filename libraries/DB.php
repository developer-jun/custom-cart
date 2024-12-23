<?php
namespace Libraries;

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
    public function __construct($host, $username, $password, $database, $port = 3306) {
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
    private function connect(): void {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Ensure exceptions are thrown
        try {
            $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        } catch (mysqli_sql_exception $e) {
            $this->error_messages[] = $e->getMessage();
            die('Error connecting to database');
        }
        // $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            $this->error_messages[] = $this->connection->connect_error;
            die('Error connecting to database');
        }
    }

    // Execute a query (useful for INSERT, UPDATE, DELETE)
    public function execute($query, $params = []): bool {
        $stmt = $this->connection->prepare($query);

        if (!empty($params)) {
            $this->bindParams($stmt, $params);
        }

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            $this->error_messages[] = $e->getMessage();
        }

        return false;
    }

    // Fetch a single row from the result set
    public function fetch($query, $params = []): ?array {
        $stmt = $this->connection->prepare($query);

        if (!empty($params)) {
            $this->bindParams($stmt, $params);
        }

        try {
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_assoc();
        } catch(Exception $e) {
            $this->error_messages[] = $e->getMessage();
        }

        return null;
    }

    // Fetch all rows from the result set
    public function fetchAll($query, $params = []): array {
        $stmt = $this->connection->prepare($query);

        if ($params) {
            $this->bindParams($stmt, $params);
        }

        try {
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch(Exception $e) {
            $this->error_messages[] = $e->getMessage();
        }

        return null;
    }

    // Get the last inserted ID
    public function lastInsertId(): int {
        return $this->connection->insert_id;
    }

    // Close the database connection
    public function close(): void
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
    private function bindParams($stmt, $params): void {
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