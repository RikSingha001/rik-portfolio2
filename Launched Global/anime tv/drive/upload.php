<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;

// Step 1: Create Google Client
$client = new Client();
$client->setApplicationName("Drive API");
$client->setScopes([Drive::DRIVE]);
$client->setAuthConfig('credentials.json');
$client->setRedirectUri('http://localhost:8000/upload.php');
$client->setAccessType('offline');

// Step 2: Handle OAuth Flow
if (!isset($_GET['code']) && !isset($_SESSION['access_token'])) {
    $authUrl = $client->createAuthUrl();
    echo "<h3>🔐 Please <a href='$authUrl'>Connect to Google Drive</a> to continue.</h3>";
    exit;
} elseif (!isset($_SESSION['access_token'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header("Location: upload.php");
    exit;
} else {
    $client->setAccessToken($_SESSION['access_token']);
}

// Step 3: Create Drive Service
$service = new Drive($client);

// Step 4: Upload Function
function uploadToDrive(Drive $service, string $filePath, string $fileName, string $mimeType): array {
    $fileMetadata = new DriveFile([
        'name' => $fileName
    ]);

    $content = file_get_contents($filePath);

    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mimeType,
        'uploadType' => 'multipart'
    ]);

    // Make file public
    $permission = new Permission([
        'type' => 'anyone',
        'role' => 'reader'
    ]);
    $service->permissions->create($file->getId(), $permission);

    // Return file link
    $fileData = $service->files->get($file->getId(), ['fields' => 'webViewLink, id']);
    return [
        'id' => $fileData->getId(),
        'link' => $fileData->getWebViewLink()
    ];
}

// Step 5: Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $desc = htmlspecialchars($_POST['description']);

    $uploads = [];

    foreach (['movie', 'trailer', 'poster'] as $type) {
        if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
            $uploads[$type] = uploadToDrive(
                $service,
                $_FILES[$type]['tmp_name'],
                $_FILES[$type]['name'],
                $_FILES[$type]['type']
            );
        }
    }

    // Step 6: Show Uploaded Result
    echo "<h2>✅ Uploaded Successfully</h2>";
    echo "<p><strong>🎬 Movie Title:</strong> $title</p>";
    echo "<p><strong>📝 Description:</strong> $desc</p><hr>";

    if (isset($uploads['movie'])) {
        echo "<h3>🎬 Full Movie:</h3>";
        echo "<a href='{$uploads['movie']['link']}' target='_blank'>Watch Movie</a><br>";
        echo "<video width='640' controls>
                <source src='https://drive.google.com/uc?export=download&id={$uploads['movie']['id']}' type='video/mp4'>
              </video><br><br>";
    }

    if (isset($uploads['trailer'])) {
        echo "<h3>🎞️ Trailer:</h3>";
        echo "<a href='{$uploads['trailer']['link']}' target='_blank'>Watch Trailer</a><br>";
        echo "<video width='640' controls>
                <source src='https://drive.google.com/uc?export=download&id={$uploads['trailer']['id']}' type='video/mp4'>
              </video><br><br>";
    }

    if (isset($uploads['poster'])) {
        echo "<h3>🖼️ Poster:</h3>";
        echo "<img src='https://drive.google.com/uc?export=download&id={$uploads['poster']['id']}' width='400'><br>";
        echo "<a href='{$uploads['poster']['link']}' target='_blank'>View Poster</a><br>";
    }
}
?>