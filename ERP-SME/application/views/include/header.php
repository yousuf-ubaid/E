<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$view_name = $this->uri->segment(1);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo 'Quantum SME | ' . $title; ?></title>
    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/skins/_all-skins.min.css'); ?>"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript"
            src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>

    <style>
        .container{
            width: 100% !important;
        }

        section.content{
            height: 810px
        }
    </style>
</head>
<body class="hold-transition skin-blue layout-top-nav">
<header class="main-header">
    <br><br>
    <nav class="navbar navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a href="#" class="navbar-brand"><b>Quantum</b> ERP</a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="<?php if ($view_name == 'Dashboard') {
                        echo 'active';
                    } ?>"><a href="<?php echo site_url('Dashboard') ?>">Dashboard </a></li>
                    <li class="<?php if ($view_name == 'companyAdmin') {
                        echo 'active';
                    } ?>"><a href="<?php echo site_url('companyAdmin') ?>">Company </a></li>

                    <!-- <li class="<?php if ($view_name == 'modules') {
                        echo 'active';
                    } ?>"><a href="<?php echo site_url('modules') ?>">Modules </a></li> -->
                   
                    <!--<li class="<?/*=($view_name == 'navigationMenu')?'active':''*/?>">
                     <a href="<?php /*echo site_url('navigationMenu') */?>">Navigation Menu </a>
                    </li>-->
                    <!--<li class="<?/*=($view_name == 'invoicesMenu')?'active':''*/?>">
                        <a href="<?php /*echo site_url('invoicesMenu') */?>">Invoices Menu </a>
                    </li>-->
                    
                    <li class="<?=($view_name == 'Subscription')? 'active': '' ?>">
                        <a href="<?php echo site_url('Subscription') ?>">Subscription</a>
                    </li>

                    <li class="<?=($view_name == 'payment_det')? 'active': '' ?>">
                        <a href="<?php echo site_url('payment_det') ?>">Payment Details</a>
                    </li>
                
                    <li class="<?=($view_name == 'payment_logs')? 'active': ''; ?>">
                        <a href="<?php echo site_url('payment_logs') ?>">Payment log</a>
                    </li>
                    <li class="<?=($view_name == 'audit_log')? 'active': ''; ?>">
                        <a href="<?php echo site_url('audit_log') ?>">Audit log</a>
                    </li>
                    <li class="<?=($view_name == 'product_master')? 'active': ''; ?>">
                        <a href="<?php echo site_url('product_master') ?>">Product Master</a>
                    </li>
                    <!-- <li class="<?=($view_name == 'nav_setup')? 'active': ''; ?>">
                        <a href="<?php echo site_url('nav_setup') ?>">Navigation Setup</a>
                    </li> -->
                    <li class="<?=($view_name == 'cron_log')? 'active': ''; ?>">
                        <a href="<?php echo site_url('cron_log') ?>">Cron Log</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">                    
                    <li class="dropdown tasks-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-success">2</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 9 tasks</li>
                            <li>
                                <!-- Inner menu: contains the tasks -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <!-- Task title and progress text -->
                                            <h3>
                                                Design some buttons
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <!-- The progress bar -->
                                            <div class="progress xs">
                                                <!-- Change the css width attribute to simulate progress -->
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">View all tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <img src="<?=base_url('plugins/dist/img/user2-160x160.jpg')?>" class="user-image" alt="User Image">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs"><?php echo $this->session->userdata('sme_company_userDisplayName'); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <img src="<?=base_url('plugins/dist/img/user2-160x160.jpg')?>" class="img-circle current-user-img" alt="User Image">
                                
                                <p>
                                    <?php echo $this->session->userdata('sme_company_userDisplayName'); ?>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="<?php echo site_url('Login/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-custom-menu -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>
<div class="content-wrapper" style="min-height: 561px;">
<div class="container">
