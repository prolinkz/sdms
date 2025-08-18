<?php
// UserManager.php
// Manages CRUD operations for the 'users' table

class UserManager {
    private $db;

    public function __construct(PDO $db_connection) {
        $this->db = $db_connection;
    }

    /**
     * Creates a new user in the database.
     * @param array $userData An associative array containing user details.
     *   Expected keys: 'username', 'email', 'password', 'role', 'status', 'created_by'.
     * @return array Status and message or new user ID on success.
     */
    public function createUser(array $userData) {
        // Basic validation (more comprehensive validation will be on the form side)
        if (empty($userData['username']) || empty($userData['email']) || empty($userData['password']) || empty($userData['role'])) {
            return ['status' => 'error', 'message' => 'Missing required user information.'];
        }

        // Hash the password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            error_log("Password hashing failed for user: " . $userData['username']);
            return ['status' => 'error', 'message' => 'Failed to process password.'];
        }

        try {
            // Check for duplicate username or email
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
            $stmt->execute([':username' => $userData['username'], ':email' => $userData['email']]);
            if ($stmt->fetchColumn() > 0) {
                return ['status' => 'error', 'message' => 'Username or Email already exists.'];
            }

            $stmt = $this->db->prepare("
                INSERT INTO users (username, password, email, role, status, created_by)
                VALUES (:username, :password, :email, :role, :status, :created_by)
            ");

            $stmt->execute([
                ':username' => $userData['username'],
                ':password' => $hashedPassword,
                ':email' => $userData['email'],
                ':role' => $userData['role'],
                ':status' => $userData['status'] ?? 'Active', // Default to Active if not provided
                ':created_by' => $userData['created_by'] // User ID of the logged-in admin
            ]);

            return ['status' => 'success', 'message' => 'User created successfully.', 'user_id' => $this->db->lastInsertId()];

        } catch (PDOException $e) {
            error_log('User Creation Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'A database error occurred during user creation.'];
        }
    }

    /**
     * Retrieves a user by their ID.
     * @param int $userId The ID of the user to retrieve.
     * @return array|false User data as an associative array, or false if not found.
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :user_id LIMIT 1");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('GetUserById Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all users or users by role.
     * @param string|null $role If provided, filter users by this role.
     * @return array List of users.
     */
    public function getAllUsers($role = null) {
        try {
            $sql = "SELECT user_id, username, email, role, status, last_login, created_at, updated_at FROM users";
            $params = [];
            if ($role) {
                $sql .= " WHERE role = :role";
                $params[':role'] = $role;
            }
            $sql .= " ORDER BY username ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('GetAllUsers Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Updates an existing user.
     * @param int $userId The ID of the user to update.
     * @param array $userData An associative array of data to update.
     *   Expected keys: 'username', 'email', 'role', 'status', 'password' (optional).
     * @param int $updatedBy The user ID of the person performing the update.
     * @return array Status and message.
     */
    public function updateUser($userId, array $userData, $updatedBy) {
        if (empty($userId) || empty($updatedBy)) {
            return ['status' => 'error', 'message' => 'Invalid user ID or updater ID.'];
        }

        $fields = [];
        $params = [':user_id' => $userId, ':updated_by' => $updatedBy];

        if (isset($userData['username'])) {
            $fields[] = 'username = :username';
            $params[':username'] = $userData['username'];
        }
        if (isset($userData['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $userData['email'];
        }
        if (isset($userData['role'])) {
            $fields[] = 'role = :role';
            $params[':role'] = $userData['role'];
        }
        if (isset($userData['status'])) {
            $fields[] = 'status = :status';
            $params[':status'] = $userData['status'];
        }
        if (isset($userData['password']) && !empty($userData['password'])) {
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                return ['status' => 'error', 'message' => 'Failed to hash new password.'];
            }
            $fields[] = 'password = :password';
            $params[':password'] = $hashedPassword;
        }

        if (empty($fields)) {
            return ['status' => 'error', 'message' => 'No data provided for update.'];
        }

        try {
             // Check for duplicate username/email if they are being updated to existing ones
            if (isset($userData['username']) || isset($userData['email'])) {
                $duplicateCheckSql = "SELECT COUNT(*) FROM users WHERE user_id != :user_id";
                $duplicateCheckParams = [':user_id' => $userId];
                
                if (isset($userData['username'])) {
                    $duplicateCheckSql .= " AND username = :username";
                    $duplicateCheckParams[':username'] = $userData['username'];
                }
                if (isset($userData['email'])) {
                    if (isset($userData['username'])) {
                        $duplicateCheckSql .= " OR email = :email"; // Use OR if checking both
                    } else {
                        $duplicateCheckSql .= " AND email = :email"; // Use AND if only checking email
                    }
                    $duplicateCheckParams[':email'] = $userData['email'];
                }
                
                $stmt = $this->db->prepare($duplicateCheckSql);
                $stmt->execute($duplicateCheckParams);
                if ($stmt->fetchColumn() > 0) {
                    return ['status' => 'error', 'message' => 'Username or Email already exists for another user.'];
                }
            }


            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP, updated_by = :updated_by WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'User updated successfully.'];
            } else {
                return ['status' => 'info', 'message' => 'No changes made to user or user not found.'];
            }

        } catch (PDOException $e) {
            error_log('User Update Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'A database error occurred during user update.'];
        }
    }

    /**
     * Deletes a user from the database.
     * @param int $userId The ID of the user to delete.
     * @return array Status and message.
     */
    public function deleteUser($userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);

            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'User deleted successfully.'];
            } else {
                return ['status' => 'error', 'message' => 'User not found.'];
            }
        } catch (PDOException $e) {
            error_log('User Delete Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'A database error occurred during user deletion.'];
        }
    }
}
?>