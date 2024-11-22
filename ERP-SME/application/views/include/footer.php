</div></div><br><br>
<footer class="main-footer navbar-fixed-bottom" style="margin-left:0px;">
    <div class="pull-right hidden-xs">
        <b>Time {elapsed_time} </b> Memory {memory_usage}
    </div>
    <strong> Copyright &copy; 2015-2020 <a> Quantum </a>.</strong> All rights reserved.
</footer>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.css'); ?>">


<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
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

<style>
    #status_txt{
        font-weight: bold;
        color: #FF0000;
    }

    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }
</style>

<div class="modal fade" id="subStatus_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="">
    <div class="modal-dialog">
        <?php echo form_open('', 'role="form" id="subStatus_change_form" autocomplete="off" class="form-horizontal"'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                     Subscription status - <span id="sub_companyName" style="font-size: 14px;"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
                    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
                        <li class="active">
                            <a href="#update-tab" data-toggle="tab" aria-expanded="true">Update Status</a>
                        </li>
                        <li class="">
                            <a href="#history-tab" data-toggle="tab" aria-expanded="false">History</a>
                        </li>
                    </ul>
                    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">
                        <div class="tab-pane active" id="update-tab">
                            <input type="hidden" name="com_id" id="sub_companyID" value="">
                            <div class="form-group">
                                <label for="paymentEnabled" class="col-sm-3 control-label">Status</label>
                                <div class="col-sm-6">
                                    <?php
                                    $sub_status = ['' => 'Select a status', 0 => 'Active', 2 => 'On Hold', 1 =>'Inactive'];
                                    echo form_dropdown('status', $sub_status, '', 'class="form-control select2" 
                                                    id="subscription_status" onchange="$(\'#sub_comment\').val(\'\')" required"');
                                    ?>
                                </div>
                                <div class="col-sm-1"><span id="status_txt"></span></div>
                            </div>

                            <div class="form-group">
                                <label for="paymentEnabled" class="col-sm-3 control-label">Comment</label>
                                <div class="col-sm-8">
                                    <textarea name="sub_comment" id="sub_comment" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button class="btn btn-primary btn-sm pull-right" type="button" onclick="update_company_subscription()">Update</button>
                            </div>
                        </div>

                        <div class="tab-pane" id="history-tab">
                            <div class="table-responsive">
                                <table id="subHistory_tb" class="<?=table_class()?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 15px">#</th>
                                        <th style="width: auto">User</th>
                                        <th style="width: auto">Status</th>
                                        <th style="width: auto">Date time</th>
                                        <th style="width: 55px">Comment</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="paymentDet_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <?php echo form_open('', 'role="form" id="paymentDet_frm" autocomplete="off" class="form-horizontal"'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Add Payment Details - <span id="paymentDet_companyName" style="font-size: 14px;"></span>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="com_id" id="pay_company_id" value="">
                <input type="hidden" name="inv_id" id="pay_inv_id" value="">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Invoice No</label>
                            <div class="col-sm-5">
                                <input type="text" id="pay_invNo" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="paymentEnabled" class="col-sm-4 control-label">Payment Type</label>
                            <div class="col-sm-5">
                                <?php
                                $sub_status = payment_type([1,3,6], 'Select a type');
                                echo form_dropdown('pay_type', $sub_status, '', 'class="form-control select2" 
                                                id="pay_type" required"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Payment Date</label>
                            <div class="col-sm-5">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" class="form-control" id="pay_date" name="pay_date" value="<?=date('Y-m-d'); ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Amount</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                                    <input type="text" name="amount" id="pay_amount" class="form-control number" required value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="paymentEnabled" class="col-sm-4 control-label">Narration</label>
                            <div class="col-sm-8">
                                <textarea name="narration" id="narration" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" onclick="add_paymentDet()">Save</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $('.audit_log_date_pic, #pay_date').datepicker({
        format: "yyyy-mm-dd",
        viewMode: "months",
        minViewMode: "days"
    }).on('changeDate', function (ev) {
        $(this).datepicker('hide');
    });

    $('#pay_amount').numeric({decimalPlaces:3, negative:false});

    function refreshNotifications() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Dashboard/fetch_notifications"); ?>',
            dataType: 'json',
            async: true,
            success: function (data) {
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
            }
        });
    }

    function startLoad() {
        let logo = "<img src='<?=base_url('images/' . LOGO);?>' style='height: 50px;'/>";
        HoldOn.open({
            theme: "sk-bounce",//If not given or inexistent theme throws default theme , sk-bounce
            message: "<div style='font-size: 13px;'> Loading, Please wait </div><div>"+logo+"</div>",
            content: 'test', // If theme is set to "custom", this property is available
            textColor: "white" // Change the font color of the message
        });
    }

    function stopLoad() {
        HoldOn.close();
    }

    function myAlert(type, message, duration=null) {
        toastr.clear();
        initAlertSetup(duration);
        if (type == 'e' || type == 'd') {
            toastr.error(message, 'Error!');
        } else if (type == 's') {
            toastr.success(message, 'Success!');
        } else if (type == 'w') {
            toastr.warning(message, 'Warning!');
        } else if (type == 'i') {
            toastr.info(message, 'Information');
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

    function get_dataTable_det(tbl, obj){
        let table = $('#'+tbl).DataTable();
        let thisRow = $(obj);
        let details = table.row(  thisRow.parents('tr') ).data();

        return details;
    }

    function open_subChange_modal(company_id, status, tbl, obj) {
        $('#subStatus_modal').modal('show');

        let det = get_dataTable_det(tbl, obj); 
        //console.log(det)        
        let company_name = det.company_name+' [ '+det.company_code+' ] ';


        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/latest_history'); ?>",
            data: {'company_id': company_id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#sub_comment').val(data['comment']);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });

        load_subHistory(company_id);

        $('#sub_companyName').text(company_name);
        $('#sub_companyID').val(company_id);
        $('#status_txt').text('');
        if(status == 3){
            status = 0;
            $('#status_txt').text('Expired');
        }

        $('#subscription_status').val(status);
    }

    function load_subHistory(company_id) {
        $('#subHistory_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_company_subscription_history'); ?>",
            "aaSorting": [[3, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "columnDefs": [
                { "targets": [0,2,4], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "id"},
                {"mData": "Fullname"},
                {"mData": "sub_status"},
                {"mData": "createdDateTime"},
                {"mData": "comment"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'company_id', 'value': company_id});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    var refresh_tbl = null;
    function update_company_subscription() {
        var post_data = $('#subStatus_change_form').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/companyStatusChange'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    refresh_tbl.draw();
                    $('#subStatus_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function add_paymentDet(){
        let post_data = $('#paymentDet_frm').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/add_payment_details'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#pay-det-body').html(data['pay_det_view']);
                    $('#paymentDet_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function delete_invoice_payment_det(id){
        bootbox.confirm({
            title: '<strong>Confirmation!</strong>',
            message: 'You want to delete this record?<br/>',
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Dashboard/delete_invoice_payment_det'); ?>",
                        data: {'id': id},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);

                            if(data[0] == 's'){
                                $('#pay-det-body').html(data['pay_det_view']);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', errorThrown);
                        }
                    });
                }
            }
        });
    }

    function ajax_toaster( data, status_array = null, _cutomFn=null){
        let ajax_status_arr = (status_array == null)? ['e', 's']: status_array;

        if( ajax_status_arr.includes( data[0]) ){              
            myAlert(data[0], data[1]);
        }
        else{
            if(_cutomFn){
                _cutomFn(data);
                return false;
            }

            swal('Hey !', data[1], 'info');
        }
    }

    function undo_discharge_conf(empID, obj){
        let det = get_dataTable_det('com_user_tb', obj);
        let empCode = det.ECode;
        let empName = det.Ename2;

        swal({
            title: "You want to undo the dischrage?",
            text: empName+' [ '+empCode+' ]',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Proceed"
        },
        function () {
            undo_discharge(empID)
        });
    }


    function undo_discharge(empID, verify=0){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'company_id': companyid, 'empID': empID, 'verify': verify},
            url: "<?=site_url('Dashboard/undo_discharge'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();            
                ajax_toaster(data, null, active_as_basicUser);

                if(data[0] == 's'){
                    // com_user_tb variable defined in appropriate views
                    com_user_tb.ajax.reload();                    
                }

            }, error: function () {
                stopLoad();

            }
        });
    }

    function active_as_basicUser(data){
        bootbox.confirm({
            title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Warning!</strong>',
            message: data[1],
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    undo_discharge(data['empID'], verify=1);
                }
            }
        });
    }
</script>
</body>
</html>
