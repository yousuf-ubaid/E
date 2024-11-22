</div><!-- /.content-wrapper -->
</div>
<footer class="main-footer <?php echo isset($notFixed) ? 'hide' : ''; /*navbar-fixed-bottom*/ ?>"
        style="padding:4px; opacity: 0.5">
    <div class="pull-right hidden-xs">
        <!--<b><?php /*echo "Time "; */ ?>{elapsed_time} </b><?php /*echo " Memory "; */ ?>{memory_usage}-->
        <b><?php echo "Timezone :  " . current_timezoneDescription(); ?></b>
    </div>
    <strong> Copyright &copy; 2015-2020</strong> All rights reserved.
</footer>

<?php if (strtolower(SETTINGS_BAR) == 'on') { ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <!--<li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>-->
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
                <!-- /.control-sidebar-menu -->
            </div><!-- /.tab-pane -->

            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">

            </div><!-- /.tab-pane -->
        </div>
    </aside><!-- /.control-sidebar -->
<?php } ?>

<div class="control-sidebar-bg"></div>

</div><!-- ./wrapper -->

<?php
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();

$this->load->view('include/inc-footer-modals'); ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>

<!--2017-06-13-->
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datatables/fixedColumns.dataTables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datatables/keyTable.dataTables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/select.dataTables.min.css'); ?>"/>
<!--End-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>

<!--2017-06-13-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/datatables/dataTables.fixedColumns.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.select.js'); ?>"></script>
<!--End-->

<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
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
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>

<!--jquery auto complete-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>

<script type="text/javascript">

    //fetchcompany(<?php //echo current_companyID() ?>, false);
    var popup = 0;
    var CSRFHash = '<?php echo $this->security->get_csrf_hash() ?>';
    var numberOfAttempt = 0;

    /*Remove employee master filter values */
    window.localStorage.removeItem("isDischarged");
    window.localStorage.removeItem("employeeCode");
    window.localStorage.removeItem("segment");
    window.localStorage.removeItem('emp-master-alpha-search');
    window.localStorage.removeItem('emp-master-searchKeyword');
    window.localStorage.removeItem('emp-master-designation-list');
    window.localStorage.removeItem('emp-master-segment-list');
    window.localStorage.removeItem('emp-master-status-list');
    window.localStorage.removeItem('emp-master-pagination');


    function check_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();

            }
        });
    }

    function refresh_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function session_logout_page() {
        swal({
            title: "Session Destroyed!",
            text: "You will be redirect to login page in 2 seconds.",
            timer: 2000,
            showConfirmButton: false
        });
        setTimeout(function () {
            window.location = '<?php echo site_url('/Login/logout'); ?>';
        }, 2000);
    }


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

    Number.prototype.formatMoney = function (c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    function number_validation() {
        $(".number").attr('autocomplete', 'off');
        $(".number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
            }
        });
        $(".m_number").attr('autocomplete', 'off');
        $(".m_number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^-0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
                ;
            }
        });
    }

    function commaSeparateNumber(val, dPlace = 2) {
        var toFloat = parseFloat(val);
        var a = toFloat.toFixed(dPlace);
        while (/(\d+)(\d{3})/.test(a.toString())) {
            a = a.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return a;
    }

    function date_format_change(userdate, policydate) {
        var date_string = moment(userdate, "YYYY-MM-DD").format(policydate);
        return date_string;
    }

    function removeCommaSeparateNumber(val) {
        return parseFloat(val.replace(/,/g, ""));
    }


    function myAlert(type, message, duration = null) {
        toastr.clear();
        initAlertSetup(duration);
        if (type == 'e' || type == 'd') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>'/*'Error!'*/);
            check_session_status();
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>'/*'Success!'*/);
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>'/*'Warning!'*/);
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>'/*'Information'*/);
        } else {
            check_session_status();
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function initAlertSetup(duration = null) {
        duration = (duration == null) ? '1000' : duration;
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
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>');
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

    function startLoad() {
        HoldOn.open({
            theme: "sk-rect",//If not given or inexistent theme throws default theme , sk-bounce , sk-cube-grid
            message: "<div style='font-size: 16px; color:#ffffff; margin-top:20px;     text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;'> Loading, Please wait </div><div id='loaderDivContent'></div>",
            content: 'test', // If theme is set to "custom", this property is available
            textColor: "#000000" // Change the font color of the message
        });
    }

    function startLoadPos() {
        $("#posPreLoader").show();
    }

    function stopLoad() {
        HoldOn.close();
        $("#posPreLoader").hide();
    }

    function modalFix() {
        setTimeout(function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        }, 500);
    }

    function csrf_init() {
        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            if (originalOptions.type == 'POST' || originalOptions.type == 'post' || options.type == 'POST' || options.type == 'post') {
                if (options.processData) { /*options.contentType === 'application/x-www-form-urlencoded; charset=UTF-8'*/
                    options.data = (options.data ? options.data + '&' : '') + $.param({'<?php echo $this->security->get_csrf_token_name(); ?>': CSRFHash});
                } else {
                    options.data.append('<?php echo $this->security->get_csrf_token_name(); ?>', CSRFHash);
                }
            } else {
                if (options.processData) {
                } else {
                }
            }
        });

    }

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });

        $(".select2").select2();

        csrf_init();

        setInterval(function () {
            refresh_session_status();
        }, 3601000);


        var company_id = '<?php echo json_encode($this->common_data['company_data']['company_id']); ?>';
        var company_code = '<?php echo json_encode($this->common_data['company_data']['company_code']); ?>';
        if (company_code == 'null') {
            fetchPage('system/company/erp_company_configuration_new', company_id, 'Add Company', 'COM');
        }
        initTouchKeyboard();
    });

    function getMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    function set_navbar_cookie() {
        var classVal = $('body').attr('class');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Dashboard/set_navbar_cookie'); ?>",
            data: {'className': classVal},
            dataType: "json",
            cache: true
        });
    }


    function isDateInputMaskNotComplete(dateStr) {
        return (/[dmy]/.test(dateStr))
    }


    function open_access_denied_alertMod() {

        $("#access_denied_alertDiv").remove();
        $('body').append('<div class="modal fade-scale" id="access_denied_alertDiv" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document" style="width: 35%"><div class="modal-content" style=" border:3px solid #cc0000;"><div class="modal-body" style="text-align: center;"><div class="row-fluid"><div class="span12" style="text-align: center;"><span><i class="fa fa-warning" style="font-size:24px;color:#cc0000;margin-left: 10px;float: left;"></i><b style="text-align: center;"> Access Denied!</b></span><br><br><span>You do not have sufficient privileges to access this feature. Please contact the admin for the access.</span></div></div><button type="button" class="btn btn-danger btn-flat btn-sm pull-right" data-dismiss="modal">Okay</button></div></div></div></div>');

        $('#access_denied_alertDiv').modal('show');

    }


    function initTouchKeyboard() {
        var screenWidth = $(window).width();
        var keyboardPolicy = <?php
            $outletID = get_outletID();
            $companyID = current_companyID();
            $status = isOnscreenKeyboardHidden($outletID, $companyID);
            if ($status) {
                echo 'true';
            } else {
                echo 'false';
            }
            ?>;

        if (screenWidth > 768 && !keyboardPolicy) {
            $('.custom_touch_keyboad').mlKeyboard({
                layout: 'en_US'
            });
        }
    }

    $(document).keyup(function(e) {
        if(e.keyCode== 27) {
            $("div.touchEngKeyboard").hide();
        }
    });

    // $('.custom_touch_keyboad').click(function () {
    //     $('.custom_touch_keyboad').focus();
    // });

</script>

</body>
</html>