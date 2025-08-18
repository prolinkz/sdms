<?php
// Removed: require_once __DIR__ . '/../config/database.php'; (Now handled by init.php)

class Auth {
    private $db;

    // Accept PDO connection as a parameter (Dependency Injection)
    public function __construct(PDO $db_connection) {
        $this->db = $db_connection;
    }

    public function login($identifier, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, 
                CASE 
                    WHEN u.role = 'Student' THEN s.student_id
                    WHEN u.role = 'Staff' THEN st.staff_id
                    WHEN u.role = 'Parent' THEN p.parent_id
                    ELSE NULL
                END as profile_id
                FROM users u
                LEFT JOIN students s ON u.user_id = s.user_id
                LEFT JOIN staff st ON u.user_id = st.user_id
                LEFT JOIN parents p ON u.user_id = p.user_id
                WHERE (u.username = :identifier OR u.email = :identifier) 
                AND u.status = 'Active'
                LIMIT 1
            ");
            
            $stmt->bindParam(':identifier', $identifier);
            $stmt->execute();
            
            if($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    
                    $_SESSION = [
                        'user_id' => $user['user_id'],
                        'role' => $user['role'],
                        'profile_id' => $user['profile_id'], // This might be null for Admin/Accountant if they don't have a linked student/staff/parent profile
                        'username' => $user['username'],
                        'last_activity' => time()
                    ];
                    
                    $this->logAccess($user['user_id']);
                    return $this->getRedirectUrl($user['role']);
                }
            }
            
            error_log("Failed login attempt for: $identifier");
            return ['status' => 'error', 'message' => 'Invalid username/email or password'];
            
        } catch(PDOException $e) {
            error_log('Login Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred. Please try again.'];
        }
    }

    private function getRedirectUrl($role) {
        $redirects = [
            'Admin' => 'admin/dashboard.php',
            'Principal' => 'principal/dashboard.php', 
            'Administrator' => 'admin/dashboard.php', // Assuming Administrator also goes to admin dashboard
            'Accountant' => 'accountant/dashboard.php', // Assuming an accountant dashboard
            'Teacher' => 'teacher/dashboard.php',
            'Student' => 'student/dashboard.php',
            'Parent' => 'parent/dashboard.php'
        ];
        
        // Use BASE_URL constant for redirection to ensure full path
        // This will be defined in init.php
        return [
            'status' => 'success',
            'redirect' => (defined('BASE_URL') ? BASE_URL : '/') . ($redirects[$role] ?? 'dashboard.php')
        ];
    }

    // In Auth.php, modify the logAccess method
    // ...
    // Add new parameters for logging
    public function logAccess($user_id, $action, $table_name = NULL, $record_id = NULL, $details = NULL) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO system_logs (user_id, action, table_name, record_id, ip_address, user_agent, details)
                VALUES (:user_id, :action, :table_name, :record_id, :ip, :ua, :details)
            ");
            
            $stmt->execute([
                ':user_id' => $user_id,
                ':action' => $action,
                ':table_name' => $table_name,
                ':record_id' => $record_id,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                ':details' => $details
            ]);
        } catch(PDOException $e) {
            error_log('Logging Error: ' . $e->getMessage());
        }
    }
    // ...

    public function isLoggedIn() {
        // Also checks if BASE_URL is defined, important for redirection
        return isset($_SESSION['user_id']) && isset($_SESSION['last_activity']) && 
               (time() - $_SESSION['last_activity'] < 3600); // 1 hour session timeout
    }

    public function redirectIfLoggedIn() {
        if($this->isLoggedIn()) {
            // Re-fetch redirect URL based on session role for consistency
            $redirectInfo = $this->getRedirectUrl($_SESSION['role']);
            header("Location: " . $redirectInfo['redirect']);
            exit();
        }
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        // Start a new session immediately after destroying the old one,
        // often useful for flash messages like "You have been logged out."
        session_start(); 
    }
}
?>