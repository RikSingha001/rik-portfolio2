<?php
// Database configuration - Move these to environment variables in production
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
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// CSRF validation function
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    requireLogin();
    
    // CSRF protection for AJAX requests
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit();
    }
    
    switch ($_POST['action']) {
        case 'create_group':
            $name = trim($_POST['name']);
            if (!empty($name) && strlen($name) <= 100) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO `groups` (name, created_by) VALUES (?, ?)");
                    $stmt->execute([$name, $_SESSION['user_id']]);
                    
                    $group_id = $pdo->lastInsertId();
                    
                    // Auto-join creator to the group
                    $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
                    $stmt->execute([$group_id, $_SESSION['user_id']]);
                    
                    echo json_encode(['success' => true, 'message' => 'Group created successfully']);
                } catch(PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Error creating group']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Group name must be 1-100 characters']);
            }
            exit();
            
        case 'join_group':
            $group_id = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
            if ($group_id) {
                try {
                    // Check if already a member
                    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
                    $stmt->execute([$group_id, $_SESSION['user_id']]);
                    
                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['success' => false, 'message' => 'Already a member of this group']);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
                        $stmt->execute([$group_id, $_SESSION['user_id']]);
                        echo json_encode(['success' => true, 'message' => 'Joined group successfully']);
                    }
                } catch(PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Error joining group']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid group ID']);
            }
            exit();
            
        case 'send_message':
            $group_id = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
            $message = trim($_POST['message']);
            
            if (!empty($message) && strlen($message) <= 1000 && $group_id) {
                try {
                    // Check if user is member of the group
                    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
                    $stmt->execute([$group_id, $_SESSION['user_id']]);
                    
                    if ($stmt->rowCount() > 0) {
                        $stmt = $pdo->prepare("INSERT INTO messages (group_id, user_id, message_text) VALUES (?, ?, ?)");
                        $stmt->execute([$group_id, $_SESSION['user_id'], $message]);
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Not a member of this group']);
                    }
                } catch(PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Error sending message']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Message must be 1-1000 characters']);
            }
            exit();
            
        case 'get_messages':
            $group_id = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
            if ($group_id) {
                try {
                    $stmt = $pdo->prepare("
                        SELECT m.message_text, m.sent_at, u.username 
                        FROM messages m 
                        JOIN users u ON m.user_id = u.id 
                        WHERE m.group_id = ? 
                        ORDER BY m.sent_at DESC
                        LIMIT 50
                    ");
                    $stmt->execute([$group_id]);
                    $messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
                    echo json_encode(['success' => true, 'messages' => $messages]);
                } catch(PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Error fetching messages']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid group ID']);
            }
            exit();
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // CSRF protection
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        $login_error = "Invalid security token";
        error_log("CSRF validation failed for login");
    } else {
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        
        if ($email && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                // Debug logging
                error_log("Login attempt for email: " . $email);
                if ($user) {
                    error_log("User found: " . $user['username']);
                    error_log("Password from DB length: " . strlen($user['password']));
                    error_log("Input password length: " . strlen($password));
                    
                    // Check if password is hashed (bcrypt hashes start with $2y$ and are 60 chars long)
                    $is_hashed = (strlen($user['password']) == 60 && strpos($user['password'], '$2y$') === 0);
                    
                    if ($is_hashed) {
                        $password_valid = password_verify($password, $user['password']);
                        error_log("Password verification (hashed): " . ($password_valid ? 'true' : 'false'));
                    } else {
                        // Handle legacy plain text passwords
                        $password_valid = ($password === $user['password']);
                        error_log("Password verification (plain text): " . ($password_valid ? 'true' : 'false'));
                        
                        // If login is successful with plain text, upgrade to hashed password
                        if ($password_valid) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $update_stmt->execute([$hashed_password, $user['id']]);
                            error_log("Password upgraded to hash for user: " . $user['username']);
                        }
                    }
                } else {
                    error_log("No user found with email: " . $email);
                    $password_valid = false;
                }
                
                if ($user && $password_valid) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    error_log("Login successful for user: " . $user['username']);
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $login_error = "Invalid email or password";
                    error_log("Login failed - invalid credentials for email: " . $email);
                }
            } catch(PDOException $e) {
                $login_error = "Database error occurred";
                error_log("Database error during login: " . $e->getMessage());
            }
        } else {
            $login_error = "Please enter valid email and password";
            error_log("Login failed - invalid input format");
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Get user's groups
function getUserGroups($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT g.id, g.name, g.created_at, u.username as creator
        FROM `groups` g
        JOIN group_members gm ON g.id = gm.group_id
        JOIN users u ON g.created_by = u.id
        WHERE gm.user_id = ?
        ORDER BY g.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get available groups to join
function getAvailableGroups($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT g.id, g.name, g.created_at, u.username as creator,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count
        FROM `groups` g
        JOIN users u ON g.created_by = u.id
        WHERE g.id NOT IN (
            SELECT group_id FROM group_members WHERE user_id = ?
        )
        ORDER BY g.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-User Chat Board</title>
    <link rel="stylesheet" href="connect.css">
    <style>
        /* Additional styles for better login form */
        .login-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 50px auto;
            backdrop-filter: blur(10px);
        }
        
        .login-form h2 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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
        
        .debug-info {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>


<div class="bottom-nav">  
  <a href="/pega/index.php">🏠 Home</a>
  <a href="/discover/index.php">🔍 Discover</a>
  <a href="/seasonal/index.php">🎬 Seasonal</a>
</div>
<div id="supportWidget" style="position:fixed;top:50%;left:18px;transform:translateY(-50%);background:#bd5fff;color:#fff;padding:15px 20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.3);font-family:sans-serif;cursor:pointer;z-index:9999;">
  💜 Contact Me
</div>

<div id="contactBox" style="display:none;position:fixed;top:50%;left:90px;transform:translateY(-50%);background:#fff;color:#333;padding:20px;width:250px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.2);z-index:9999;">
  <div id="closeBtn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-weight:bold;font-size:18px;">&times;</div>
  <h3 style="margin-top:0;font-family:sans-serif;">Contact Me</h3>
  <form action="https://formspree.io/f/mrblbawn" method="POST">
    <input type="text" name="name" placeholder="Your name" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;">
    <input type="email" name="email" placeholder="Your email" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;">
    <textarea name="message" placeholder="Your message" rows="3" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"></textarea>
    <button type="submit" style="margin-top:10px;background:#bd5fff;color:#fff;padding:8px;border:none;border-radius:5px;cursor:pointer;width:100%;">Send</button>
  </form>
</div>
<script>
  document.getElementById("supportWidget").onclick = function () {
    const b = document.getElementById("contactBox");
    b.style.display = b.style.display === "block" ? "none" : "block";
  };
  document.getElementById("closeBtn").onclick = function () {
    document.getElementById("contactBox").style.display = "none";
  };
</script>

    <?php if (!isLoggedIn()): ?>
        <div class="container">
            <form class="login-form" method="POST">
                <h2>Login to Chat Board</h2>
                <?php if (isset($login_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                
                <!-- Debug information (remove in production) -->
                <?php if (isset($_POST['login'])): ?>
                    <div class="debug-info">
                        <strong>Debug Info:</strong><br>
                        Email: <?php echo htmlspecialchars($_POST['email'] ?? 'Not provided'); ?><br>
                        Password length: <?php echo strlen($_POST['password'] ?? ''); ?><br>
                        CSRF Token: <?php echo isset($_POST['csrf_token']) ? 'Provided' : 'Missing'; ?><br>
                        Session ID: <?php echo session_id(); ?><br>
                        Time: <?php echo date('Y-m-d H:i:s'); ?>
                    </div>
                <?php endif; ?>
                
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="login">Login</button>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p>Don't have an account? <a href="../register_page.php" style="color: #667eea; text-decoration: none; font-weight: 600;">Register here</a></p>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="header">
                <h1>Chat Board</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="?logout=1" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="main-content">
                <div class="sidebar">
                    <div class="section-title">Create New Group</div>
                    <div class="form-group">
                        <input type="text" id="groupName" placeholder="Enter group name" maxlength="100">
                        <button onclick="createGroup()">Create Group</button>
                    </div>
                    
                    <div class="section-title">My Groups</div>
                    <div class="group-list" id="myGroups">
                        <?php 
                        $userGroups = getUserGroups($pdo, $_SESSION['user_id']);
                        foreach ($userGroups as $group): 
                        ?>
                            <div class="group-item" onclick="selectGroup(<?php echo $group['id']; ?>, '<?php echo htmlspecialchars($group['name'], ENT_QUOTES); ?>')">
                                <div class="group-name"><?php echo htmlspecialchars($group['name']); ?></div>
                                <div class="group-meta">Created by <?php echo htmlspecialchars($group['creator']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="section-title">Available Groups</div>
                    <div class="group-list" id="availableGroups">
                        <?php 
                        $availableGroups = getAvailableGroups($pdo, $_SESSION['user_id']);
                        foreach ($availableGroups as $group): 
                        ?>
                            <div class="group-item">
                                <div class="group-name"><?php echo htmlspecialchars($group['name']); ?></div>
                                <div class="group-meta">
                                    Created by <?php echo htmlspecialchars($group['creator']); ?> • 
                                    <?php echo intval($group['member_count']); ?> members
                                </div>
                                <button class="join-btn" onclick="joinGroup(<?php echo intval($group['id']); ?>)">Join Group</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="chat-area">
                    <div id="chatContent" class="no-group-selected">
                        Select a group to start chatting
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            let currentGroupId = null;
            let currentGroupName = '';
            let messageInterval = null;
            const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
            
            function createGroup() {
                const groupName = document.getElementById('groupName').value.trim();
                if (!groupName) {
                    alert('Please enter a group name');
                    return;
                }
                if (groupName.length > 100) {
                    alert('Group name must be 100 characters or less');
                    return;
                }
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=create_group&name=${encodeURIComponent(groupName)}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
            
            function joinGroup(groupId) {
                if (!groupId || isNaN(groupId)) {
                    alert('Invalid group ID');
                    return;
                }
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=join_group&group_id=${groupId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
            
            function selectGroup(groupId, groupName) {
                if (!groupId || isNaN(groupId)) {
                    alert('Invalid group ID');
                    return;
                }
                
                currentGroupId = groupId;
                currentGroupName = groupName;
                
                // Update active group styling
                document.querySelectorAll('.group-item').forEach(item => {
                    item.classList.remove('active');
                });
                event.currentTarget.classList.add('active');
                
                // Setup chat interface
                setupChatInterface();
                loadMessages();
                
                // Start auto-refresh for messages
                if (messageInterval) {
                    clearInterval(messageInterval);
                }
                messageInterval = setInterval(loadMessages, 3000);
            }
            
            function setupChatInterface() {
                const chatContent = document.getElementById('chatContent');
                chatContent.innerHTML = `
                    <div class="section-title">Chat: ${escapeHtml(currentGroupName)}</div>
                    <div class="messages-container" id="messagesContainer"></div>
                    <form class="message-form" onsubmit="sendMessage(event)">
                        <input type="text" class="message-input" id="messageInput" placeholder="Type your message..." required maxlength="1000">
                        <button type="submit" class="send-btn">Send</button>
                    </form>
                `;
            }
            
            function loadMessages() {
                if (!currentGroupId || isNaN(currentGroupId)) return;
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=get_messages&group_id=${currentGroupId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayMessages(data.messages);
                    } else {
                        console.error('Error loading messages:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            
            function displayMessages(messages) {
                const container = document.getElementById('messagesContainer');
                if (!container) return;
                
                container.innerHTML = '';
                messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message';
                    messageDiv.innerHTML = `
                        <div class="message-header">
                            <span class="message-user">${escapeHtml(message.username)}</span>
                            <span class="message-time">${formatTime(message.sent_at)}</span>
                        </div>
                        <div class="message-text">${escapeHtml(message.message_text)}</div>
                    `;
                    container.appendChild(messageDiv);
                });
                
                // Scroll to bottom
                container.scrollTop = container.scrollHeight;
            }
            
            function sendMessage(event) {
                event.preventDefault();
                
                const messageInput = document.getElementById('messageInput');
                const message = messageInput.value.trim();
                
                if (!message || !currentGroupId || isNaN(currentGroupId)) return;
                
                if (message.length > 1000) {
                    alert('Message is too long (max 1000 characters)');
                    return;
                }
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=send_message&group_id=${currentGroupId}&message=${encodeURIComponent(message)}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        loadMessages();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text || '';
                return div.innerHTML;
            }
            
            function formatTime(timestamp) {
                if (!timestamp) return '';
                const date = new Date(timestamp);
                return date.toLocaleString();
            }
            
            // Cleanup interval when page unloads
            window.addEventListener('beforeunload', function() {
                if (messageInterval) {
                    clearInterval(messageInterval);
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>