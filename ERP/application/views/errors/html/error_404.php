<?php
$ci = new CI_Controller();
$ci =& get_instance();
$ci->load->helper('url');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Page Not Found</title>

    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>

    <!--Google font-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/mat-dash.css'); ?>">
</head>
<body>

<div id="main-wrapper">
    <div class="position-relative overflow-hidden min-vh-100 w-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-lg-4">
                    <div class="text-center">
                        <img src="<?php echo base_url('images/errorimg.svg') ?>" alt="matdash-img" class="img-fluid" width="500">
                        <h1 class="fw-semibold mb-7 fs-9">Opps!!!</h1>
                        <h4 class="fw-semibold mb-7">This page you are looking for could not be found.</h4>
                        <a class="btn btn-primary" href="<?php echo site_url('login') ?>" role="button">Go Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>