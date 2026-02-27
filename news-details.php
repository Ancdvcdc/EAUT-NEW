<?php 
   session_start();
   include('includes/config.php');
   include('includes/user_session.php');
   if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
   }
   $postid=intval($_GET['nid']);
   
       $sql = "SELECT viewCounter FROM tblposts WHERE id = '$postid'";
       $result = $con->query($sql);
   
       if ($result->num_rows > 0) {
           while($row = $result->fetch_assoc()) {
               $visits = $row["viewCounter"];
               $sql = "UPDATE tblposts SET viewCounter = $visits+1 WHERE id ='$postid'";
       $con->query($sql);
   
           }
       } else {
           echo "no results";
       }
       
   
   
   ?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo htmlentities(SITE_NAME); ?> | News Details</title>
    <link rel="shortcut icon" href="<?php echo htmlentities(SITE_FAVICON_PATH); ?>" type="image/x-icon">
    
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="css/modern-business.css" rel="stylesheet">
    <link rel="stylesheet" href="css/icons.css">
</head>

<body>
    
    
    <?php include('includes/header.php');?>
    
    <div class="container-fluid">
        <div class="row" style="margin-top: 4%">
            
            <div class="col-md-9 mt-5">
                
                <?php
                  $pid=intval($_GET['nid']);
                  $currenturl="http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];;
                   $query=mysqli_query($con,"select tblposts.PostTitle as posttitle,tblposts.PostImage,tblcategory.CategoryName as category,tblcategory.id as cid,tblsubcategory.Subcategory as subcategory,tblposts.PostDetails as postdetails,tblposts.PostingDate as postingdate,tblposts.PostUrl as url,tblposts.postedBy,tblposts.lastUpdatedBy,tblposts.UpdationDate from tblposts left join tblcategory on tblcategory.id=tblposts.CategoryId left join  tblsubcategory on  tblsubcategory.SubCategoryId=tblposts.SubCategoryId where tblposts.id='$pid'");
                  while ($row=mysqli_fetch_array($query)) {
                  ?>
                <div class="card border-0">
                    <div class="card-body">
                        <a class="badge bg-success text-decoration-none link-light" href="category.php?catid=<?php echo htmlentities($row['cid'])?>" style="color:#fff"><?php echo htmlentities($row['category']);?></a>
                        
                        <a class="badge bg-warning text-decoration-none link-light" style="color:#fff"><?php echo htmlentities($row['subcategory']);?></a>
                        <h1 class="card-title"><?php echo htmlentities($row['posttitle']);?></h1>
                        

                        <p>
                            by <?php echo htmlentities($row['postedBy']);?> on | <?php echo htmlentities($row['postingdate']);?>
                            <?php if($row['lastUpdatedBy']!=''):?>
                            Last Updated by <?php echo htmlentities($row['lastUpdatedBy']);?> on<?php echo htmlentities($row['UpdationDate']);?>
                        </p>
                        <?php endif;?>
                        <p><strong>Share:</strong> <a href="http://www.facebook.com/share.php?u=<?php echo $currenturl;?>" target="_blank">Facebook</a> |
                            <a href="https://twitter.com/share?url=<?php echo $currenturl;?>" target="_blank">Twitter</a> |
                            <a href="https://web.whatsapp.com/send?text=<?php echo $currenturl;?>" target="_blank">Whatsapp</a> |
                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $currenturl;?>" target="_blank">Linkedin</a> <b>Visits:</b> <?php print $visits; ?>
                        </p>
                        <hr>
                        <?php
                        $postImage = trim($row['PostImage'] ?? '');
                        $imagePath = 'admin/postimages/' . $postImage;
                        if ($postImage === '' || !file_exists(__DIR__ . '/' . $imagePath)) {
                            $imagePath = 'images/a-2048x647.jpg';
                        }
                        ?>
                        <img class="img-fluid w-100" src="<?php echo htmlentities($imagePath); ?>" alt="<?php echo htmlentities($row['posttitle']);?>">
                        <p class="card-text"><?php 
                        $pt=$row['postdetails'];
                                      echo  (substr($pt,0));?></p>
                    </div>

                </div>
                <?php } ?>
            </div>
            
            <?php include('includes/sidebar.php');?>
        </div>
        
        
        <div class="col-md-8">
            <h5 class="mt-5 mb-4">Bình Luận (<span id="commentCount"><?php 
                  $pid=intval($_GET['nid']);
                  $countQuery=mysqli_query($con,"select count(*) as total from tblcomments where postId='$pid' and status='1' and parentCommentId IS NULL");
                  $countRow=mysqli_fetch_array($countQuery);
                  echo $countRow['total'];
            ?></span>)</h5>
            <div id="commentsContainer">
                <?php 
                      $sts=1;
                      $query=mysqli_query($con,"select tc.id, tc.comment, tc.postingDate, tu.username from tblcomments tc left join tbluser tu on tc.userId=tu.id where tc.postId='$pid' and tc.status='$sts' and tc.parentCommentId IS NULL order by tc.postingDate DESC");
                      
                      while ($row=mysqli_fetch_array($query)) {
                          $commentId = $row['id'];
                          $repliesQuery = mysqli_query($con, "select tc.comment, tc.postingDate, tu.username from tblcomments tc left join tbluser tu on tc.userId=tu.id where tc.postId='$pid' and tc.status='1' and tc.parentCommentId='$commentId' order by tc.postingDate ASC");
                      ?>
                <div class="media mb-4" style="padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                    <div class="media-body">
                        <h6 class="mt-0"><?php echo htmlentities($row['username'] ?? 'Anonymous');?></h6>
                        <small class="text-muted">
                            <?php echo htmlentities($row['postingDate']);?>
                        </small>
                        <p class="mt-2"><?php echo htmlentities($row['comment']);?></p>
                        
                        <?php if (isUserLoggedIn()): ?>
                        <a href="#" class="reply-btn" data-comment-id="<?php echo $row['id']; ?>">
                            <small><i class="fa fa-reply"></i> Trả lời</small>
                        </a>
                        <?php endif; ?>
                        
                        
                        <?php while ($replyRow = mysqli_fetch_array($repliesQuery)): ?>
                        <div class="media mt-3 ml-3" style="padding: 10px; background-color: #f9f9f9; border-radius: 3px;">
                            <div class="media-body">
                                <h6 class="mt-0"><?php echo htmlentities($replyRow['username'] ?? 'Anonymous');?></h6>
                                <small class="text-muted"><?php echo htmlentities($replyRow['postingDate']);?></small>
                                <p class="mt-1"><?php echo htmlentities($replyRow['comment']);?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        
                        
                        <div class="reply-form-container" id="reply-form-<?php echo $row['id']; ?>" style="display: none; margin-top: 15px; background-color: #f9f9f9; padding: 15px; border-radius: 5px;">
                            <textarea class="form-control mb-2 reply-text" placeholder="Nhập trả lời..." required></textarea>
                            <button class="btn btn-sm btn-primary reply-submit" data-comment-id="<?php echo $row['id']; ?>">Gửi</button>
                            <button class="btn btn-sm btn-secondary reply-cancel" data-comment-id="<?php echo $row['id']; ?>">Hủy</button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        
        
        <div class="col-md-8">
            <hr>
            <div class="card my-4 bg-transparent border-0">
                <h5 class="card-header bg-transparent border-0">Viết Bình Luận</h5>
                <div class="card-body">
                    <?php if (isUserLoggedIn()): ?>
                    <div class="alert alert-info">
                        Bạn đang đăng nhập với tài khoản: <strong><?php echo getCurrentUser()['username']; ?></strong>
                    </div>
                    <form id="commentForm">
                        <div class="form-group">
                            <textarea class="form-control" id="commentText" rows="4" placeholder="Nhập bình luận của bạn..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Đăng Bình Luận</button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <strong>Vui lòng <a href="user-login.php">đăng nhập</a> hoặc <a href="user-register.php">đăng ký</a> để bình luận.</strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php');?>
    
    <script src="js/foot.js"></script>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#commentForm').submit(function(e) {
            e.preventDefault();
            let commentText = $('#commentText').val().trim();
            let postId = <?php echo $pid; ?>;
            
            if (commentText === '') {
                alert('Vui lòng nhập bình luận');
                return;
            }
            
            $.ajax({
                url: 'submit-comment.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    postId: postId,
                    comment: commentText,
                    parentCommentId: 0
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#commentText').val('');
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert('Lỗi: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Lỗi kết nối: ' + error);
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        });
        $('.reply-btn').click(function(e) {
            e.preventDefault();
            let commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).slideToggle();
        });
        $('.reply-cancel').click(function(e) {
            e.preventDefault();
            let commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).slideUp();
            $('#reply-form-' + commentId + ' .reply-text').val('');
        });
        $('.reply-submit').click(function() {
            let commentId = $(this).data('comment-id');
            let replyText = $('#reply-form-' + commentId + ' .reply-text').val().trim();
            let postId = <?php echo $pid; ?>;
            
            if (replyText === '') {
                alert('Vui lòng nhập trả lời');
                return;
            }
            
            $.ajax({
                url: 'submit-comment.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    postId: postId,
                    comment: replyText,
                    parentCommentId: commentId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#reply-form-' + commentId + ' .reply-text').val('');
                        $('#reply-form-' + commentId).slideUp();
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert('Lỗi: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Lỗi kết nối: ' + error);
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        });
    });
    </script>
</body>

</html>
