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
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Set proper content type
header('Content-Type: application/json');

// Get parameters from request
$groupId = filter_var($_GET['group_id'] ?? null, FILTER_VALIDATE_INT);
$limit = filter_var($_GET['limit'] ?? 50, FILTER_VALIDATE_INT);
$offset = filter_var($_GET['offset'] ?? 0, FILTER_VALIDATE_INT);
$since = $_GET['since'] ?? null; // For getting messages since a specific timestamp
$search = trim($_GET['search'] ?? ''); // For searching messages

// Validate parameters
if (!$groupId) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid group ID']);
    exit();
}

// Limit the maximum messages that can be requested
if ($limit > 100) $limit = 100;
if ($limit < 1) $limit = 50;

if ($offset < 0) $offset = 0;

try {
    // Check if user is member of the group
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$groupId, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'You are not a member of this group']);
        exit();
    }
    
    // Build the query based on parameters
    $whereConditions = ["m.group_id = ?"];
    $queryParams = [$groupId];
    
    // Add search condition if provided
    if (!empty($search)) {
        $whereConditions[] = "m.message_text LIKE ?";
        $queryParams[] = "%$search%";
    }
    
    // Add since condition if provided
    if ($since) {
        $whereConditions[] = "m.sent_at > ?";
        $queryParams[] = $since;
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    // FIXED: Get messages for the group with proper LIMIT handling
    $query = "
        SELECT 
            m.id,
            m.user_id,
            m.message_text,
            m.sent_at,
            u.username,
            COALESCE(u.is_admin, 0) as is_admin
        FROM messages m
        JOIN users u ON m.user_id = u.id
        WHERE $whereClause
        ORDER BY m.sent_at DESC
        LIMIT " . (int)$limit . " OFFSET " . (int)$offset
    );
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($queryParams);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Reverse the order to show oldest first (since we used DESC for pagination)
    if (!$since) {
        $messages = array_reverse($messages);
    }
    
    // Get total count of messages in the group (for pagination info)
    $countQuery = "
        SELECT COUNT(*) as total
        FROM messages m
        WHERE m.group_id = ?
    ";
    $countParams = [$groupId];
    
    if (!empty($search)) {
        $countQuery .= " AND m.message_text LIKE ?";
        $countParams[] = "%$search%";
    }
    
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($countParams);
    $totalMessages = $stmt->fetch()['total'];
    
    // Get group information
    $stmt = $pdo->prepare("
        SELECT g.name, u.username as creator_name,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count
        FROM `groups` g
        JOIN users u ON g.created_by = u.id
        WHERE g.id = ?
    ");
    $stmt->execute([$groupId]);
    $groupInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Format timestamps for better readability
    foreach ($messages as &$message) {
        $message['sent_at_formatted'] = date('M j, Y H:i', strtotime($message['sent_at']));
        $message['is_own'] = ($message['user_id'] == $_SESSION['user_id']);
        $message['user_avatar'] = strtoupper(substr($message['username'], 0, 1));
        
        // Add relative time
        $timestamp = strtotime($message['sent_at']);
        $now = time();
        $diff = $now - $timestamp;
        
        if ($diff < 60) {
            $message['time_ago'] = 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            $message['time_ago'] = $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            $message['time_ago'] = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = floor($diff / 86400);
            $message['time_ago'] = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'messages' => $messages,
        'pagination' => [
            'total' => (int)$totalMessages,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $totalMessages
        ],
        'group_info' => $groupInfo,
        'request_info' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'Unknown'
        ]
    ];
    
    // Add search info if search was performed
    if (!empty($search)) {
        $response['search'] = [
            'query' => $search,
            'results_count' => count($messages)
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'success' => false
    ]);
    exit();
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage(),
        'success' => false
    ]);
    exit();
}
?>