<?php
session_start();
include('includes/config.php');
include('includes/user_session.php');

header('Content-Type: application/json');
$debug = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isUserLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để bình luận']);
        exit;
    }

    $user = getCurrentUser();
    if (!$user || !isset($user['id'])) {
        echo json_encode(['success' => false, 'message' => 'Không thể lấy thông tin người dùng']);
        exit;
    }
    $postId = isset($_POST['postId']) ? intval($_POST['postId']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $parentCommentId = isset($_POST['parentCommentId']) ? intval($_POST['parentCommentId']) : 0;
    if (empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập bình luận']);
        exit;
    }
    
    if ($postId == 0) {
        echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ']);
        exit;
    }
    $checkColumns = mysqli_query($con, "SHOW COLUMNS FROM `tblcomments` LIKE 'userId'");
    if (mysqli_num_rows($checkColumns) == 0) {
        mysqli_query($con, "ALTER TABLE `tblcomments` ADD COLUMN `userId` int AFTER `postId`");
    }
    
    $checkParentId = mysqli_query($con, "SHOW COLUMNS FROM `tblcomments` LIKE 'parentCommentId'");
    if (mysqli_num_rows($checkParentId) == 0) {
        mysqli_query($con, "ALTER TABLE `tblcomments` ADD COLUMN `parentCommentId` int DEFAULT NULL AFTER `userId`");
    }
    $comment = mysqli_real_escape_string($con, $comment);
    $userId = intval($user['id']);
    $status = 1;
    $parentId = ($parentCommentId > 0) ? $parentCommentId : 'NULL';
    $insertQuery = "INSERT INTO tblcomments (postId, userId, comment, parentCommentId, status) 
                    VALUES ($postId, $userId, '$comment', $parentId, $status)";
    
    $result = mysqli_query($con, $insertQuery);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Bình luận đã được đăng']);
    } else {
        $error = mysqli_error($con);
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được phép']);
}
?>
