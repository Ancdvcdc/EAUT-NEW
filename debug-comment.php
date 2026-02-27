<?php
session_start();
include('includes/config.php');
include('includes/user_session.php');

echo "=== DEBUG COMMENT SYSTEM ===\n\n";

echo "1. User Status:\n";
echo "   Logged In: " . (isUserLoggedIn() ? 'YES' : 'NO') . "\n";
if (isUserLoggedIn()) {
    $user = getCurrentUser();
    echo "   User ID: " . $user['id'] . "\n";
    echo "   Username: " . $user['username'] . "\n";
} else {
    echo "   Please login first at user-login.php\n";
}

echo "\n2. Database Connection:\n";
if ($con) {
    echo "   Connected: YES\n";
    echo "   Database: " . DB_NAME . "\n";
} else {
    echo "   Connected: NO\n";
    echo "   Error: " . mysqli_connect_error() . "\n";
}

echo "\n3. Table Check:\n";
$tables = ['tbluser', 'tblcomments', 'tblposts'];
foreach ($tables as $table) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "   $table: EXISTS\n";
    } else {
        echo "   $table: MISSING\n";
    }
}

echo "\n4. tblcomments Columns:\n";
$result = mysqli_query($con, "SHOW COLUMNS FROM tblcomments");
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        echo "   - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "   Error: " . mysqli_error($con) . "\n";
}

echo "\n5. Test POST data:\n";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "   POST received\n";
    echo "   postId: " . ($_POST['postId'] ?? 'MISSING') . "\n";
    echo "   comment: " . substr($_POST['comment'] ?? 'MISSING', 0, 50) . "...\n";
    echo "   parentCommentId: " . ($_POST['parentCommentId'] ?? 'MISSING') . "\n";
} else {
    echo "   No POST data - visit from form to test\n";
}

echo "\n6. Test submit-comment.php:\n";
if (file_exists('submit-comment.php')) {
    echo "   File EXISTS\n";
} else {
    echo "   File MISSING\n";
}

echo "\n=== End Debug ===\n";
?>
