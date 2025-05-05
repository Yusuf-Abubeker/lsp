<?php
require_once __DIR__ . '/../config/database.php';

class Review {
    // Database connection and table name
    private $conn;
    private $table_name = "reviews";
    
    // Properties
    public $id;
    public $booking_id;
    public $service_id;
    public $customer_id;
    public $provider_id;
    public $rating;
    public $comment;
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
     * Create a new review
     * 
     * @return boolean
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (booking_id, service_id, customer_id, provider_id, rating, comment)
                VALUES
                (:booking_id, :service_id, :customer_id, :provider_id, :rating, :comment)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->comment = htmlspecialchars(strip_tags($this->comment));
        
        // Bind values
        $stmt->bindParam(":booking_id", $this->booking_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":provider_id", $this->provider_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":comment", $this->comment);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Update provider's average rating
            $provider = new Provider($this->conn);
            $provider->id = $this->provider_id;
            $provider->updateRating();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get a review by ID
     * 
     * @param int $id
     * @return Review
     */
    public function readOne($id) {
        $query = "SELECT r.*, u.name as customer_name, s.title as service_title
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.customer_id = u.id
                  LEFT JOIN services s ON r.service_id = s.id
                  WHERE r.id = :id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->booking_id = $row['booking_id'];
            $this->service_id = $row['service_id'];
            $this->customer_id = $row['customer_id'];
            $this->provider_id = $row['provider_id'];
            $this->rating = $row['rating'];
            $this->comment = $row['comment'];
            $this->created_at = $row['created_at'];
            
            // Additional data
            $this->customer_name = $row['customer_name'];
            $this->service_title = $row['service_title'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Check if a booking has been reviewed
     * 
     * @param int $booking_id
     * @return boolean
     */
    public function bookingHasReview($booking_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE booking_id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $booking_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
    
    /**
     * Get reviews by provider ID
     * 
     * @param int $provider_id
     * @param int $limit
     * @param int $offset
     * @return PDOStatement
     */
    public function getByProviderId($provider_id, $limit = 10, $offset = 0) {
        $query = "SELECT r.*, u.name as customer_name, s.title as service_title
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.customer_id = u.id
                  LEFT JOIN services s ON r.service_id = s.id
                  WHERE r.provider_id = :provider_id
                  ORDER BY r.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Get reviews by service ID
     * 
     * @param int $service_id
     * @param int $limit
     * @param int $offset
     * @return PDOStatement
     */
    public function getByServiceId($service_id, $limit = 10, $offset = 0) {
        $query = "SELECT r.*, u.name as customer_name, s.title as service_title
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.customer_id = u.id
                  LEFT JOIN services s ON r.service_id = s.id
                  WHERE r.service_id = :service_id
                  ORDER BY r.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":service_id", $service_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Count reviews by provider ID
     * 
     * @param int $provider_id
     * @return int
     */
    public function countByProviderId($provider_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE provider_id = :provider_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
    /**
     * Get average rating by provider ID
     * 
     * @param int $provider_id
     * @return float
     */
    public function getAverageRatingByProviderId($provider_id) {
        $query = "SELECT AVG(rating) as avg_rating FROM " . $this->table_name . " WHERE provider_id = :provider_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row['avg_rating'], 1);
    }
}
?>