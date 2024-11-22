<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?php echo base_url() . '/favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url() . '/favicon.ico'; ?>" type="image/x-icon"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/skins/_all-skins.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/all.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/themify-icons/themify-icons.css'); ?>"/>
    <link rel="stylesheet"
          href="<?php echo base_url('plugins/datetimepicker/build/css/bootstrap-datetimepicker.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/css/jquery.Jcrop.min.css'); ?>"/>

    <!--<link rel="stylesheet" href="<?php /*echo base_url('plugins/Dragtable/dragtable.css'); */ ?>" />-->

    <!--Bootstrap Country flag-->
    <link rel="stylesheet" href="<?php echo base_url('plugins/country_flag/flags.css'); ?>"/>


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
</head>
<style type="text/css">
    .dataTable_selectedTr {
        background-color: #B0BED9 !important;
    }

    .progressbr {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }

    /*Access Denied modal*/
    .fade-scale {
        transform: scale(0);
        opacity: 0;
        -webkit-transition: all .25s linear;
        -o-transition: all .25s linear;
        transition: all .25s linear;
    }

    .fade-scale.in {
        opacity: 1;
        transform: scale(1);
    }
</style>
<!--<div class="col-md-1 pull-right">
<a href="#" onclick="compose_email()" class="btn btn-primary btn-block margin-bottom compose">Back</a>
    </div>-->


<div class="mailbox-read-message">
    <div class="mailbox-read-info">
        <h3 id="subject_sent"> <?php echo $details['emailSubject']; ?> </h3>
        <?php if(!empty($details['ccEmail'])){?>
            <h5>CC: <span id="from_sent"> <?php echo $details['ccEmail']; ?></span><span class="mailbox-read-time pull-right"
                                                                                                id="date"></span>
            </h5>
        <?php }?>
        <h5>From: <span id="from_sent"> <?php echo $details['fromEmailAddress']; ?></span><span class="mailbox-read-time pull-right"
                                                                                            id="date"></span>
        </h5>
        <h5>To: <span id="from_sent"> <?php echo $details['toEmailAddress']; ?></span><span class="mailbox-read-time pull-right"
                                                                                            id="date"></span>
        </h5>

    </div>
    <?php echo $details['emailBody']; ?>
</div><!-- /.mailbox-read-message -->
<hr>
<?php if ($attachments) { ?>
<ul class="mailbox-attachments clearfix">
    <?php
    foreach ($attachments as $val) {
        $file = base_url() . 'attachments/email_received/' . $val['new_filename'];
        $link = generate_encrypt_link_only($file);
        ?>
        <li>
                <span class="mailbox-attachment-icon">
                    <i class="fa fa-paperclip"></i>
                </span>
            <div class="mailbox-attachment-info">
                <a href="<?php echo $link ?>" target="_blank" class="mailbox-attachment-name"><i
                            class="fa fa-paperclip"></i> <?php echo $val['new_filename'] ?></a>
                <span class="mailbox-attachment-size">
                          <?php echo $val['file_size'] ?>
                        </span>
            </div>
        </li>
    <?php } ?>
</ul>
<?php } ?>