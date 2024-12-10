<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
<body>
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

    <div class="col-md-12">

        <div class="row">
            <div class="text-center">
                <img alt="Logo" style="max-height: 50px; max-width:200px; margin:30px 0px;" src="https://fms.ilooops.com/images/logo.png">
            </div>
        </div>

        <div class="alert alert-danger text-center" style="margin:25px 0px; font-size:15px;">
            <span>Invalid Request Received ! </span>
        </div>
    </div>
   
</body>
</html>

<script type="text/javascript">

    var req_reference = '<?php echo $reference ?>';
    var company = '<?php echo $comp ?>';


    function save_accept() {
        //var comments = $('#comments').val();
        var data = $("#visitor_log_form").serializeArray();

        data.push({'name':'reference', 'value': req_reference});
        data.push({'name':'setting', 'value': company});
        // data.push({'name':'companyID', 'value': companyID});
        // data.push({'name':'csrf_token', 'value': hash});

        swal({
            title: "Are you sure?",
            text: "You want to Submit this !",
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
                data:data,
                url: "<?php echo site_url('ilooops/save_visitor_log_online'); ?>",
                beforeSend: function () {

                },
                success: function (data1) {

                    if(data1.status == 'success'){
                        myAlert_topPosition('s', data1.message);
                        $("#visitor_log_form")[0].reset();
                    }else{
                        myAlert_topPosition('e', 'Something went wrong');
                    }

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                }
            });
        });

    }

    function myAlert_topPosition(type, message) {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right animated-panel fadeInRight",
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
        }

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