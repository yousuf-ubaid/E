<!DOCTYPE html>
<html>
<head>

    <title>Sales Quotation</title>
    <link rel="icon" href="<?php echo base_url().'/favicon.ico'; ?>" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="application/x-javascript"> addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);
        function hideURLbar() {
            window.scrollTo(0, 1);
        } </script>

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

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline-language-english.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/xeditable/css/bootstrap-editable.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">

<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/demo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/offline/offline.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/typeahead.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/handlebars/handlebars-v4.0.5.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiselect/dist/js/multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/daterangepicker/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>
<!--<script type="text/javascript" src="<?php /*echo base_url('plugins/Dragtable/jquery.dragtable.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>
<!--<script type="text/javascript"
        src="<?php /*echo base_url('plugins/multiselect/dist/js/bootstrap-multiselect.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/highcharts.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/modules/exporting.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/jquery.inputmask.bundle.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.date.extensions.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/jquery.inputmask.js'); ?>"></script>
<!-- value as well in textbox-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>

<!--jquery auto complete-->


</head>
<style>
    .hide {
        display: none;
    }

    .alert.alert-danger {
        border-top: 1px solid rgba(140, 0, 0, 0.4);
        border-bottom: 1px solid rgba(140, 0, 0, 0.4);
    }

    .alert.alert-success {
        border-top: 1px solid limegreen;
        border-bottom: 1px solid limegreen;
    }

    .alert {
        padding-left: 30px;
        margin-left: 15px;
        position: relative;
        font-size: 12px;
    }

    .alert {
        background-position: 2% 7px;
        background-repeat: no-repeat;
        background-size: auto 35px;
        background-color: rgba(0, 0, 0, 0);
        border: 0;
        min-width: auto !important;
        text-align: left;
        padding-left: 68px;
    }

    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }

    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-danger, .alert-error {
        color: #b94a48;
        background-color: #f2dede;
        border-color: #eed3d7;
    }

    .alert, .alert h4 {
        color: #c09853;
    }

    .alert {
        padding: 8px 35px 8px 14px;
        margin-bottom: 20px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        background-color: #fcf8e3;
        border: 1px solid #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .bordertype {
        border-left: 3px solid #daa520;
    }
    .bordertypePRO {
        border-left: 3px solid #f7f4f4;
    }
    .tableth {
        background-color: #f7f7f7;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .tablethcol2 {
        background-color: #ececec;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .tablethcoltotal {
        background-color: #fde49d;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .vl {
        border-left: 3px solid #f7f4f4;
        height: 500px;
    }

    .buttonacceptanddecline {
        border-radius: 0;
    }
</style>
<body>
<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div style="padding: 5%;margin: 0 auto;">
    <div class="row">
        <div class="col-sm-8">
            <h4 class="modal-title bordertype">&nbsp;<strong style="font-family: tahoma;font-weight: 900;font-size: 108%;">Estimate</strong></h4><!--Quotation-->
            <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Date of Issuance : <lablel id="dateofissue"><?php echo $master['quotationDate'] ?></lablel></strong></h6><!--Quotation-->
            <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Open Till : <label id="expiarydate"><?php echo $master['quotationExpDate'] ?></label></strong></h6><!--Quotation-->
            <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;"><?php if($master['referenceNo'])
            {
                        echo 'Reference Number : '.$master['referenceNo'];
                    }else {
                        echo 'Reference Number : -';
                    }?>


                 </strong></h6>
            <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">
                    <?php if($master['quotationNarration'])
                    {
                        echo 'Narration : '.$master['quotationNarration'];
                    } else {
                        echo 'Narration : -';
                    }?>



                </strong></h6>
        </div>
        <div class="col-sm-4">
            <h4 class="modal-title bordertypePRO">&nbsp;&nbsp;<strong style="font-weight: 300;font-size: 127%;color: #908f8f;"><lablel id="quotationcode"><?php echo $master['quotationCode'] ?></lablel></strong></h4><!--Quotation-->
            <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Date of Issuance :  <lablel id="dateofissueright"><?php echo $master['quotationDate'] ?></lablel></strong></h6><!--Quotation-->
            <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Open Till : <label id="expiarydateright"><?php echo $master['quotationExpDate'] ?></strong></h6><!--Qu
                        <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;"></strong></h6><!--Quotation-->
        </div>

        <div class="row" style="margin-top: 5px">
            <div class="col-md-8">
                <hr>
                <div id="detail_tbl"></div>
              <?php if($master['termsAndConditions']){?>
                <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 110%;color: #3e3e3e;font-weight: 700;"><label id="expiarydateright"><u>Terms And Condition</u></strong></h6>
              <p><?php echo $master['termsAndConditions']?></p>

                <?php }?>


            </div>
            <div class="col-md-4" style="padding-left: 0.8%;">
                <div class="vl">
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-12">&nbsp;&nbsp;<strong
                                    style="font-weight: 800;font-size: 90%;font-family: sans-serif;"><?php echo $company['company_name']; ?></strong>
                        </div>
                        <div class="form-group col-sm-12">&nbsp;<strong
                                    style="color:#63636369;font-family: tahoma;font-size: 9px;"> <?php echo $company['company_address1'] . ',' . $company['company_address2'] . ',' . $company['company_city'] . ',' .  $company['company_country'] ?></strong>
                        </div>
                        <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                                    style="color:#63636369;font-family: tahoma;font-size: 9px;">Phone
                                : <?php echo $company['company_phone'] ?></strong>


                        </div>
                        <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                                    style="color:#63636369;font-family: tahoma;font-size: 9px;"><?php echo $company['company_email'] ?></strong>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-12">&nbsp;&nbsp;<strong
                                    style="font-weight: 800;font-size: 76%;font-family: sans-serif;color: #cc9a1c;">PROPOSAL
                                TO:</strong><br>&nbsp;&nbsp;<label style="font-family: tahoma;font-weight: 900;"><?php echo $master['quotationPersonName']?></label>
                        </div>
                        <div class="form-group col-sm-12">&nbsp;<strong
                                    style="color:#63636369;font-family: tahoma;font-size: 61%;">&nbsp;<?php echo $master['organizationName']; ?></strong>
                        </div>
                        <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                                    style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['orgAddress']; ?></strong>


                        </div>
                        <div class="form-group col-sm-12" style="margin-top: -14px;">&nbsp;&nbsp;<strong
                                    style="color:#63636369;font-family: tahoma;font-size: 61%;"><?php echo $master['email']; ?></strong>
                        </div>
                    </div>
                    <hr style="width: 88%;">


                    <?php if($master['confirmedYNqut']==1){?>
                        <div class="table-responsive" style="height: 25%;">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width:100%;">

                                        <table style="width: 100%">
                                            <tbody>
                                            <tr>
                                                <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Confirmed By</b></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;">&nbsp;<?php echo $master['qutConfirmedUser']?></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Confirmed Date </b></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"> &nbsp;<?php echo $master['qutConfirmDate']?> </td>
                                            </tr>
                                            <?php if(($master['approvedYN1'] == '1')||($master['approvedYN1'] == '2')) {?>
                                            <tr>
                                                <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Status </b></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                            <?php if($master['approvedYN1'] == '1'){?>
                                                <td style="color:#636363ad;font-family: inherit;font-weight: 300;font-size: 8px;">&nbsp;<span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Accepted<!--Confirmed--></span></td>
                                                <?php } else {?>
                                                <td style="color:#636363ad;font-family: inherit;font-weight: 300;font-size: 8px;">&nbsp;<span class="label" style="background-color:#d80004; color: #FFFFFF; font-size: 11px;">Rejected<!--Confirmed--></span></td>
                                                <?php }?>
                                            </tr>


                                            <?php }?>
                                            <?php if(($master['approvalComment']!='')) {?>
                                            <tr>
                                                <td style="width: 28%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><b>Comment </b></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"><strong>: </strong></td>
                                                <td style="min-width: 85%;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"> &nbsp;<?php echo $master['approvalComment']?> </td>
                                            </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>

                                    </td>
                                    <td style="width:60%;">
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php }?>
                    <br>
                  <?php if(($master['confirmedYNqut']== 1)&&(($master['approvedYN1']!='1')&&($master['approvedYN1']!='2')&&($master['approvedYN1']!='3')&&($master['approvedYN1']!='4')&&($master['approvedYN1']!='5'))){?>
                      <div class="row">
                          <div class="form-group col-sm-12" style="margin-left: 10px;margin-top: 4%;">
                              <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                          </div>
                      </div>
                    <?php }?>
                    <br>
                    <?php if(($master['confirmedYNqut']== 1)&&(($master['approvedYN1']!='1')&&($master['approvedYN1']!='2')&&($master['approvedYN1']!='3')&&($master['approvedYN1']!='4')&&($master['approvedYN1']!='5'))){?>
                        <div class="row">
                            <div class="form-group col-sm-12" style="margin-left: 10px;">
                                <button class="btn buttonacceptanddecline btn-md btn-responsive"
                                        style="background-color: #64A758;border-color: #64A758;width: 38%;" type="button"
                                        onclick="save_accept(1,'<?php echo $quatationId ?>','<?php echo $companyID ?>','<?=$csrf['name'];?>','<?=$csrf['hash'];?>')">
                                    <i class="fa fa-check" aria-hidden="true"></i><br><strong>Accept</strong>
                                </button>
                                <button class="btn buttonacceptanddecline btn-md btn-responsive"
                                        style="background-color: #DF5240;border-color: #DF5240;width: 38%;" type="button"
                                        onclick="save_accept(2,'<?php echo $quatationId ?>','<?php echo $companyID ?>','<?=$csrf['name'];?>','<?=$csrf['hash'];?>')"><i class="fa fa-times" aria-hidden="true"></i><br><strong>DECLINE</strong>
                                </button>

                            </div>
                        </div>
                    <?php }?>

                    <br>
                    <br>


                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>

<script type="text/javascript">

    var quatationid = '<?php echo $quatationId?>';
    var companyid = '<?php echo $companyID?>';
    $(document).ready(function () {

        detailtable(quatationid,companyid,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>');
        });

        function save_accept(val,quatationId,companyID,csrf,hash) {
        var comments = $('#comments').val();

        if(val == 1)
        {
            swal({
                title: "Are you sure?",
                text: "You want to Accept this Estimate!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            },


            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {value: val,quatationId:quatationId,companyID:companyID,csrf_token:hash,comments:comments},
                    url: "<?php echo site_url('QuotationPortal/save_sales_quotation_customer_feedback'); ?>",
                    beforeSend: function () {

                    },
                    success: function (data) {
                        // myAlert(data[0], data[1]);
                        if(data[0]='s')
                        {
                            swal({
                            title: "Thank You!",
                            text: "For Accepting Our Estimate!",
                            type: "success"
                        }, function() {
                                location.reload()
                        });


                        }

                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                    }
                });
            });
        }else
        {
            swal({
                    title: "Are you sure?",
                    text: "You want to Reject this Estimate!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel"
                },


                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {value: val,quatationId:quatationId,companyID:companyID,csrf_token:hash,comments:comments},
                        url: "<?php echo site_url('QuotationPortal/save_sales_quotation_customer_feedback'); ?>",
                        beforeSend: function () {

                        },
                        success: function (data) {
                            // myAlert(data[0], data[1]);
                            if(data[0]='s')
                            {
                                location.reload();
                            }

                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                        }
                    });
                });
        }
    }
    function save_comment(quatationId,companyID,csrf,hash) {
        var comment = $('#comments').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {comment: comment,quatationId:quatationId,companyID:companyID,csrf_token:hash},
            url: "<?php echo site_url('QuotationPortal/save_sales_quotation_customer_feedback_comment'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                //myAlert(data[0], data[1]);
                if(data[0]='s')
                {
                    location.reload()

                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }
    function detailtable(quatationid,companyid,csrf_token,hash) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'quatationid': quatationid,'companyid':companyid,csrf_token:hash},
            url: "<?php echo site_url('QuotationPortal/load_qut_view'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#detail_tbl').html(data);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }
    function myAlert(type, message, duration=null) {
        toastr.clear();
        initAlertSetup(duration);
        if (type == 'e' || type == 'd') {
            toastr.error(message, 'Error'/*'Error!'*/);
           // check_session_status();
        } else if (type == 's') {
            toastr.success(message, 'Success'/*'Success!'*/);
        } else if (type == 'w') {
            toastr.warning(message, 'Warning'/*'Warning!'*/);
        } else if (type == 'i') {
            toastr.info(message, 'Information'/*'Information'*/);
        } else {
            //check_session_status();
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function initAlertSetup(duration=null) {
        duration = ( duration == null ) ? '1000' : duration;
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": duration,
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

    function alerMessage(type, message) {
        // message+='<br /><br /><button type="button" class="btn clear">Yes</button>';

        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": 0,
            "extendedTimeOut": 0,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        };
        toastr.clear();
        if (type == 'e') {
            toastr.error(message, 'Error!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, 'Success!');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, 'Warning!');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, 'Information');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function myAlert_topPosition(type, message) {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }


    </script>