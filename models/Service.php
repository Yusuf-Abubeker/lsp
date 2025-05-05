<?php
require_once __DIR__ . '/../config/database.php';

class Service {
    // Database connection and table name
    private $conn;
    private $table_name = "services";
    
    // Properties
    public $id;
    public $provider_id;
    public $category_id;
    public $title;
    public $description;
    public $price;
    public $price_type;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor with DB connection
     * 
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new service
     * 
     * @return boolean
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (provider_id, category_id, title, description, price, price_type)
                VALUES
                (:provider_id, :category_id, :title, :description, :price, :price_type)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price_type = htmlspecialchars(strip_tags($this->price_type));
        
        // Bind values
        $stmt->bindParam(":provider_id", $this->provider_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":price_type", $this->price_type);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get a service by ID
     * 
     * @param int $id
     * @return Service
     */
    public function readOne($id) {
        $query = "SELECT s.*, c.name as category_name, c.description as category_description,
                      p.user_id, u.name as provider_name, p.avg_rating, p.total_reviews
                  FROM " . $this->table_name . " s
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN providers p ON s.provider_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE s.id = :id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->provider_id = $row['provider_id'];
            $this->category_id = $row['category_id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->price_type = $row['price_type'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Additional data
            $this->category_name = $row['category_name'];
            $this->category_description = $row['category_description'];
            $this->provider_name = $row['provider_name'];
            $this->user_id = $row['user_id'];
            $this->avg_rating = $row['avg_rating'];
            $this->total_reviews = $row['total_reviews'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Update service details
     * 
     * @return boolean
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    category_id = :category_id,
                    title = :title,
                    description = :description,
                    price = :price,
                    price_type = :price_type
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price_type = htmlspecialchars(strip_tags($this->price_type));
        
        // Bind values
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":price_type", $this->price_type);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        return $stmt->execute();
    }
    
    /**
     * Delete service
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
     * Get services by provider ID
     * 
     * @param int $provider_id
     * @return PDOStatement
     */
    public function getByProviderId($provider_id) {
        $query = "SELECT s.*, c.name as category_name, 
                     (SELECT COUNT(*) FROM bookings WHERE service_id = s.id) as booking_count
                  FROM " . $this->table_name . " s
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.provider_id = :provider_id
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Search services by various criteria
     * 
     * @param array $params
     * @return PDOStatement
     */
    public function search($params = []) {
        $category_id = $params['category_id'] ?? null;
        $location = $params['location'] ?? null;
        $price_min = $params['price_min'] ?? null;
        $price_max = $params['price_max'] ?? null;
        $rating_min = $params['rating_min'] ?? null;
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;
        
        $query = "SELECT s.*, c.name as category_name, c.description as category_description,
                      p.avg_rating, p.total_reviews, u.name as provider_name,
                      p.city, p.state, p.zip_code
                  FROM " . $this->table_name . " s
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN providers p ON s.provider_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id";
        
        $conditions = [];
        $params = [];
        
        // Add where conditions
        if ($category_id) {
            $conditions[] = "s.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        if ($location) {
            $conditions[] = "(p.city LIKE :location OR p.state LIKE :location OR p.zip_code LIKE :location)";
            $params[':location'] = "%$location%";
        }
        
        if ($price_min) {
            $conditions[] = "s.price >= :price_min";
            $params[':price_min'] = $price_min;
        }
        
        if ($price_max) {
            $conditions[] = "s.price <= :price_max";
            $params[':price_max'] = $price_max;
        }
        
        if ($rating_min) {
            $conditions[] = "p.avg_rating >= :rating_min";
            $params[':rating_min'] = $rating_min;
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Add ordering
        $query .= " ORDER BY p.avg_rating DESC, s.created_at DESC";
        
        // Add limit and offset
        $query .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind all parameters
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        // Always bind these parameters
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Get top services (recent or featured)
     * 
     * @param int $limit
     * @return PDOStatement
     */
    public function getTopServices($limit = 6) {
        $query = "SELECT s.*, c.name as category_name, p.avg_rating, u.name as provider_name,
                      p.city, p.state, p.zip_code
                  FROM " . $this->table_name . " s
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN providers p ON s.provider_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id
                  ORDER BY p.avg_rating DESC, s.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
}
?>