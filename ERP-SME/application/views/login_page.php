<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo 'Quantum SME | '.$title; ?></title>
    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
    <style type="text/css">
		body { 
		    background: url("<?php echo base_url('images/login-bg.jpg');?>") no-repeat center center fixed;
		    -webkit-background-size: cover;
		    -moz-background-size: cover;
		    -o-background-size: cover;
		    background-size: cover;
		}
	</style>
</head>
<body>
<div class="login-box">
  <div class="login-box-body">
    <?php if (!empty($extra)) { ?>
      <div class="alert alert-<?php echo $extra['type']; ?>"> <strong>Oh snap ! </strong><?php echo $extra['message']; ?></div>
    <?php } ?>
    <div class="text-center m-b-md">
      <h3>Welcome to Quantum&trade; ERP</h3>
      <small>Web Enterprise Resource Planning Solution.</small>
      <p>Please Reconfirm Your User Credential</p>
    </div><br>
    <?php echo form_open('Login/loginSubmit',' id="login_form" role="form"'); ?>
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="Username" placeholder="Please enter you username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="Password"  placeholder="******">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">

        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#login_form').bootstrapValidator({
      live            : 'enabled',
      message         : 'This value is not valid.',
      excluded        : [':disabled'],
      fields          : {
        Username    : {validators : {notEmpty:{message:'Username is required.'}}},
        Password    : {validators : {notEmpty:{message:'Password is required.'}}}
      },
    }).on('success.form.bv', function(e) { });
  });
</script>
</body>
</html>