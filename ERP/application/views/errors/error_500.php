<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Session Expired</title>

    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    
    <!--Google font-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300&amp;display=swap" rel="stylesheet">
    <!-- Bootstrap css -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <!-- Theme css -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/dist/css/error-page.css'); ?>">
</head>
<body>
  
    <!-- 02 Main page -->
  
    <section class="page-section">
        <div class="full-width-screen">
            <div class="container-fluid">
                <div class="content-detail">
                    <h4 class="sub-title">500</h4>
                    <h1 class="global-title"><span>O</span><span>o</span><span>ps!</span></h1>
                    

                    <p class="detail-text">We're sorry,<br>The server encountered an unexpected condition<br> that prevented it from fulfilling the request. <br>Please try again later.</p> 

                    <div class="back-btn">
                        <a href="<?php echo site_url('Login') ?>" class="btn">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
</body>
</html>