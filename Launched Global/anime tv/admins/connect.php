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

// Handle logout
if (isset($_GET['logout'])) {
    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        
        // Clear token from database
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
    }
    
    session_destroy();
    header('Location: login.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current_user) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
} catch(PDOException $e) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Update is_admin in session if it changed
$_SESSION['is_admin'] = $current_user['is_admin'] ?? 0;

// Create default group if none exists
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `groups`");
    $stmt->execute();
    $groupCount = $stmt->fetch()['count'];
    
    if ($groupCount == 0) {
        $stmt = $pdo->prepare("INSERT INTO `groups` (name, created_by) VALUES (?, ?)");
        $stmt->execute(['General Chat', $_SESSION['user_id']]);
        $defaultGroupId = $pdo->lastInsertId();
        
        // Add current user to the group
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->execute([$defaultGroupId, $_SESSION['user_id']]);
    }
} catch(PDOException $e) {
    // Groups might already exist
}

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_group') {
    $groupName = trim($_POST['group_name'] ?? '');
    
    if (!empty($groupName)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO `groups` (name, created_by) VALUES (?, ?)");
            $stmt->execute([$groupName, $_SESSION['user_id']]);
            $newGroupId = $pdo->lastInsertId();
            
            // Add creator to the group
            $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
            $stmt->execute([$newGroupId, $_SESSION['user_id']]);
            
            echo json_encode(['success' => true, 'group_id' => $newGroupId]);
            exit();
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }
}

// Handle joining group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'join_group') {
    $groupId = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
    
    if ($groupId) {
        try {
            // Check if already a member
            $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
            $stmt->execute([$groupId, $_SESSION['user_id']]);
            
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
                $stmt->execute([$groupId, $_SESSION['user_id']]);
            }
            
            echo json_encode(['success' => true]);
            exit();
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $message = trim($_POST['message'] ?? '');
    $groupId = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
    
    if (!empty($message) && $groupId) {
        try {
            // Check if user is member of the group
            $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
            $stmt->execute([$groupId, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO messages (user_id, group_id, message_text) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $groupId, $message]);
                
                echo json_encode(['success' => true]);
                exit();
            } else {
                echo json_encode(['success' => false, 'error' => 'You are not a member of this group']);
                exit();
            }
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }
}

// Get user's groups
function getUserGroups($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT g.*, u.username as creator_name,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count
        FROM `groups` g 
        JOIN users u ON g.created_by = u.id
        JOIN group_members gm ON g.id = gm.group_id
        WHERE gm.user_id = ?
        ORDER BY g.name ASC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all available groups
function getAllGroups($pdo) {
    $stmt = $pdo->prepare("
        SELECT g.*, u.username as creator_name,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count
        FROM `groups` g 
        JOIN users u ON g.created_by = u.id
        ORDER BY g.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// FIXED: Get messages for a group - proper handling of LIMIT parameter
function getGroupMessages($pdo, $group_Id, $limit = 50) {
    // Ensure limit is an integer and within reasonable bounds
    $limit = (int)$limit;
    if ($limit < 1) $limit = 50;
    if ($limit > 1000) $limit = 1000; // Prevent excessive queries
    
    // Use the limit directly in the query since it's now sanitized
    $stmt = $pdo->prepare("
        SELECT m.*, u.username, COALESCE(u.is_admin, 0) as is_admin
        FROM messages m
        JOIN users u ON m.user_id = u.id
        WHERE m.group_id = ?
        ORDER BY m.sent_at DESC
        LIMIT " . $limit
    );
    $stmt->execute([$group_Id]);
    return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
}

$userGroups = getUserGroups($pdo, $_SESSION['user_id']);
$allGroups = getAllGroups($pdo);
$currentGroupId = $userGroups[0]['id'] ?? null;
$messages = $currentGroupId ? getGroupMessages($pdo, $currentGroupId) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Board - <?php echo htmlspecialchars($current_user['username']); ?></title>
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
        }
        
        .chat-container {
            display: flex;
            height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
        }
        
        .sidebar {
            width: 300px;
            background: rgba(255, 255, 255, 0.98);
            border-right: 1px solid #e1e8ed;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }
        
        .user-details h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .user-role {
            font-size: 12px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar-nav {
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            flex: 1;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .groups-section {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }
        
        .group-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .group-item:hover {
            background: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.2);
            transform: translateX(5px);
        }
        
        .group-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .group-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .group-item.active .group-icon {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .group-details {
            flex: 1;
        }
        
        .group-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .group-meta {
            font-size: 11px;
            opacity: 0.7;
        }
        
        .create-group-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .create-group-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .main-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid #e1e8ed;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .chat-title h2 {
            color: #667eea;
            font-size: 20px;
            font-weight: 700;
        }
        
        .online-indicator {
            width: 8px;
            height: 8px;
            background: #2ed573;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .chat-actions {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            padding: 8px 16px;
            border: 1px solid #667eea;
            background: transparent;
            color: #667eea;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .message {
            margin-bottom: 20px;
            animation: slideInUp 0.3s ease;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        
        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .message-username {
            font-weight: 600;
            color: #667eea;
            font-size: 14px;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .message-time {
            font-size: 11px;
            color: #999;
            margin-left: auto;
        }
        
        .message-content {
            background: white;
            padding: 15px 18px;
            border-radius: 15px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-left: 42px;
            line-height: 1.5;
        }
        
        .message.own .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-left: 4px solid rgba(255, 255, 255, 0.3);
            margin-left: 0;
            margin-right: 42px;
        }
        
        .message.own .message-header {
            flex-direction: row-reverse;
        }
        
        .message-input-container {
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.98);
            border-top: 1px solid #e1e8ed;
        }
        
        .message-input-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }
        
        .message-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 25px;
            font-size: 16px;
            resize: none;
            min-height: 50px;
            max-height: 120px;
            transition: all 0.3s ease;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .send-btn {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .send-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .typing-indicator {
            padding: 10px 25px;
            font-size: 12px;
            color: #999;
            font-style: italic;
        }
        
        .no-messages {
            text-align: center;
            padding: 50px;
            color: #999;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            color: #667eea;
            font-size: 20px;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
                height: 100vh;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                max-height: 40vh;
            }
            
            .sidebar-header {
                padding: 15px;
            }
            
            .groups-section {
                padding: 15px;
                max-height: 30vh;
            }
            
            .main-chat {
                flex: 1;
                min-height: 60vh;
            }
            
            .messages-container {
                padding: 15px;
            }
            
            .message-input-container {
                padding: 15px;
            }
            
            .message-content {
                margin-left: 0;
            }
            
            .message.own .message-content {
                margin-right: 0;
            }
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            z-index: 10001;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($current_user['username'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($current_user['username']); ?></h3>
                        <div class="user-role">
                            <?php echo $current_user['is_admin'] ? '🛡️ Administrator' : '👤 User'; ?>
                        </div>
                    </div>
                </div>
                <div class="sidebar-nav">
                    <?php if ($current_user['is_admin']): ?>
                        <button class="nav-btn" onclick="window.open('admin.php', '_blank')">🛡️ Admin Panel</button>
                    <?php endif; ?>
                    <button class="nav-btn" onclick="window.location.href='?logout=1'">🚪 Logout</button>
                </div>
            </div>
            
            <div class="groups-section">
                <button class="create-group-btn" onclick="showCreateGroupModal()">
                    ➕ Create New Group
                </button>
                
                <div class="section-title">💬 My Groups</div>
                <div id="userGroups">
                    <?php foreach ($userGroups as $index => $group): ?>
                        <div class="group-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                             onclick="selectGroup(<?php echo $group['id']; ?>, this)"
                             data-group-id="<?php echo $group['id']; ?>">
                            <div class="group-icon">💬</div>
                            <div class="group-details">
                                <div class="group-name"><?php echo htmlspecialchars($group['name']); ?></div>
                                <div class="group-meta"><?php echo $group['member_count']; ?> members</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-title" style="margin-top: 25px;">🌍 All Groups</div>
                <div id="allGroups">
                    <?php foreach ($allGroups as $group): ?>
                        <?php 
                        $isMember = false;
                        foreach ($userGroups as $userGroup) {
                            if ($userGroup['id'] == $group['id']) {
                                $isMember = true;
                                break;
                            }
                        }
                        ?>
                        <?php if (!$isMember): ?>
                            <div class="group-item" onclick="joinGroup(<?php echo $group['id']; ?>, this)">
                                <div class="group-icon">🔓</div>
                                <div class="group-details">
                                    <div class="group-name"><?php echo htmlspecialchars($group['name']); ?></div>
                                    <div class="group-meta">
                                        <?php echo $group['member_count']; ?> members • 
                                        by <?php echo htmlspecialchars($group['creator_name']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Main Chat Area -->
        <div class="main-chat">
            <div class="chat-header">
                <div class="chat-title">
                    <h2 id="currentGroupName">
                        <?php echo $userGroups ? htmlspecialchars($userGroups[0]['name']) : 'Select a group'; ?>
                    </h2>
                    <div class="online-indicator"></div>
                </div>
                <div class="chat-actions">
                    <button class="action-btn" onclick="refreshMessages()">🔄 Refresh</button>
                    <button class="action-btn" onclick="showGroupInfo()">ℹ️ Info</button>
                </div>
            </div>
            
            <div class="messages-container" id="messagesContainer">
                <?php if ($messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo $message['user_id'] == $_SESSION['user_id'] ? 'own' : ''; ?>">
                            <div class="message-header">
                                <div class="message-avatar">
                                    <?php echo strtoupper(substr($message['username'], 0, 1)); ?>
                                </div>
                                <div class="message-username">
                                    <?php echo htmlspecialchars($message['username']); ?>
                                    <?php if ($message['is_admin']): ?>
                                        <span class="admin-badge">Admin</span>
                                    <?php endif; ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('M j, H:i', strtotime($message['sent_at'])); ?>
                                </div>
                            </div>
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($message['message_text'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-messages">
                        <h3>💬 Start the conversation!</h3>
                        <p>Be the first to send a message in this group.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="typing-indicator" id="typingIndicator" style="display: none;">
                Someone is typing...
            </div>
            
            <div class="message-input-container">
                <form class="message-input-form" onsubmit="sendMessage(event)">
                    <textarea class="message-input" id="messageInput" 
                              placeholder="Type your message here..." 
                              rows="1" 
                              onkeydown="handleKeyDown(event)"
                              oninput="autoResize(this)"></textarea>
                    <button type="submit" class="send-btn" id="sendBtn">
                        🚀
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Create Group Modal -->
    <div class="modal" id="createGroupModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Group</h3>
            </div>
            <form onsubmit="createGroup(event)">
                <div class="form-group">
                    <label for="groupName">Group Name</label>
                    <input type="text" id="groupName" placeholder="Enter group name" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideCreateGroupModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let currentGroupId = <?php echo $currentGroupId ?? 'null'; ?>;
        let messageRefreshInterval;
        
        // Auto-resize textarea
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }
        
        // Handle keyboard shortcuts
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage(event);
            }
        }
        
        // Send message
        async function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const message = messageInput.value.trim();
            
            if (!message || !currentGroupId) return;
            
            sendBtn.disabled = true;
            sendBtn.innerHTML = '⏳';
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=send_message&message=${encodeURIComponent(message)}&group_id=${currentGroupId}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    messageInput.value = '';
                    autoResize(messageInput);
                    refreshMessages();
                } else {
                    showNotification('Error sending message: ' + (result.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                showNotification('Error sending message', 'error');
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '🚀';
            }
        }
        
        // Refresh messages
        async function refreshMessages() {
            if (!currentGroupId) return;
            
            try {
                const response = await fetch(`get_messages.php?group_id=${currentGroupId}`);
                const result = await response.json();
                
                const container = document.getElementById('messagesContainer');
                container.innerHTML = '';
                
                if (!result.success || result.messages.length === 0) {
                    container.innerHTML = `
                        <div class="no-messages">
                            <h3>💬 Start the conversation!</h3>
                            <p>Be the first to send a message in this group.</p>
                        </div>
                    `;
                } else {
                    result.messages.forEach(message => {
                        const messageElement = createMessageElement(message);
                        container.appendChild(messageElement);
                    });
                    
                    container.scrollTop = container.scrollHeight;
                }
            } catch (error) {
                console.error('Error refreshing messages:', error);
            }
        }
        
        // Create message element
        function createMessageElement(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${message.user_id == <?php echo $_SESSION['user_id']; ?> ? 'own' : ''}`;
            
            const adminBadge = message.is_admin ? '<span class="admin-badge">Admin</span>' : '';
            const messageTime = new Date(message.sent_at).toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            messageDiv.innerHTML = `
                <div class="message-header">
                    <div class="message-avatar">
                        ${message.username.charAt(0).toUpperCase()}
                    </div>
                    <div class="message-username">
                        ${message.username}
                        ${adminBadge}
                    </div>
                    <div class="message-time">${messageTime}</div>
                </div>
                <div class="message-content">
                    ${message.message_text.replace(/\n/g, '<br>')}
                </div>
            `;
            
            return messageDiv;
        }
        
        // Select group
        function selectGroup(groupId, element) {
            // Remove active class from all groups
            document.querySelectorAll('.group-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to selected group
            element.classList.add('active');
            
            // Update current group
            currentGroupId = groupId;
            document.getElementById('currentGroupName').textContent = element.querySelector('.group-name').textContent;
            
            // Refresh messages
            refreshMessages();
        }
        
        // Join group
        async function joinGroup(groupId, element) {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=join_group&group_id=${groupId}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Successfully joined the group!');
                    
                    // Move to user groups
                    const groupName = element.querySelector('.group-name').textContent;
                    const memberCount = element.querySelector('.group-meta').textContent.split(' ')[0];
                    
                    const userGroupsContainer = document.getElementById('userGroups');
                    const newGroupElement = document.createElement('div');
                    newGroupElement.className = 'group-item';
                    newGroupElement.setAttribute('data-group-id', groupId);
                    newGroupElement.onclick = () => selectGroup(groupId, newGroupElement);
                    newGroupElement.innerHTML = `
                        <div class="group-icon">💬</div>
                        <div class="group-details">
                            <div class="group-name">${groupName}</div>
                            <div class="group-meta">${parseInt(memberCount) + 1} members</div>
                        </div>
                    `;
                    
                    userGroupsContainer.appendChild(newGroupElement);
                    element.remove();
                } else {
                    showNotification('Error joining group: ' + (result.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                showNotification('Error joining group', 'error');
            }
        }
        
        // Show create group modal
        function showCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'flex';
            document.getElementById('groupName').focus();
        }
        
        // Hide create group modal
        function hideCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'none';
            document.getElementById('groupName').value = '';
        }
        
        // Create group
        async function createGroup(event) {
            event.preventDefault();
            
            const groupName = document.getElementById('groupName').value.trim();
            if (!groupName) return;
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=create_group&group_name=${encodeURIComponent(groupName)}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Group created successfully!');
                    hideCreateGroupModal();
                    
                    // Add to user groups
                    const userGroupsContainer = document.getElementById('userGroups');
                    const newGroupElement = document.createElement('div');
                    newGroupElement.className = 'group-item';
                    newGroupElement.setAttribute('data-group-id', result.group_id);
                    newGroupElement.onclick = () => selectGroup(result.group_id, newGroupElement);
                    newGroupElement.innerHTML = `
                        <div class="group-icon">💬</div>
                        <div class="group-details">
                            <div class="group-name">${groupName}</div>
                            <div class="group-meta">1 member</div>
                        </div>
                    `;
                    
                    userGroupsContainer.appendChild(newGroupElement);
                } else {
                    showNotification('Error creating group: ' + (result.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                showNotification('Error creating group', 'error');
            }
        }
        
        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            
            if (type === 'error') {
                notification.style.background = 'linear-gradient(135deg, #ff4757 0%, #ff3742 100%)';
            }
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }
        
        // Show group info
        function showGroupInfo() {
            if (!currentGroupId) return;
            
            const groupElement = document.querySelector(`[data-group-id="${currentGroupId}"]`);
            if (groupElement) {
                const groupName = groupElement.querySelector('.group-name').textContent;
                const memberCount = groupElement.querySelector('.group-meta').textContent;
                
                alert(`Group: ${groupName}\n${memberCount}\n\nGroup ID: ${currentGroupId}`);
            }
        }
        
        // Auto-refresh messages every 5 seconds
        function startAutoRefresh() {
            messageRefreshInterval = setInterval(refreshMessages, 5000);
        }
        
        function stopAutoRefresh() {
            if (messageRefreshInterval) {
                clearInterval(messageRefreshInterval);
            }
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('createGroupModal');
            if (event.target === modal) {
                hideCreateGroupModal();
            }
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
            
            // Scroll to bottom of messages
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Focus message input
            document.getElementById('messageInput').focus();
        });
        
        // Stop auto-refresh when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });
        
        // Handle page unload
        window.addEventListener('beforeunload', function() {
            stopAutoRefresh();
        });
        
        console.log(`
        💬 Chat Board
        =============
        
        Welcome, <?php echo htmlspecialchars($current_user['username']); ?>!
        
        Features:
        • Real-time messaging
        • Group management
        • Auto-refresh every 5 seconds
        • Admin panel access
        • Responsive design
        
        Keyboard Shortcuts:
        • Enter: Send message
        • Shift + Enter: New line
        
        Current Group: ${currentGroupId ? '<?php echo htmlspecialchars($userGroups[0]['name'] ?? ''); ?>' : 'None'}
        `);
    </script>
</body>
</html>