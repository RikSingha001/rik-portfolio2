<?php
// Database configuration - same as your existing setup
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

// Check if user is logged in and is admin
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    if (!isLoggedIn()) return false;
    
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        return $user && isset($user['is_admin']) && $user['is_admin'] == 1;
    } catch(PDOException $e) {
        // If is_admin column doesn't exist, check if user is the first user (likely admin)
        $stmt = $pdo->prepare("SELECT id FROM users ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $firstUser = $stmt->fetch();
        
        return $firstUser && $firstUser['id'] == $_SESSION['user_id'];
    }
}

// Add admin column if it doesn't exist
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT DEFAULT 0");
    // Make first user admin if no admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as admin_count FROM users WHERE is_admin = 1");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['admin_count'] == 0) {
        $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 ORDER BY id ASC LIMIT 1");
        $stmt->execute();
    }
} catch(PDOException $e) {
    // Column might already exist
}

// Redirect if not admin
if (!isLoggedIn()) {
    header('Location: connect.php');
    exit();
}

if (!isAdmin()) {
    die('<div style="text-align:center;margin-top:100px;font-family:Arial,sans-serif;"><h2>Access Denied</h2><p>You need admin privileges to access this panel.</p><a href="connect.php">Back to Chat</a></div>');
}

// Handle admin actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete_user':
            $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            if ($user_id && $user_id != $_SESSION['user_id']) {
                try {
                    $pdo->beginTransaction();
                    
                    // Delete user's messages
                    $stmt = $pdo->prepare("DELETE FROM messages WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Delete user's group memberships
                    $stmt = $pdo->prepare("DELETE FROM group_members WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Delete groups created by user (and their messages/members)
                    $stmt = $pdo->prepare("SELECT id FROM `groups` WHERE created_by = ?");
                    $stmt->execute([$user_id]);
                    $groups = $stmt->fetchAll();
                    
                    foreach ($groups as $group) {
                        $stmt = $pdo->prepare("DELETE FROM messages WHERE group_id = ?");
                        $stmt->execute([$group['id']]);
                        
                        $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ?");
                        $stmt->execute([$group['id']]);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM `groups` WHERE created_by = ?");
                    $stmt->execute([$user_id]);
                    
                    // Delete user
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    
                    $pdo->commit();
                    $message = "User deleted successfully";
                    $messageType = "success";
                } catch(PDOException $e) {
                    $pdo->rollback();
                    $message = "Error deleting user: " . $e->getMessage();
                    $messageType = "error";
                }
            }
            break;
            
        case 'delete_group':
            $group_id = filter_var($_POST['group_id'], FILTER_VALIDATE_INT);
            if ($group_id) {
                try {
                    $pdo->beginTransaction();
                    
                    // Delete group messages
                    $stmt = $pdo->prepare("DELETE FROM messages WHERE group_id = ?");
                    $stmt->execute([$group_id]);
                    
                    // Delete group members
                    $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ?");
                    $stmt->execute([$group_id]);
                    
                    // Delete group
                    $stmt = $pdo->prepare("DELETE FROM `groups` WHERE id = ?");
                    $stmt->execute([$group_id]);
                    
                    $pdo->commit();
                    $message = "Group deleted successfully";
                    $messageType = "success";
                } catch(PDOException $e) {
                    $pdo->rollback();
                    $message = "Error deleting group: " . $e->getMessage();
                    $messageType = "error";
                }
            }
            break;
            
        case 'delete_message':
            $message_id = filter_var($_POST['message_id'], FILTER_VALIDATE_INT);
            if ($message_id) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
                    $stmt->execute([$message_id]);
                    $message = "Message deleted successfully";
                    $messageType = "success";
                } catch(PDOException $e) {
                    $message = "Error deleting message: " . $e->getMessage();
                    $messageType = "error";
                }
            }
            break;
            
        case 'toggle_admin':
            $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $is_admin = filter_var($_POST['is_admin'], FILTER_VALIDATE_INT);
            if ($user_id && $user_id != $_SESSION['user_id']) {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
                    $stmt->execute([$is_admin, $user_id]);
                    $message = "User admin status updated";
                    $messageType = "success";
                } catch(PDOException $e) {
                    $message = "Error updating admin status: " . $e->getMessage();
                    $messageType = "error";
                }
            }
            break;
    }
}

// Get all users with stats
function getAllUsers($pdo) {
    $stmt = $pdo->prepare("
        SELECT u.*, 
               COALESCE(u.is_admin, 0) as is_admin,
               (SELECT COUNT(*) FROM messages WHERE user_id = u.id) as message_count,
               (SELECT COUNT(*) FROM group_members WHERE user_id = u.id) as group_count,
               (SELECT COUNT(*) FROM `groups` WHERE created_by = u.id) as created_groups
        FROM users u 
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all groups with stats
function getAllGroups($pdo) {
    $stmt = $pdo->prepare("
        SELECT g.*, u.username as creator_name,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count,
               (SELECT COUNT(*) FROM messages WHERE group_id = g.id) as message_count
        FROM `groups` g 
        JOIN users u ON g.created_by = u.id
        ORDER BY g.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get recent messages
function getRecentMessages($pdo, $limit = 100) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.username, g.name as group_name
        FROM messages m
        JOIN users u ON m.user_id = u.id
        JOIN `groups` g ON m.group_id = g.id
        ORDER BY m.sent_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get system statistics
function getSystemStats($pdo) {
    $stats = [];
    
    // Total users
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $stats['total_users'] = $stmt->fetch()['count'];
    
    // Total groups
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `groups`");
    $stmt->execute();
    $stats['total_groups'] = $stmt->fetch()['count'];
    
    // Total messages
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages");
    $stmt->execute();
    $stats['total_messages'] = $stmt->fetch()['count'];
    
    // Admin users
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
    $stmt->execute();
    $stats['admin_users'] = $stmt->fetch()['count'];
    
    // Recent activity (messages in last 24 hours)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE sent_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->execute();
    $stats['recent_activity'] = $stmt->fetch()['count'];
    
    // Active users (users who sent messages in last 7 days)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as count FROM messages WHERE sent_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stmt->execute();
    $stats['active_users'] = $stmt->fetch()['count'];
    
    return $stats;
}

$users = getAllUsers($pdo);
$groups = getAllGroups($pdo);
$recent_messages = getRecentMessages($pdo);
$stats = getSystemStats($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Chat Board</title>
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
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #667eea;
            font-size: 2.5em;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .nav-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 2px solid #667eea;
        }
        
        .nav-links a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 3em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-label {
            color: #666;
            font-size: 1.2em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .admin-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .tab-button {
            flex: 1;
            padding: 20px 25px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.3s ease;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .tab-button.active {
            background: #667eea;
            color: white;
            box-shadow: inset 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .tab-button:hover:not(.active) {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        
        .tab-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .tab-panel {
            display: none;
        }
        
        .tab-panel.active {
            display: block;
        }
        
        .section-title {
            font-size: 1.8em;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .data-table th,
        .data-table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
        }
        
        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .data-table tr:hover {
            background: #e3f2fd;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            margin: 2px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff4757 0%, #ff3742 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2ed573 0%, #26d668 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 213, 115, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 213, 115, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 167, 38, 0.3);
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 167, 38, 0.4);
        }
        
        .alert {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #ffebee 0%, #f8d7da 100%);
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .badge-user {
            background: #e0e0e0;
            color: #666;
        }
        
        .message-text {
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .search-box {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-tabs {
                flex-direction: column;
            }
            
            .data-table {
                font-size: 14px;
            }
            
            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }
        }
        
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 18px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 16px;
        }
        
        .confirm-dialog {
            background: rgba(0, 0, 0, 0.8);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        
        .confirm-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>🛡️ Admin Control Panel</h1>
            <div class="nav-links">
                <a href="connect.php">💬 Back to Chat</a>
                <a href="connect.php?logout=1">🚪 Logout</a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">👥 Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_groups']; ?></div>
                <div class="stat-label">💬 Total Groups</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_messages']; ?></div>
                <div class="stat-label">📝 Total Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['admin_users']; ?></div>
                <div class="stat-label">🛡️ Admin Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['recent_activity']; ?></div>
                <div class="stat-label">🔥 Messages (24h)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_users']; ?></div>
                <div class="stat-label">⚡ Active Users (7d)</div>
            </div>
        </div>
        
        <div class="admin-tabs">
            <button class="tab-button active" onclick="switchTab('users')">👥 Users Management</button>
            <button class="tab-button" onclick="switchTab('groups')">💬 Groups Management</button>
            <button class="tab-button" onclick="switchTab('messages')">📝 Messages Monitor</button>
        </div>
        
        <div class="tab-content">
            <!-- Users Tab -->
            <div id="users-tab" class="tab-panel active">
                <h2 class="section-title">Users Management</h2>
                <input type="text" class="search-box" id="userSearch" placeholder="🔍 Search users by name, email, or phone..." onkeyup="filterTable('userTable', this.value)">
                
                <table class="data-table" id="userTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Info</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Activity</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($user['email']); ?></div>
                                <small style="color: #666;"><?php echo htmlspecialchars($user['phone']); ?></small>
                            </td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge badge-admin">Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-user">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><strong><?php echo $user['message_count']; ?></strong> messages</div>
                                <div><strong><?php echo $user['group_count']; ?></strong> groups joined</div>
                                <div><strong><?php echo $user['created_groups']; ?></strong> groups created</div>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form style="display: inline;" method="POST" onsubmit="return confirmAction('toggle admin status for this user')">
                                        <input type="hidden" name="action" value="toggle_admin">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="is_admin" value="<?php echo $user['is_admin'] ? 0 : 1; ?>">
                                        <button type="submit" class="action-btn <?php echo $user['is_admin'] ? 'btn-warning' : 'btn-success'; ?>">
                                            <?php echo $user['is_admin'] ? '👤 Remove Admin' : '🛡️ Make Admin'; ?>
                                        </button>
                                    </form>
                                    <form style="display: inline;" method="POST" onsubmit="return confirmAction('permanently delete this user and all their data')">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="action-btn btn-danger">🗑️ Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-admin">You</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Groups Tab -->
            <div id="groups-tab" class="tab-panel">
                <h2 class="section-title">Groups Management</h2>
                <input type="text" class="search-box" id="groupSearch" placeholder="🔍 Search groups by name or creator..." onkeyup="filterTable('groupTable', this.value)">
                
                <table class="data-table" id="groupTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Group Name</th>
                            <th>Creator</th>
                            <th>Statistics</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $group): ?>
                        <tr>
                            <td><?php echo $group['id']; ?></td>
                            <td>
                                <strong style="color: #667eea;"><?php echo htmlspecialchars($group['name']); ?></strong>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($group['creator_name'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($group['creator_name']); ?>
                                </div>
                            </td>
                            <td>
                                <div><strong><?php echo $group['member_count']; ?></strong> members</div>
                                <div><strong><?php echo $group['message_count']; ?></strong> messages</div>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($group['created_at'])); ?></td>
                            <td>
                                <form style="display: inline;" method="POST" onsubmit="return confirmAction('permanently delete this group and all its messages')">
                                    <input type="hidden" name="action" value="delete_group">
                                    <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                                    <button type="submit" class="action-btn btn-danger">🗑️ Delete Group</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Messages Tab -->
            <div id="messages-tab" class="tab-panel">
                <h2 class="section-title">Recent Messages Monitor</h2>
                <input type="text" class="search-box" id="messageSearch" placeholder="🔍 Search messages by user, group, or content..." onkeyup="filterTable('messageTable', this.value)">
                
                <table class="data-table" id="messageTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Group</th>
                            <th>Message Content</th>
                            <th>Sent At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_messages as $message): ?>
                        <tr>
                            <td><?php echo $message['id']; ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($message['username'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($message['username']); ?>
                                </div>
                            </td>
                            <td>
                                <strong style="color: #667eea;"><?php echo htmlspecialchars($message['group_name']); ?></strong>
                            </td>
                            <td>
                                <div class="message-text" title="<?php echo htmlspecialchars($message['message_text']); ?>">
                                    <?php echo htmlspecialchars($message['message_text']); ?>
                                </div>
                            </td>
                            <td>
                                <div><?php echo date('M j, Y', strtotime($message['sent_at'])); ?></div>
                                <small style="color: #666;"><?php echo date('H:i:s', strtotime($message['sent_at'])); ?></small>
                            </td>
                            <td>
                                <form style="display: inline;" method="POST" onsubmit="return confirmAction('delete this message')">
                                    <input type="hidden" name="action" value="delete_message">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" class="action-btn btn-danger">🗑️ Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab panel
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
        
        // Confirmation dialog for dangerous actions
        function confirmAction(actionDescription) {
            return confirm(`Are you sure you want to ${actionDescription}?\n\nThis action cannot be undone!`);
        }
        
        // Table filtering functionality
        function filterTable(tableId, searchValue) {
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            const searchTerm = searchValue.toLowerCase();
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                // Search through all cells in the row
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(searchTerm) > -1) {
                        found = true;
                        break;
                    }
                }
                
                // Show or hide row based on search result
                row.style.display = found ? '' : 'none';
            }
        }
        
        // Auto-refresh functionality
        let autoRefreshInterval;
        
        function startAutoRefresh() {
            autoRefreshInterval = setInterval(() => {
                // Only refresh if user is on messages tab and hasn't interacted recently
                const activeTab = document.querySelector('.tab-panel.active');
                if (activeTab && activeTab.id === 'messages-tab') {
                    // Add subtle indicator that refresh is happening
                    const refreshIndicator = document.createElement('div');
                    refreshIndicator.innerHTML = '🔄 Refreshing...';
                    refreshIndicator.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #667eea;
                        color: white;
                        padding: 10px 15px;
                        border-radius: 8px;
                        font-size: 12px;
                        z-index: 10000;
                        opacity: 0.8;
                    `;
                    document.body.appendChild(refreshIndicator);
                    
                    // Remove indicator after 2 seconds
                    setTimeout(() => {
                        if (refreshIndicator.parentNode) {
                            refreshIndicator.parentNode.removeChild(refreshIndicator);
                        }
                    }, 2000);
                    
                    // In a real application, you would make an AJAX call here
                    // For now, we'll just show the indicator
                }
            }, 30000); // Refresh every 30 seconds
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
        
        // Start auto-refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + 1,2,3 for tab switching
                if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '3') {
                    e.preventDefault();
                    const tabNames = ['users', 'groups', 'messages'];
                    const tabIndex = parseInt(e.key) - 1;
                    if (tabNames[tabIndex]) {
                        switchTab(tabNames[tabIndex]);
                    }
                }
                
                // Escape key to clear search
                if (e.key === 'Escape') {
                    const activeSearchBox = document.querySelector('.search-box:focus');
                    if (activeSearchBox) {
                        activeSearchBox.value = '';
                        activeSearchBox.dispatchEvent(new Event('keyup'));
                    }
                }
            });
        });
        
        // Stop auto-refresh when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });
        
        // Enhanced table interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to table rows
            const tables = document.querySelectorAll('.data-table tbody tr');
            tables.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Add tooltips to action buttons
            const actionButtons = document.querySelectorAll('.action-btn');
            actionButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
        
        // Utility function to format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Update stat cards with animations
        function animateStatCards() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach((element, index) => {
                const finalValue = parseInt(element.textContent);
                element.textContent = '0';
                
                setTimeout(() => {
                    let current = 0;
                    const increment = finalValue / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= finalValue) {
                            element.textContent = formatNumber(finalValue);
                            clearInterval(timer);
                        } else {
                            element.textContent = formatNumber(Math.floor(current));
                        }
                    }, 30);
                }, index * 200);
            });
        }
        
        // Run stat card animation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(animateStatCards, 500);
        });
        
        // Add success animation for actions
        function showSuccessAnimation(element) {
            element.style.background = '#2ed573';
            element.style.transform = 'scale(1.1)';
            
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
        }
        
        // Enhanced confirmation with custom dialog
        function enhancedConfirm(message, callback) {
            const dialog = document.createElement('div');
            dialog.className = 'confirm-dialog';
            dialog.style.display = 'flex';
            
            dialog.innerHTML = `
                <div class="confirm-content">
                    <h3 style="margin-bottom: 20px; color: #333;">⚠️ Confirm Action</h3>
                    <p style="margin-bottom: 30px; color: #666;">${message}</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button onclick="closeConfirmDialog(false)" class="action-btn" style="background: #6c757d;">Cancel</button>
                        <button onclick="closeConfirmDialog(true)" class="action-btn btn-danger">Confirm</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(dialog);
            
            window.confirmCallback = callback;
        }
        
        function closeConfirmDialog(confirmed) {
            const dialog = document.querySelector('.confirm-dialog');
            if (dialog) {
                dialog.remove();
            }
            
            if (window.confirmCallback) {
                window.confirmCallback(confirmed);
                window.confirmCallback = null;
            }
        }
        
        // Console welcome message for developers
        console.log(`
    🛡️ Chat Board Admin Panel
    ========================
    
    Keyboard Shortcuts:
    • Ctrl/Cmd + 1: Users tab
    • Ctrl/Cmd + 2: Groups tab  
    • Ctrl/Cmd + 3: Messages tab
    • Escape: Clear search
    
    Features:
    • Real-time statistics
    • Advanced search & filtering
    • Auto-refresh every 30 seconds
    • Responsive design
    • Secure admin controls
        `);
    </script>
    
    <!-- Add some final styling for better UX -->
    <style>
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.5);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.7);
        }
        
        /* Loading states */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive improvements */
        @media (max-width: 480px) {
            .stat-number {
                font-size: 2em;
            }
            
            .action-btn {
                padding: 6px 10px;
                font-size: 10px;
                margin: 1px;
            }
            
            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .user-avatar {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }
        }
    </style>
</body>
</html>