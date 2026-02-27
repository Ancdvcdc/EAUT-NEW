<?php
include('includes/user_session.php');

if (isUserLoggedIn()) {
    logoutUser();
} else {
    header('Location: index.php');
}
?>
