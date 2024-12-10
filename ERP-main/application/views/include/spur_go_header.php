<?php defined('BASEPATH') OR exit('No direct script access allowed');
$companyInfo = get_companyInfo();
$productID = $companyInfo['productID'];
?>
<?php //header('Content-type: text/html; charset=utf-8');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!--<link rel="stylesheet" href="<?php /*echo base_url('plugins/bootstrap/css/bootstrap.min.css'); */?>">-->
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/skins/_all-skins.min.css'); ?>"/>
    <link href="<?php echo base_url('plugins_spurgo/bootstrap.min.css')?>" rel="stylesheet" id="bootstrap-css">


    <link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/all.css'); ?>"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('plugins_spurgo/jquery.min.js')?>"></script>
    <script src="<?php echo base_url('plugins_spurgo/bootstrap.min.js')?>"></script>
    <!--<script src="<?php /*echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); */?>"></script>-->
    <!--<script src="<?php /*echo base_url('plugins/bootstrap/js/bootstrap.min.js'); */?>"></script>-->
    <script type="text/javascript"
            src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
   <!-- <link rel="stylesheet" href="<?php /*echo base_url('plugins_spurgo/fonts/material-icon/css/material-design-iconic-font.min.css'); */?>">
    <link rel="stylesheet" href="<?php /*echo base_url('plugins_spurgo/css/style.css'); */?>">-->
</head>
<body>