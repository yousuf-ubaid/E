<?php
$session = $this->session->userdata('status');
if ($session == 1) {
    header('Location:' . site_url() . '/dashboard');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Forgot Password | ERP</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Forgot Password | ERP</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/mat-dash.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">

        body {
            background: url(" <?php echo base_url('images/login-bg.jpg');?>") no-repeat center center fixed;
            background-color: #d6d9d0;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .help-block {
            display: block;
            margin-top: 5px;
            margin-bottom: 10px;
            color: #737373;
        }

        .has-error .checkbox, .has-error .checkbox-inline, .has-error .control-label, .has-error .form-control-feedback, .has-error .help-block, .has-error .radio, .has-error .radio-inline, .has-error.checkbox label, .has-error.checkbox-inline label, .has-error.radio label, .has-error.radio-inline label {
            color: #a94442;
        }
    </style>
</head>
<body class="hold-transition login-page">

<div id="main-wrapper">
    <div class="position-relative overflow-hidden auth-bg min-vh-100 w-100 d-flex align-items-center justify-content-center" style="background-image: url('<?php echo base_url('images/login-bg.jpg') ?>');">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100 my-5 my-xl-0">
                <div class="col-md-9 d-flex flex-column justify-content-center">
                    <div class="card mb-0 bg-body auth-login m-auto w-100">
                        <div class="row gx-0">
                            <!-- ------------------------------------------------- -->
                            <!-- Part 1 -->
                            <!-- ------------------------------------------------- -->
                            <div class="col-xl-6 border-end">
                                <div class="row justify-content-center py-4">
                                    <div class="col-lg-11">
                                        <div class="card-body">
                                            <a href="<?php echo site_url('dashboard') ?>" class="text-nowrap logo-img d-block mb-4 w-100">
                                                <img src="<?php echo base_url('images/quantum.png') ?>" class="dark-logo" alt="Logo-Dark" height="50px">
                                            </a>
                                            <p class="text-muted">Please enter the email address associated with your account and We will
                                                email you a link to reset your password.</p>

                                            <?php if (!empty($extra) && ($type == 'e')) { ?>
                                                <div role="alert" class="alert alert-danger"><?php echo $extra; ?></div>
                                            <?php } elseif(!empty($extra) && ($type == 's')) {
                                                ?>
                                                <div role="alert" class="alert alert-success"><?php echo $extra; ?></div>
                                                <?php
                                            } ?>

                                            <?php echo form_open('login/forgetPasswordSubmit', ' id="login_form" role="form" class="login100-form"'); ?>
                                            <div class="mb-3">
                                                <div class="form-group has-feedback">
                                                    <label for="from_username" class="form-label">Email Address</label>
                                                    <input type="email" id="from_username" name="email" class="form-control" placeholder="Enter your email" aria-describedby="emailHelp" readonly onfocus="this.removeAttribute('readonly');">
                                                    <span class="form-control-feedback"></span>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-dark w-100 py-8 mb-4 rounded-1">Forgot Password</button>
                                            <a href="<?php echo site_url('login') ?>" class="btn bg-primary-subtle text-primary w-100 py-8 mb-4 rounded-1">Back to Login</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ------------------------------------------------- -->
                            <!-- Part 2 -->
                            <!-- ------------------------------------------------- -->
                            <div class="col-xl-6 d-none d-xl-block">
                                <div class="row justify-content-center align-items-start h-100">
                                    <div class="col-lg-9">
                                        <div id="auth-login" class="mt-4 pt-4">
                                            <div class="d-flex align-items-center justify-content-center w-100 h-100 flex-column gap-9 text-center">
                                                <img src="<?php echo base_url('images/login-side.png') ?>" alt="login-side-img" width="300" class="img-fluid">
                                                <h4 class="mb-0">All in one business management application </h4>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#login_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                email: {validators: {notEmpty: {message: 'Email is required.'}}},
            },
        }).on('success.form.bv', function (e) {
        });
    });
</script>
</body>
</html>
