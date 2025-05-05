<?php
require_once __DIR__ . '/../config/database.php';

class Booking {
    // Database connection and table name
    private $conn;
    private $table_name = "bookings";
    
    // Properties
    public $id;
    public $service_id;
    public $customer_id;
    public $booking_date;
    public $start_time;
    public $end_time;
    public $status;
    public $total_price;
    public $notes;
    public $created_at;
    public $updated_at;

    public $provider_id;
    
    /**
     * Constructor with DB connection
     * 
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new booking
     * 
     * @return boolean
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (service_id, customer_id, booking_date, start_time, end_time, status, total_price, notes)
                VALUES
                (:service_id, :customer_id, :booking_date, :start_time, :end_time, :status, :total_price, :notes)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->notes = $this->notes ? htmlspecialchars(strip_tags($this->notes)) : null;
        
        // Bind values
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":notes", $this->notes);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get a booking by ID
     * 
     * @param int $id
     * @return Booking
     */
    public function readOne($id) {
        $query = "SELECT b.*, s.title as service_title, s.price, s.price_type,
                    p.id as provider_id, u_provider.name as provider_name,
                    u_customer.name as customer_name, c.name as category_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN providers p ON s.provider_id = p.id
                  LEFT JOIN categories c ON s.category_id = c.id
                  LEFT JOIN users u_provider ON p.user_id = u_provider.id
                  LEFT JOIN users u_customer ON b.customer_id = u_customer.id
                  WHERE b.id = :id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set values to object properties
            $this->id = $row['id'];
            $this->service_id = $row['service_id'];
            $this->customer_id = $row['customer_id'];
            $this->booking_date = $row['booking_date'];
            $this->start_time = $row['start_time'];
            $this->end_time = $row['end_time'];
            $this->status = $row['status'];
            $this->total_price = $row['total_price'];
            $this->notes = $row['notes'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Additional data
            $this->service_title = $row['service_title'];
            $this->service_price = $row['price'];
            $this->service_price_type = $row['price_type'];
            $this->provider_id = $row['provider_id'];
            $this->provider_name = $row['provider_name'];
            $this->customer_name = $row['customer_name'];
            $this->category_name = $row['category_name'];
            
            return $this;
        }
        
        return false;
    }
    
    /**
     * Update booking
     * 
     * @return boolean
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    booking_date = :booking_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    status = :status,
                    total_price = :total_price,
                    notes = :notes
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->notes = $this->notes ? htmlspecialchars(strip_tags($this->notes)) : null;
        
        // Bind values
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        return $stmt->execute();
    }
    
    /**
     * Get bookings by customer
     * 
     * @param int $customer_id
     * @param string $status (optional)
     * @return PDOStatement
     */
    public function getByCustomer($customer_id, $status = null) {
        $query = "SELECT b.*, s.title as service_title, s.price_type,
                    u.name as provider_name, c.name as category_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN providers p ON s.provider_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE b.customer_id = :customer_id";
        
        if ($status) {
            $query .= " AND b.status = :status";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customer_id);
        
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Get bookings by provider
     * 
     * @param int $provider_id
     * @param string $status (optional)
     * @return PDOStatement
     */
    public function getByProvider($provider_id, $status = null) {
        $query = "SELECT b.*, s.title as service_title, s.price_type,
                    u.name as customer_name, c.name as category_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN users u ON b.customer_id = u.id
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.provider_id = :provider_id";
        
        if ($status) {
            $query .= " AND b.status = :status";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Update booking status
     * 
     * @param string $status
     * @return boolean
     */
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        return $stmt->execute();
    }
    
    /**
     * Check if booking time is available
     * 
     * @param int $provider_id
     * @param string $date
     * @param string $start_time
     * @param string $end_time
     * @param int $exclude_booking_id (optional)
     * @return boolean
     */
    public function isTimeAvailable($provider_id, $date, $start_time, $end_time = null, $exclude_booking_id = null) {
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->table_name . " b
                  LEFT JOIN services s ON b.service_id = s.id
                  WHERE s.provider_id = :provider_id
                  AND b.booking_date = :date
                  AND b.status != 'cancelled'
                  AND (
                    (b.start_time <= :start_time AND (b.end_time IS NULL OR b.end_time > :start_time))
                    OR (:end_time IS NOT NULL AND b.start_time < :end_time AND (b.end_time IS NULL OR b.end_time >= :end_time))
                    OR (:start_time <= b.start_time AND (:end_time IS NULL OR :end_time > b.start_time))
                  )";
        
        if ($exclude_booking_id) {
            $query .= " AND b.id != :exclude_booking_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":start_time", $start_time);
        
        if ($end_time) {
            $stmt->bindParam(":end_time", $end_time);
        } else {
            $stmt->bindValue(":end_time", null, PDO::PARAM_NULL);
        }
        
        if ($exclude_booking_id) {
            $stmt->bindParam(":exclude_booking_id", $exclude_booking_id);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] == 0;
    }
    
    /**
     * Get count of bookings by status
     * 
     * @param string $status
     * @return int
     */
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = :status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Get all bookings (for admin), with optional status filter
     * 
     * @param string|null $status_filter
     * @return PDOStatement
     */
    public function getAllBookings($status_filter = null) {
        $query = "SELECT b.*, s.title as service_title, s.price_type,
                    u_provider.name as provider_name,
                    u_customer.name as customer_name,
                    c.name as category_name
                FROM " . $this->table_name . " b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN providers p ON s.provider_id = p.id
                LEFT JOIN users u_provider ON p.user_id = u_provider.id
                LEFT JOIN users u_customer ON b.customer_id = u_customer.id
                LEFT JOIN categories c ON s.category_id = c.id";
        
        if ($status_filter) {
            $query .= " WHERE b.status = :status";
        }

        $query .= " ORDER BY b.booking_date DESC, b.start_time ASC";

        $stmt = $this->conn->prepare($query);

        if ($status_filter) {
            $stmt->bindParam(":status", $status_filter);
        }

        $stmt->execute();

        return $stmt;
    }

}
?>