<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    // Database connection and table name
    private $conn;
    private $table_name = "categories";
    
    // Properties
    public $id;
    public $name;
    public $description;
    public $icon;
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
     * Create a new category
     * 
     * @return boolean
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (name, description, icon)
                VALUES
                (:name, :description, :icon)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->icon = htmlspecialchars(strip_tags($this->icon));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":icon", $this->icon);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get a category by ID
     * 
     * @param int $id
     * @return Category
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
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->icon = $row['icon'];
            $this->created_at = $row['created_at'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Get all categories
     * 
     * @return PDOStatement
     */
    public function readAll() {
        $query = "SELECT c.*, 
                    (SELECT COUNT(*) FROM services WHERE category_id = c.id) as service_count
                  FROM " . $this->table_name . " c
                  ORDER BY c.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Update category
     * 
     * @return boolean
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    name = :name,
                    description = :description,
                    icon = :icon
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->icon = htmlspecialchars(strip_tags($this->icon));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":icon", $this->icon);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        return $stmt->execute();
    }
    
    /**
     * Delete category
     * 
     * @return boolean
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Get popular categories
     * 
     * @param int $limit
     * @return PDOStatement
     */
    public function getPopularCategories($limit = 8) {
        $query = "SELECT c.*, 
                    (SELECT COUNT(*) FROM services WHERE category_id = c.id) as service_count
                  FROM " . $this->table_name . " c
                  ORDER BY service_count DESC, c.name ASC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
}
?>