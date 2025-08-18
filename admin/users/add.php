<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/helpers.php'; // For isActiveLink

// Check if user is logged in AND has the 'Admin' or 'Administrator' role
if (!$auth->isLoggedIn() || !in_array($_SESSION['role'], ['Admin', 'Administrator'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

$page_title = "Add New User";
$page_heading = "User Management"; // Used in the top-navbar

$errors = [];
$success_message = '';
$form_data = [
    'first_name' => '', // Assuming you'd want to capture these for staff/student/parent profiles later
    'last_name' => '',
    'username' => '',
    'email' => '',
    'role' => '',
    'status' => 'Active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $form_data['first_name'] = trim($_POST['first_name'] ?? '');
    $form_data['last_name'] = trim($_POST['last_name'] ?? '');
    $form_data['username'] = trim($_POST['username'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $form_data['role'] = $_POST['role'] ?? '';
    $form_data['status'] = $_POST['status'] ?? 'Active';
    $created_by = $_SESSION['user_id']; // The ID of the logged-in admin

    // Server-side Validation
    if (empty($form_data['first_name'])) {
        $errors['first_name'] = 'First Name is required.';
    }
    if (empty($form_data['last_name'])) {
        $errors['last_name'] = 'Last Name is required.';
    }
    if (empty($form_data['username'])) {
        $errors['username'] = 'Username is required.';
    }
    if (empty($form_data['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) { // Example: minimum 6 characters
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }
    if (empty($form_data['role'])) {
        $errors['role'] = 'Role is required.';
    }

    // List of allowed roles from the database schema
    $allowed_roles = ['Admin', 'Principal', 'Administrator', 'Accountant', 'Teacher', 'Student', 'Parent'];
    if (!in_array($form_data['role'], $allowed_roles)) {
        $errors['role'] = 'Invalid role selected.';
    }
    
    // If no validation errors, proceed to create user
    if (empty($errors)) {
        $result = $user_manager->createUser([
            'username' => $form_data['username'],
            'email' => $form_data['email'],
            'password' => $password,
            'role' => $form_data['role'],
            'status' => $form_data['status'],
            'created_by' => $created_by
        ]);

        if ($result['status'] === 'success') {
            $success_message = $result['message'];
            // Clear form data on successful submission for a fresh form
            $form_data = [
                'first_name' => '',
                'last_name' => '',
                'username' => '',
                'email' => '',
                'role' => '',
                'status' => 'Active'
            ];
            // After successful user creation, it's a good practice to log the action
            $auth->logAccess($created_by, 'CREATE_USER', 'users', $result['user_id'], 'New user ' . $form_data['username'] . ' created with role ' . $form_data['role']);
        } else {
            // Store the error message from UserManager
            $errors['general'] = $result['message'];
        }
    }
}

// List of roles for the dropdown
$roles = ['Admin', 'Principal', 'Administrator', 'Accountant', 'Teacher', 'Student', 'Parent'];
?>

<?php include __DIR__ . '/../../layouts/admin_header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Add New User</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>admin/users/add.php" method="POST" novalidate id="addUserForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" id="first_name" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($form_data['first_name']) ?>" required>
                            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= $errors['first_name'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" id="last_name" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($form_data['last_name']) ?>" required>
                            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= $errors['last_name'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($form_data['username']) ?>" required>
                            <?php if (isset($errors['username'])): ?><div class="invalid-feedback"><?= $errors['username'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($form_data['email']) ?>" required>
                            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= $errors['password'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['confirm_password'])): ?><div class="invalid-feedback"><?= $errors['confirm_password'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-control <?= isset($errors['role']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role) ?>" <?= ($form_data['role'] === $role) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['role'])): ?><div class="invalid-feedback"><?= $errors['role'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="Active" <?= ($form_data['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= ($form_data['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-right mt-4">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="document.getElementById('addUserForm').reset();">Clear</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Add specific styles for forms in dashboard context */
    .container-fluid {
        padding: 30px; /* Adjust padding to match page-content */
    }
    .card {
        background-color: var(--card-bg);
        border-radius: 8px;
        box-shadow: var(--shadow-light);
        margin-bottom: 20px;
        text-align: start;
    }
    .card-header {
        background-color: var(--body-bg); /* Lighter header background */
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
        font-size: 1.2em;
        font-weight: 500;
        color: var(--text-color);
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .card-body {
        padding: 20px;
    }
    .form-group {
        margin-bottom: 15px; /* Reduced margin for tighter layout */
    }
    .form-group label {
        font-weight: 500;
        color: var(--text-color);
    }
    .form-control {
        display: block;
        width: 100%;
        padding: 10px 15px;
        font-size: 1em;
        line-height: 1.5;
        color: var(--text-color);
        background-color: var(--card-bg);
        background-clip: padding-box;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }
    .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + .75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        background-size: 1.1em 1.1em;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    /* Row and column styles for responsive layout */
    .row {
        display: flex;
        flex-wrap: nowrap;
        margin-right: -15px; /* Negative margin to compensate for column padding */
        margin-left: -15px;
        justify-content: center;
    }
    .col-md-6 {
        flex: 0 0 auto;
        width: 50%;
        padding-right: 15px;
        padding-left: 15px;
    }
    /* Button group alignment */
    .form-group.text-right {
        text-align: right;
    }
    .form-group.text-right .btn {
        width: auto; /* Override 100% width from general .btn */
        margin-left: 10px; /* Space between buttons */
    }

    @media (max-width: 768px) {
        .col-md-6 {
            width: 100%; /* Stack columns on smaller screens */
        }
        .form-group.text-right {
            text-align: center; /* Center buttons on small screens */
        }
        .form-group.text-right .btn {
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 10px; /* Stack buttons if needed */
        }
    }
</style>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>