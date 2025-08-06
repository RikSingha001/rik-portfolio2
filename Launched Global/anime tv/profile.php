<?php
// Database configuration
$host = $_ENV['DB_HOST'] ?? "localhost";
$dbuser = $_ENV['DB_USER'] ?? "root";
$dbpass = $_ENV['DB_PASS'] ?? "Riksingha@615";
$dbname = $_ENV['DB_NAME'] ?? "user_auth";

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Session security
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}

session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connect/connect.php');
    exit();
}

// CSRF validation function
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Get current user data
function getCurrentUser($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT username, email, phone FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$user = getCurrentUser($pdo, $_SESSION['user_id']);
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token";
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_profile':
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $phone = trim($_POST['phone']);
                
                // Validation
                if (empty($username) || empty($email) || empty($phone)) {
                    $error = "All fields are required.";
                } elseif (strlen($username) < 3) {
                    $error = "Username must be at least 3 characters long.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid email format.";
                } else {
                    try {
                        // Check if email is already taken by another user
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                        $stmt->execute([$email, $_SESSION['user_id']]);
                        
                        if ($stmt->rowCount() > 0) {
                            $error = "Email already exists. Please use a different email.";
                        } else {
                            // Update user information
                            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
                            $stmt->execute([$username, $email, $phone, $_SESSION['user_id']]);
                            
                            // Update session username
                            $_SESSION['username'] = $username;
                            
                            // Refresh user data
                            $user = getCurrentUser($pdo, $_SESSION['user_id']);
                            $success = "Profile updated successfully!";
                        }
                    } catch(PDOException $e) {
                        $error = "Error updating profile. Please try again.";
                        error_log("Profile update error: " . $e->getMessage());
                    }
                }
                break;
                
            case 'change_password':
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                // Validation
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $error = "All password fields are required.";
                } elseif (strlen($new_password) < 6) {
                    $error = "New password must be at least 6 characters long.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "New passwords do not match.";
                } else {
                    try {
                        // Get current password hash
                        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $user_data = $stmt->fetch();
                        
                        // Verify current password
                        $password_valid = false;
                        if ($user_data) {
                            // Check if password is hashed
                            $is_hashed = (strlen($user_data['password']) == 60 && strpos($user_data['password'], '$2y$') === 0);
                            
                            if ($is_hashed) {
                                $password_valid = password_verify($current_password, $user_data['password']);
                            } else {
                                // Handle legacy plain text passwords
                                $password_valid = ($current_password === $user_data['password']);
                            }
                        }
                        
                        if ($password_valid) {
                            // Hash new password
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            
                            // Update password
                            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                            
                            $success = "Password changed successfully!";
                        } else {
                            $error = "Current password is incorrect.";
                        }
                    } catch(PDOException $e) {
                        $error = "Error changing password. Please try again.";
                        error_log("Password change error: " . $e->getMessage());
                    }
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Chat Board</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 2.5em;
            font-weight: 600;
        }
        
        .nav-buttons {
            display: flex;
            gap: 15px;
        }
        
        .nav-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .profile-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #667eea;
            font-size: 1.8em;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e1e8ed;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            justify-self: start;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
            text-align: center;
            font-weight: 500;
        }
        
        .success {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
            text-align: center;
            font-weight: 500;
        }
        
        .password-strength {
            height: 4px;
            background: #e1e8ed;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: #ff6b6b; width: 33%; }
        .strength-medium { background: #ffa726; width: 66%; }
        .strength-strong { background: #4caf50; width: 100%; }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .user-info-display {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid #e1e8ed;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
            font-family: monospace;
            background: white;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-section {
                padding: 20px;
            }
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-top: 1px solid #e1e8ed;
            display: flex;
            justify-content: center;
            gap: 30px;
            z-index: 1000;
        }
        
        .bottom-nav a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .bottom-nav a:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Profile Settings</h1>
            <div class="nav-buttons">
                <a href="../connect/connect.php" class="nav-btn">Back to Chat</a>
                <a href="../connect/connect.php?logout=1" class="nav-btn">Logout</a>
            </div>
        </div>
        
        <!-- Current User Info Display -->
        <div class="profile-section">
            <div class="section-title">Current Profile Information</div>
            <div class="user-info-display">
                <div class="info-item">
                    <span class="info-label">Username:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">User ID:</span>
                    <span class="info-value"><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Update Profile Section -->
        <div class="profile-section">
            <div class="section-title">Update Profile Information</div>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" id="profileForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($user['username']); ?>"
                               minlength="3" maxlength="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" required 
                               value="<?php echo htmlspecialchars($user['phone']); ?>"
                               maxlength="20">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               maxlength="150">
                    </div>
                    
                    <div class="form-group full-width">
                        <button type="submit" class="submit-btn">Update Profile</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Change Password Section -->
        <div class="profile-section">
            <div class="section-title">Change Password</div>
            
            <form method="POST" id="passwordForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required 
                               minlength="6" maxlength="255">
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-requirements">
                            At least 6 characters long
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               minlength="6" maxlength="255">
                    </div>
                    
                    <div class="form-group full-width">
                        <button type="submit" class="submit-btn">Change Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="bottom-nav">  
        <a href="/pega/index.php">Home</a>
        <a href="/discover/index.php">Discover</a>
        <a href="/seasonal/index.php">Seasonal</a>
    </div>
    
    <script>
        // Password strength indicator
        const newPasswordInput = document.getElementById('new_password');
        const strengthBar = document.getElementById('strengthBar');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check password strength
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            // Update strength bar
            strengthBar.className = 'password-strength-bar';
            if (strength >= 1) strengthBar.classList.add('strength-weak');
            if (strength >= 2) strengthBar.classList.add('strength-medium');
            if (strength >= 3) strengthBar.classList.add('strength-strong');
        });
        
        // Password confirmation validation
        confirmPasswordInput.addEventListener('input', function() {
            const newPassword = newPasswordInput.value;
            const confirm = this.value;
            
            if (confirm && newPassword !== confirm) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Form validation for password change
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = newPasswordInput.value;
            const confirm = confirmPasswordInput.value;
            
            if (newPassword !== confirm) {
                e.preventDefault();
                alert('New passwords do not match!');
                return false;
            }
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long!');
                return false;
            }
        });
        
        // Profile form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            
            if (username.length < 3) {
                e.preventDefault();
                alert('Username must be at least 3 characters long!');
                return false;
            }
            
            if (!email || !email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address!');
                return false;
            }
            
            if (!phone) {
                e.preventDefault();
                alert('Please enter a phone number!');
                return false;
            }
        });
        
        // Auto-focus first input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Clear success/error messages after 5 seconds
        setTimeout(function() {
            const successDiv = document.querySelector('.success');
            const errorDiv = document.querySelector('.error');
            
            if (successDiv) {
                successDiv.style.opacity = '0';
                successDiv.style.transition = 'opacity 0.5s ease';
                setTimeout(() => successDiv.remove(), 500);
            }
            
            if (errorDiv) {
                errorDiv.style.opacity = '0';
                errorDiv.style.transition = 'opacity 0.5s ease';
                setTimeout(() => errorDiv.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>