<?php
require_once __DIR__ . '/../config/database.php';

class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // User properties
    public $id;
    public $role;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $profile_image;
    public $created_at;
    
    /**
     * Constructor with DB connection
     * 
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Register a new user
     * 
     * @return boolean
     */
    public function register() {
        $query = "INSERT INTO " . $this->table_name . "
                (role, name, email, password, phone)
                VALUES
                (:role, :name, :email, :password, :phone)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and hash the password
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Bind values
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":phone", $this->phone);
        
        // Execute query
        if ($stmt->execute()) {
            // Get the user id
            $this->id = $this->conn->lastInsertId();
            
            // If the user is a provider, create a provider record
            if ($this->role === 'provider') {
                $this->createProviderProfile();
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a provider profile
     * 
     * @return boolean
     */
    private function createProviderProfile() {
        $query = "INSERT INTO providers (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->id);
        return $stmt->execute();
    }
    
    /**
     * Check if email exists
     * 
     * @return boolean
     */
    public function emailExists() {
        $query = "SELECT id, role, name, email, password
                FROM " . $this->table_name . "
                WHERE email = :email
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind email value
        $stmt->bindParam(":email", $this->email);
        
        // Execute query
        $stmt->execute();
        
        // Check if email exists
        if ($stmt->rowCount() > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->role = $row['role'];
            $this->name = $row['name'];
            $this->password = $row['password'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Login user
     * 
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function login($email, $password) {
        $this->email = $email;
        
        // Check if email exists
        if ($this->emailExists()) {
            // Verify password
            if (password_verify($password, $this->password)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id
     * @return User
     */
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->role = $row['role'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->password = $row['password'];
            $this->profile_image = $row['profile_image'];
            $this->created_at = $row['created_at'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Update user profile
     * 
     * @return boolean
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    name = :name,
                    email = :email,
                    phone = :phone
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Update user password
     * 
     * @param string $new_password
     * @return boolean
     */
    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table_name . "
                SET
                    password = :password
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash the password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Bind values
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        return $stmt->execute();
    }
    
    /**
     * Get all users
     * 
     * @param string $role Optional role filter
     * @return PDOStatement
     */
    public function readAll($role = null) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(":role", $role);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Count users
     * 
     * @param string $role Optional role filter
     * @return int
     */
    public function countUsers($role = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(":role", $role);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
}
?>