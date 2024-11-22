
</body>
</html>


<!--<script src="<?php /*echo base_url('plugins_spurgo/vendor/jquery/jquery.min.js'); */?>"></script>
<script src="<?php /*echo base_url('plugins_spurgo/js/main.js'); */?>"></script>-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.css'); ?>">



<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript">

function refreshNotifications() {
    $.ajax({
        type: 'POST',
        url: '<?php echo site_url("Dashboard/fetch_notifications"); ?>',
        dataType: 'json',
        async: true,
        success: function (data) {
            check_session_status();
            if (!jQuery.isEmptyObject(data)) {
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
                $.each(data, function (i, v) {
                    toastr[v.t](v.m, v.h);
                });
            }
            stopLoad();
        },
        error: function () {
            stopLoad();
        }
    });
}

function notification(message, status) {
    toastr.options = {
        "positionClass": "toast-bottom-right",
    }

    if (status == undefined) {
        toastr.error(message)
    } else if (status == 's') {
        toastr.success(message);
    } else if (status == 'w') {
        toastr.warning(message);
    } else if (status == 'i') {
        toastr.info(message);
    } else {
        toastr.error(message);
    }
}
function myAlert(type, message, duration=null) {
    toastr.clear();
    initAlertSetup(duration);
    if (type == 'e' || type == 'd') {
        toastr.error(message, 'Error!'/*'Error!'*/);

    } else if (type == 's') {
        toastr.success(message,'Success!'/*'Success!'*/);
    } else if (type == 'w') {
        toastr.warning(message,'Warning!'/*'Warning!'*/);
    } else if (type == 'i') {
        toastr.info(message,'Information!'/*'Information'*/);
    } else {
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
        toastr.info(message, 'Information!');
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
        toastr.error(message, 'Error!');
        /*Error*/
    } else if (type == 's') {
        toastr.success(message, 'Success!');
        /*Success!*/
    } else if (type == 'w') {
        toastr.warning(message, 'Warning!');
        /*Warning!*/
    } else if (type == 'i') {
        toastr.info(message, 'Information!');
        /*Information*/
    } else {
        toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
    }
}
function startLoad_spur_go() {
    HoldOn.open({
        theme: "sk-bounce",//If not given or inexistent theme throws default theme , sk-bounce
        message: "<div style='font-size: 13px;'> Loading, Please wait </div><div><img src='<?php
            echo base_url('images/' . LOGO);?>' style='height: 50px;'/></div>",
        content: 'test', // If theme is set to "custom", this property is available
        textColor: "white" // Change the font color of the message
    });
}

function stopLoad_spur_go() {
    HoldOn.close();
}

</script>