<?php
include('includes/config.php');
include('includes/user_session.php');
if (isUserLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Vui lòng điền đầy đủ tất cả trường';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu không khớp';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } else {
        $checkQuery = mysqli_query($con, "SELECT id FROM tbluser WHERE username='$username' OR email='$email'");
        
        if ($checkQuery === false) {
            $error = 'Lỗi cơ sở dữ liệu: ' . mysqli_error($con) . '. Vui lòng liên hệ quản trị viên.';
        } else if (mysqli_num_rows($checkQuery) > 0) {
            $error = 'Tên đăng nhập hoặc email đã tồn tại';
        } else {
            $hashedPassword = hashPassword($password);
            $insertQuery = mysqli_query($con, "INSERT INTO tbluser (username, email, password, FullName) VALUES ('$username', '$email', '$hashedPassword', '$fullname')");
            
            if ($insertQuery) {
                $success = 'Đăng ký thành công! <a href="user-login.php">Đăng nhập ngay</a>';
            } else {
                $error = 'Lỗi đăng ký: ' . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities(SITE_NAME); ?> | Đăng Ký</title>
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
                    <img src="admin/assets/images/blog-reading.png" width="auto" alt="Register">
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
                                <p>Tạo tài khoản người dùng mới để tham gia bình luận.</p>
                            </div>
                            <div class="account-content">
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
                                <?php endif; ?>

                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success"><?php echo $success; ?></div>
                                <?php endif; ?>

                                <form class="form-horizontal" method="post" autocomplete="off">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="text" name="fullname" required placeholder="Họ và tên" value="<?php echo htmlentities($_POST['fullname'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="text" name="username" required placeholder="Tên đăng nhập" value="<?php echo htmlentities($_POST['username'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="email" name="email" required placeholder="Email" value="<?php echo htmlentities($_POST['email'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="password" name="password" required placeholder="Mật khẩu">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="password" name="confirm_password" required placeholder="Xác nhận mật khẩu">
                                        </div>
                                    </div>

                                    <div class="form-group account-btn text-center m-t-10">
                                        <div class="col-xs-12">
                                            <button class="btn btn-custom waves-effect waves-light btn-md w-100" type="submit">Đăng Ký</button>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center m-t-10">
                                    <p>Đã có tài khoản? <a href="user-login.php">Đăng nhập tại đây</a></p>
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
