<?php
// Database configuration
$host = "localhost";
$dbuser = "root";
$dbpass = "Riksingha@615";
$dbname = "user_auth";

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Email already exists. Please use a different email.";
            } else {
                // Hash password properly before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $phone, $hashed_password]);
                
                $success = "Registration successful! You can now login.";
                
                // Optional: Auto-login the user
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                
                // Redirect to chat after successful registration
                header("Location: ../connect/connect.php");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Registration failed. Please try again.";
            // Log the actual error for debugging
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Chat Board</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }
        
        .nav-buttons {
            text-align: center;
        }
        
        h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
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
            margin-top: 10px;
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e8ed;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
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
        
        @media (max-width: 480px) {
            .register-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="nav-buttons">
            <h1>Register</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form id="registerForm" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           minlength="3" maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           maxlength="150">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                           maxlength="20">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required 
                           minlength="6" maxlength="255">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-requirements">
                        At least 6 characters long
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" required 
                           minlength="6" maxlength="255">
                </div>
                
                <button type="submit" class="submit-btn">Create Account</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="index.php">Login here</a>
            </div>
        </div>
    </div>
 
    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const confirmPassword = document.getElementById('confirm-password');
        
        passwordInput.addEventListener('input', function() {
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
        confirmPassword.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmPassword.value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
        
        // Auto-focus first input
        document.getElementById('username').focus();
    </script>
    <div id="supportWidget" style="position:fixed;bottom:18px;right:18px;background:#bd5fff;color:#fff;padding:15px 20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.3);font-family:sans-serif;cursor:pointer;z-index:9999;">💜 Contact Me</div><div id="contactBox" style="display:none;position:fixed;bottom:80px;right:18px;background:#fff;color:#333;padding:20px;width:250px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.2);z-index:9999;"><div id="closeBtn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-weight:bold;font-size:18px;">&times;</div><h3 style="margin-top:0;font-family:sans-serif;">Contact Me</h3><form action="https://formspree.io/f/mrblbawn" method="POST"><input type="text" name="name" placeholder="Your name" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"><input type="email" name="email" placeholder="Your email" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"><textarea name="message" placeholder="Your message" rows="3" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"></textarea><button type="submit" style="margin-top:10px;background:#bd5fff;color:#fff;padding:8px;border:none;border-radius:5px;cursor:pointer;width:100%;">Send</button></form></div><script>document.getElementById("supportWidget").onclick=function(){const b=document.getElementById("contactBox");b.style.display=b.style.display==="block"?"none":"block"};document.getElementById("closeBtn").onclick=function(){document.getElementById("contactBox").style.display="none";};</script>
</body>
</html>