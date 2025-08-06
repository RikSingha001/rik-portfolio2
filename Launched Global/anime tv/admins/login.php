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

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: connect.php');
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');  // Can be username or email
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($login) || empty($password)) {
        $message = "Please enter both username/email and password.";
        $messageType = "error";
    } else {
        try {
            // Check if login is email or username
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
                
                // Set remember me cookie if requested
                if ($remember) {
                    $cookie_token = bin2hex(random_bytes(32));
                    $cookie_expiry = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store token in database (you might want to create a separate table for this)
                    $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$cookie_token, $user['id']]);
                    
                    setcookie('remember_token', $cookie_token, $cookie_expiry, '/', '', false, true);
                }
                
                // Redirect based on admin status
                if ($user['is_admin']) {
                    header('Location: admin.php');
                } else {
                    header('Location: connect.php');
                }
                exit();
            } else {
                $message = "Invalid username/email or password.";
                $messageType = "error";
            }
        } catch(PDOException $e) {
            $message = "Login failed: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle remember me cookie
if (isset($_COOKIE['remember_token']) && !isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$_COOKIE['remember_token']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            
            if ($user['is_admin']) {
                header('Location: admin.php');
            } else {
                header('Location: connect.php');
            }
            exit();
        }
    } catch(PDOException $e) {
        // Invalid token, clear cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chat Board</title>
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
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            width: 100%;
            max-width: 450px;
            animation: slideIn 0.8s ease;
            position: relative;
            z-index: 10;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header h1 {
            color: #667eea;
            font-size: 2.8em;
            font-weight: 800;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-header p {
            color: #666;
            font-size: 16px;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-group input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            transform: translateY(-3px);
            background: white;
        }
        
        .form-group input::placeholder {
            color: #999;
        }
        
        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #667eea;
            pointer-events: none;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }
        
        .checkbox-wrapper label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            text-transform: none;
            letter-spacing: normal;
            margin: 0;
            cursor: pointer;
        }
        
        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(-1px);
        }
        
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .login-btn:hover::before {
            left: 100%;
        }
        
        .register-link {
            text-align: center;
            color: #666;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .alert {
            padding: 18px;
            border-radius: 15px;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
            animation: fadeIn 0.5s ease;
            position: relative;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #ffebee 0%, #f8d7da 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        .floating-shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 15%;
            animation-delay: 0s;
        }
        
        .floating-shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 70%;
            right: 15%;
            animation-delay: 3s;
        }
        
        .floating-shape:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 15%;
            left: 10%;
            animation-delay: 6s;
        }
        
        .floating-shape:nth-child(4) {
            width: 60px;
            height: 60px;
            top: 30%;
            right: 20%;
            animation-delay: 1.5s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            33% { transform: translateY(-30px) rotate(120deg); opacity: 0.4; }
            66% { transform: translateY(20px) rotate(240deg); opacity: 0.8; }
        }
        
        .demo-accounts {
            background: rgba(102, 126, 234, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .demo-accounts h4 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .demo-account:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateX(5px);
        }
        
        .demo-account:last-child {
            margin-bottom: 0;
        }
        
        .demo-info {
            display: flex;
            flex-direction: column;
        }
        
        .demo-username {
            font-weight: 600;
            color: #667eea;
        }
        
        .demo-role {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .use-demo-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .use-demo-btn:hover {
            background: #764ba2;
            transform: scale(1.05);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .login-container {
                padding: 30px 25px;
                margin: 15px;
            }
            
            .login-header h1 {
                font-size: 2.2em;
            }
            
            .checkbox-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .demo-accounts {
                padding: 15px;
            }
            
            .demo-account {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #667eea;
            font-size: 18px;
            user-select: none;
        }
        
        .password-toggle:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>
    
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Welcome Back</h1>
            <p>Sign in to your Chat Board account</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Demo Accounts Section -->
        <div class="demo-accounts">
            <h4>🎯 Quick Demo Access</h4>
            <div class="demo-account" onclick="fillDemoCredentials('admin', 'password123')">
                <div class="demo-info">
                    <div class="demo-username">admin</div>
                    <div class="demo-role">Administrator</div>
                </div>
                <button class="use-demo-btn">Use Demo</button>
            </div>
            <div class="demo-account" onclick="fillDemoCredentials('demo@example.com', 'demo123')">
                <div class="demo-info">
                    <div class="demo-username">demo@example.com</div>
                    <div class="demo-role">Regular User</div>
                </div>
                <button class="use-demo-btn">Use Demo</button>
            </div>
        </div>
        
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="login">Username or Email</label>
                <div class="input-wrapper">
                    <input type="text" id="login" name="login" placeholder="Enter username or email" 
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
                    <div class="input-icon">👤</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <div class="password-toggle" onclick="togglePassword()">👁️</div>
                </div>
            </div>
            
            <div class="checkbox-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me for 30 days</label>
                </div>
                <a href="#" class="forgot-password" onclick="showForgotPassword()">Forgot Password?</a>
            </div>
            
            <button type="submit" class="login-btn" id="loginBtn">
                🚀 Sign In
            </button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Create one here</a>
        </div>
    </div>
    
    <script>
        // Fill demo credentials
        function fillDemoCredentials(username, password) {
            document.getElementById('login').value = username;
            document.getElementById('password').value = password;
            
            // Add visual feedback
            const loginInput = document.getElementById('login');
            const passwordInput = document.getElementById('password');
            
            loginInput.style.borderColor = '#2ed573';
            passwordInput.style.borderColor = '#2ed573';
            
            setTimeout(() => {
                loginInput.style.borderColor = '#e1e8ed';
                passwordInput.style.borderColor = '#e1e8ed';
            }, 2000);
        }
        
        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
        
        // Form submission with loading state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        loginForm.addEventListener('submit', function(e) {
            // Show loading state
            loginBtn.innerHTML = '⏳ Signing In...';
            loginBtn.disabled = true;
            loadingOverlay.style.display = 'flex';
            
            // Add some delay to show loading effect
            setTimeout(() => {
                // The form will submit naturally
            }, 500);
        });
        
        // Input focus animations
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.style.boxShadow = '0 0 0 4px rgba(102, 126, 234, 0.15)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
                if (this.value) {
                    this.style.borderColor = '#2ed573';
                }
            });
            
            // Real-time validation
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.style.borderColor = '#667eea';
                } else {
                    this.style.borderColor = '#e1e8ed';
                }
            });
        });
        
        // Forgot password functionality
        function showForgotPassword() {
            const email = prompt('Enter your email address to reset password:');
            if (email) {
                // In a real application, you would send a request to a password reset endpoint
                alert('Password reset link would be sent to: ' + email + '\n\n(This is a demo - no email will be sent)');
            }
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter key on demo accounts
            if (e.key === 'Enter' && e.target.closest('.demo-account')) {
                e.target.closest('.demo-account').click();
            }
            
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                loginForm.submit();
            }
        });
        
        // Auto-fill last used credentials (for demo purposes)
        window.addEventListener('load', function() {
            const lastLogin = localStorage.getItem('lastLogin');
            if (lastLogin && !document.getElementById('login').value) {
                document.getElementById('login').value = lastLogin;
            }
        });
        
        // Save last used login for convenience
        loginForm.addEventListener('submit', function() {
            const loginValue = document.getElementById('login').value;
            localStorage.setItem('lastLogin', loginValue);
        });
        
        // Add ripple effect to demo accounts
        document.querySelectorAll('.demo-account').forEach(account => {
            account.addEventListener('click', function(e) {
                const ripple = document.createElement('div');
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(102, 126, 234, 0.3);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                
                this.style.position = 'relative';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Console welcome message
        console.log(`
        🔐 Chat Board Login
        ==================
        
        Demo Accounts Available:
        • Admin: admin / password123
        • User: demo@example.com / demo123
        
        Features:
        • Remember me functionality
        • Password visibility toggle
        • Real-time validation
        • Responsive design
        • Loading states
        
        Keyboard Shortcuts:
        • Ctrl/Cmd + Enter: Submit form
        • Click demo accounts for quick access
        `);
        
        // Add some interactive elements
        const loginContainer = document.querySelector('.login-container');
        
        loginContainer.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        loginContainer.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
        
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>