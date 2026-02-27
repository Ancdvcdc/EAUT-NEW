<?php
include('includes/config.php');
include('includes/user_session.php');
if (isUserLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập tên đăng nhập và mật khẩu';
    } else {
        $hashedPassword = hashPassword($password);
        $query = mysqli_query($con, "SELECT id, username, email, FullName FROM tbluser WHERE username='$username' AND password='$hashedPassword' AND Status=1");
        
        if ($query === false) {
            $error = 'Lỗi cơ sở dữ liệu: ' . mysqli_error($con);
        } else if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['fullname'] = $row['FullName'];
            
            header('Location: index.php?login=success');
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities(SITE_NAME); ?> | Đăng Nhập</title>
    <link href="admin/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="admin/assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="admin/assets/js/modernizr.min.js"></script>
</head>
<body class="bg-transparent">
    <section>
        <div class="container m-t-50">
            <div class="row align-items-center m-t-50">
                <div class="col-md-8 text-center">
                    <img src="admin/assets/images/blog-reading.png" width="auto" alt="Login">
                </div>
                <div class="col-md-4">
                    <div class="wrapper-page">
                        <div class="m-t-40 account-pages">
                            <div class="account-logo-box">
                                <h2 class="text-uppercase">
                                    <a href="index.php" class="text-success">
                                        <span><img src="<?php echo htmlentities(SITE_LOGO_PATH); ?>" alt="<?php echo htmlentities(SITE_NAME); ?>" width="350"></span>
                                    </a>
                                </h2>
                                <p>Đăng nhập tài khoản người dùng để bình luận và theo dõi tin tức.</p>
                            </div>
                            <div class="account-content">
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
                                <?php endif; ?>

                                <form class="form-horizontal" method="post" autocomplete="off">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="text" name="username" required placeholder="Tên đăng nhập" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="password" name="password" required placeholder="Mật khẩu">
                                        </div>
                                    </div>

                                    <div class="form-group account-btn text-center m-t-10">
                                        <div class="col-xs-12">
                                            <button class="btn btn-custom waves-effect waves-light btn-md w-100" type="submit">Đăng Nhập</button>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center m-t-10">
                                    <p>Chưa có tài khoản? <a href="user-register.php">Đăng ký ngay</a></p>
                                    <a href="index.php"><i class="mdi mdi-home"></i> Về trang chủ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    var resizefunc = [];
    </script>
    <script src="admin/assets/js/jquery.min.js"></script>
    <script src="admin/assets/js/detect.js"></script>
    <script src="admin/assets/js/fastclick.js"></script>
    <script src="admin/assets/js/jquery.blockUI.js"></script>
    <script src="admin/assets/js/waves.js"></script>
    <script src="admin/assets/js/jquery.slimscroll.js"></script>
    <script src="admin/assets/js/jquery.scrollTo.min.js"></script>
    <script src="admin/assets/js/jquery.core.js"></script>
    <script src="admin/assets/js/jquery.app.js"></script>
</body>
</html>
