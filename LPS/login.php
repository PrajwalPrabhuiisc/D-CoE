<?php
session_start();
require 'db_connect.php';

$success_message = '';
$error_message = '';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // For demo, use plaintext password; in production, use password_verify()
        if ($user && $password === 'pass123') {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . ($user['role'] == 'Planner' ? 'index.php' : 'team.php'));
            exit;
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}

// Handle User Creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $new_username = $_POST['new_username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($new_username) || empty($new_password) || empty($confirm_password) || empty($role)) {
        $error_message = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (!in_array($role, ['Planner', 'SA_Team'])) {
        $error_message = 'Invalid role selected.';
    } else {
        // Check if username exists
        $check_query = "SELECT COUNT(*) FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('s', $new_username);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            $error_message = 'Username already taken.';
        } else {
            // For demo, store plaintext password; in production, use password_hash()
            $insert_query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('sss', $new_username, $new_password, $role);
            if ($insert_stmt->execute()) {
                $success_message = 'Account created successfully! Please log in.';
            } else {
                $error_message = 'Failed to create account. Try again.';
            }
            $insert_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LPS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }
        .auth-card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            transition: transform 0.3s ease;
        }
        .auth-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(90deg, #6366F1, #4F46E5);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366F1;
            box-shadow: 0 0 5px rgba(99, 102, 241, 0.3);
        }
        .btn-primary {
            background: #6366F1;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: #4F46E5;
            transform: translateY(-2px);
        }
        .btn-outline-secondary {
            border-color: #64748B;
            color: #64748B;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background: #64748B;
            color: white;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .toggle-link {
            color: #6366F1;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }
        .toggle-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .auth-card {
                margin: 1rem;
            }
            .card-header {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card fade-in">
        <div class="card-header" id="form-header">
            Login
        </div>
        <div class="card-body p-4">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div id="login-form" class="form-container">
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-medium">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3">
                    Don't have an account? <span class="toggle-link" onclick="toggleForm('register')">Create one</span>
                </p>
            </div>

            <!-- Register Form -->
            <div id="register-form" class="form-container" style="display: none;">
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label for="new_username" class="form-label fw-medium">Username</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" value="<?php echo isset($new_username) ? htmlspecialchars($new_username) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-medium">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label fw-medium">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" disabled selected>Select role</option>
                            <option value="Planner" <?php echo isset($role) && $role == 'Planner' ? 'selected' : ''; ?>>Planner</option>
                            <option value="SA_Team" <?php echo isset($role) && $role == 'SA_Team' ? 'selected' : ''; ?>>SA Team</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
                <p class="text-center mt-3">
                    Already have an account? <span class="toggle-link" onclick="toggleForm('login')">Log in</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        function toggleForm(formType) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const header = document.getElementById('form-header');

            if (formType === 'login') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                header.textContent = 'Login';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                header.textContent = 'Create Account';
            }
        }
    </script>
</body>
</html>