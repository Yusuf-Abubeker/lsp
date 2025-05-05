<?php
require_once __DIR__ . '/../config/database.php';

class Provider {
    // Database connection and table name
    private $conn;
    private $table_name = "providers";
    
    // Properties
    public $id;
    public $user_id;
    public $bio;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $latitude;
    public $longitude;
    public $is_verified;
    public $avg_rating;
    public $total_reviews;
    
    /**
     * Constructor with DB connection
     * 
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get provider by user_id
     * 
     * @param int $user_id
     * @return Provider
     */
    public function getByUserId($user_id) {
        $query = "SELECT p.*, u.name, u.email, u.phone, u.profile_image
                FROM " . $this->table_name . " p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.user_id = :user_id
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->bio = $row['bio'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->zip_code = $row['zip_code'];
            $this->latitude = $row['latitude'];
            $this->longitude = $row['longitude'];
            $this->is_verified = $row['is_verified'];
            $this->avg_rating = $row['avg_rating'];
            $this->total_reviews = $row['total_reviews'];
            
            // User data
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->profile_image = $row['profile_image'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Get provider by ID
     * 
     * @param int $id
     * @return Provider
     */
    public function readOne($id) {
        $query = "SELECT p.*, u.name, u.email, u.phone, u.profile_image
                FROM " . $this->table_name . " p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.id = :id
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->bio = $row['bio'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->zip_code = $row['zip_code'];
            $this->latitude = $row['latitude'];
            $this->longitude = $row['longitude'];
            $this->is_verified = $row['is_verified'];
            $this->avg_rating = $row['avg_rating'];
            $this->total_reviews = $row['total_reviews'];
            
            // User data
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->profile_image = $row['profile_image'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Update provider profile
     * 
     * @return boolean
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    bio = :bio,
                    address = :address,
                    city = :city,
                    state = :state,
                    zip_code = :zip_code,
                    latitude = :latitude,
                    longitude = :longitude
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->state = htmlspecialchars(strip_tags($this->state));
        $this->zip_code = htmlspecialchars(strip_tags($this->zip_code));
        
        // Bind values
        $stmt->bindParam(":bio", $this->bio);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":state", $this->state);
        $stmt->bindParam(":zip_code", $this->zip_code);
        $stmt->bindParam(":latitude", $this->latitude);
        $stmt->bindParam(":longitude", $this->longitude);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Search providers by location and/or category
     * 
     * @param string $location
     * @param int $category_id
     * @param float $user_lat
     * @param float $user_lng
     * @param int $limit
     * @param int $offset
     * @return PDOStatement
     */
    public function search($location = null, $category_id = null, $user_lat = null, $user_lng = null, $limit = 10, $offset = 0) {
        $query = "SELECT DISTINCT p.*, u.name, u.email, u.phone, u.profile_image,
                    CASE 
                        WHEN :user_lat IS NOT NULL AND :user_lng IS NOT NULL 
                        THEN (
                            3959 * acos(
                                cos(radians(:user_lat)) * 
                                cos(radians(p.latitude)) * 
                                cos(radians(p.longitude) - radians(:user_lng)) + 
                                sin(radians(:user_lat)) * 
                                sin(radians(p.latitude))
                            )
                        )
                        ELSE NULL 
                    END as distance
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN services s ON p.id = s.provider_id";
        
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
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Add ordering
        if ($user_lat && $user_lng) {
            $query .= " ORDER BY distance ASC";
        } else {
            $query .= " ORDER BY p.avg_rating DESC";
        }
        
        // Add limit and offset
        $query .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind all parameters
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        // Always bind these parameters
        $stmt->bindValue(':user_lat', $user_lat, PDO::PARAM_NULL);
        $stmt->bindValue(':user_lng', $user_lng, PDO::PARAM_NULL);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Get top rated providers
     * 
     * @param int $limit
     * @return PDOStatement
     */
    public function getTopRated($limit = 6) {
        $query = "SELECT p.*, u.name, u.email, u.phone, u.profile_image
                FROM " . $this->table_name . " p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.avg_rating > 0
                ORDER BY p.avg_rating DESC, p.total_reviews DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Set verification status
     * 
     * @param boolean $status
     * @return boolean
     */
    public function setVerificationStatus($status) {
        $query = "UPDATE " . $this->table_name . "
                SET 
                    is_verified = :status
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $status = $status ? 1 : 0;
        
        $stmt->bindParam(":status", $status, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Update provider rating
     * 
     * @param float $rating
     * @return boolean
     */
    public function updateRating() {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total
                FROM reviews
                WHERE provider_id = :provider_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $this->id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $avg_rating = $result['avg_rating'];
            $total_reviews = $result['total'];
            
            $update_query = "UPDATE " . $this->table_name . "
                           SET 
                               avg_rating = :avg_rating,
                               total_reviews = :total_reviews
                           WHERE
                               id = :id";
            
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":avg_rating", $avg_rating);
            $update_stmt->bindParam(":total_reviews", $total_reviews);
            $update_stmt->bindParam(":id", $this->id);
            
            return $update_stmt->execute();
        }
        
        return false;
    }

    public function getAll($limit = 50, $offset = 0) {
        $query = "SELECT p.*, u.name, u.email, u.phone, u.profile_image
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt;
    }

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
    
    /**
     * Get provider ID by user ID
     *
     * @param int $user_id
     * @return int|null
     */
    public function getProviderIdByUserId($user_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }
    
}
?>