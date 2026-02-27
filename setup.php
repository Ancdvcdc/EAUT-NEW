<?php
include('includes/config.php');
$sqlStatements = [
    "CREATE TABLE IF NOT EXISTS `tbluser` (
      `id` int NOT NULL AUTO_INCREMENT,
      `username` varchar(100) NOT NULL UNIQUE,
      `email` varchar(150) NOT NULL UNIQUE,
      `password` varchar(255) NOT NULL,
      `FullName` varchar(200),
      `CreatedDate` timestamp DEFAULT CURRENT_TIMESTAMP,
      `UpdatedDate` timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
      `Status` int DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    "ALTER TABLE `tblcomments` ADD COLUMN IF NOT EXISTS `userId` int AFTER `postId`",
    "ALTER TABLE `tblcomments` ADD COLUMN IF NOT EXISTS `parentCommentId` int DEFAULT NULL AFTER `userId`"
];

$setup_success = true;
$setup_messages = [];

foreach ($sqlStatements as $sql) {
    if (!mysqli_query($con, $sql)) {
        $error = mysqli_error($con);
        if (stripos($error, 'already exists') === false && 
            stripos($error, 'Duplicate column') === false &&
            stripos($error, 'Duplicate key') === false) {
            $setup_success = false;
            $setup_messages[] = "Lỗi: $error";
        }
    }
}

if ($setup_success) {
    $fkCheck = mysqli_query($con, "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='tblcomments' AND COLUMN_NAME='userId' AND REFERENCED_TABLE_NAME='tbluser'");
    
    if (mysqli_num_rows($fkCheck) == 0) {
        mysqli_query($con, "ALTER TABLE `tblcomments` ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`userId`) REFERENCES `tbluser`(`id`) ON DELETE CASCADE");
    }
    
    $_SESSION['setup_complete'] = true;
    header('Location: index.php');
    exit;
} else {
    echo "Setup Error: " . implode("<br>", $setup_messages);
}
?>
