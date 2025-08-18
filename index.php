<?php
require_once __DIR__ . '/includes/init.php'; // Central initialization

$auth->redirectIfLoggedIn(); // Redirect if already logged in

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if(empty($identifier) || empty($password)) {
        $error = 'Both fields are required';
    } else {
        $result = $auth->login($identifier, $password);
        
        if($result['status'] === 'success') {
            // Redirection is now handled by getRedirectUrl which includes BASE_URL
            header("Location: " . $result['redirect']);
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDMS | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Left Side - Branding -->
            <div class="login-brand">
                <img src="<?= BASE_URL ?>assets/images/logo.png" alt="School Logo" class="logo">
                <h1>Student Database Management</h1>
                <p>Streamlining educational administration for modern institutions</p>
                <div class="features">
                    <div class="feature-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Student Records</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Staff Management</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Attendance Tracking</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="login-form">
                <h2>Sign In</h2>
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form id="loginForm" method="POST" novalidate>
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter your username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        <div class="invalid-feedback">Please enter your username</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" required
                                   placeholder="Enter your password">
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Please enter your password</div>
                    </div>
                    
                    <div class="form-options">
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="<?= BASE_URL ?>forgot-password.php" class="forgot-password">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Login
                    </button>
                </form>
                
                <div class="login-footer">
                    <p>Don't have an account? <a href="<?= BASE_URL ?>contact-admin.php">Contact administrator</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>