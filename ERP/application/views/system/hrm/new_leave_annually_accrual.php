
<!--Translation added by Naseek-->

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_new_leave_accrual');
echo head_page($title, false);

$saveUrl = site_url('Employee/save_leave_annualAccrual');
$updateUrl = site_url('Employee/update_leave_annualAccrual');
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"> <?php echo $this->lang->line('common_step');?><!--Step--> 1 - <?php echo $this->lang->line('hrms_leave_management_leave_accrual_header');?><!--Leave Accrual Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="loanSettlementTable()" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 2
        - <?php echo $this->lang->line('hrms_leave_management_leave_accrual_detail');?><!--Leave Accrual Detail--></a>
    <!--<a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation()" data-toggle="tab">Step 3 - JV
        Confirmation</a>-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="Journal_entry_form"'); ?>


        <div class="row">
            <div class="col-md-12">

                <div class="form-group col-sm-4">
                    <label for=""><?php echo $this->lang->line('common_description');?><!--Description--></label>
                    <input type="text" class="form-control " id="description" name="description">
                </div>



                <div class="form-group col-sm-4">
                    <label for="JVType"> <?php echo $this->lang->line('hrms_leave_management_leave_group');?><!--Leave Group--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('leaveGroupID', leaveGroup_drop(), '', 'class="form-control select2" id="leaveGroupID" required  "'); ?>
                </div>
                <!--  <div class="form-group col-sm-4">
                    <label for="JVType"> HR Period <?php /*required_mark(); */ ?></label>
                   <?php /*echo form_dropdown('hrPeriod', hrPeriod_drop(), '', 'class="form-control select2" onchange="getperiodMonth(this.value)" required id="hrPeriod"  "'); */ ?>
                </div>-->
            </div>

        </div>
        <!--        <div class="row">
            <div class="col-md-12">


                <div class="form-group col-sm-4">
                    <label for="JVType"> HR Period Month <?php /*required_mark(); */ ?></label>
                    <span id="div_hrPeriodMonth"> <?php /*echo form_dropdown('hrPeriodMonth', hrPeriodMonth_drop(), '', 'class="form-control select2"  id="hrPeriodMonth"  "'); */ ?></span>
                </div>
            </div>

        </div>
-->
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary btnhide" type="submit"><?php echo $this->lang->line('common_generate');?><!--Generate--></button>
            <button class="btn btn-primary btnhide updateBtn" type="submit"><?php echo $this->lang->line('common_update');?><!--Update--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs ">

                <li class="active"><a data-toggle="tab" href="#tab_4" aria-expanded="false"><?php echo $this->lang->line('common_details');?><!--Detail--></a></li>

            </ul>
            <div class="tab-content" ">
                <div id="tab_4" class="tab-pane active ">
                    <div id="divSettlementTable" style="overflow: scroll;">

                    </div>
                    <hr>



                </div>
            </div>

        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="jv_detail_modal" class="modal fade">
        <div class="modal-dialog" style="width: 50%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title"><?php echo $this->lang->line('hrms_leave_management_group_detail');?><!--Group Detail--></h5>
                </div>
                <form role="form" id="jv_detail_form" class="form-horizontal">
                    <input type="hidden" id="xJVDetailAutoID" name="JVDetailAutoID">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_leave_type');?><!--Leave Type--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('leaveTypeID', leavemaster_dropdown(), '', 'class="form-control select2" id="leaveTypeID" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_no_of_day');?><!--No of Days--> (<?php echo $this->lang->line('common_monthly');?><!--Monthly-->)</label>
                            <div class="col-sm-6">
                                <input type="text" step="any" name="noOfDays" class="form-control" id="noOfDays">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                        <button class="btn btn-primary" type="submit" id="save_btn"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<div aria-hidden="true" role="dialog" id="not_approved_leave" class="modal fade">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">Approve following leaves</h4>
            </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <td>Employee Code</td>
                                <td>Document Code</td>
                            </tr>
                            </thead>
                            <tbody id="not_approved_leavedet">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#period').datepicker({

                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months"
            }).on('changeDate', function (ev) {

                $(this).datepicker('hide');
            });
            $('.btnhide').show();
            $('.updateBtn').hide();
            $("#description").attr('disabled',false);
            $("#leaveGroupID").attr('disabled',false);
            $("#hrPeriod").attr('disabled',false);
            $("#hrPeriodMonth").attr('disabled',false);



            $('#Journal_entry_form').attr('action', '<?php echo $saveUrl ?>');
            masterID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

            if (masterID) {
                $('[href=#step2]').tab('show');
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $('[href=#step2]').removeClass('btn-default');
                $('[href=#step2]').addClass('btn-primary');
                $('.btn-wizard').removeClass('disabled');
                // load_journal_entry_header();
                $('#Journal_entry_form').attr('action', '<?php echo $updateUrl ?>');
                getleaveGroupheader();
                loanSettlementTable();


            }
            else {
                $('.btn-wizard').addClass('disabled');
            }

            $('.headerclose').click(function () {



                fetchPage('system/hrm/leave_accrual_annually','','Leave Annual accrual')


            });
            $('.select2').select2();

            $('#Journal_entry_form').bootstrapValidator({

                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    description: {validators: {notEmpty: {message: 'Description is required.'}}}


                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var url = $form.attr('action');
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();

                data.push({'name': 'masterID', 'value': masterID});

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: url,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (data['error'] == 0) {
                            $('.btn-wizard').removeClass('disabled');
                            masterID = data['leaveGroupID'];
                            setTimeout(function () {
                                $('[href=#step2]').tab('show');
                                $('a[data-toggle="tab"]').removeClass('btn-primary');
                                $('a[data-toggle="tab"]').addClass('btn-default');
                                $('[href=#step2]').removeClass('btn-default');
                                $('[href=#step2]').addClass('btn-primary');
                            }, 500);

                            loanSettlementTable();
                            $('.btnhide').hide();
                            $("#leaveGroupID").attr('disabled',true);
                            $("#hrPeriod").attr('disabled',true);
                            $("#hrPeriodMonth").attr('disabled',true);

                            if(data['confirmedYN'] == 1){
                                $("#description").attr('disabled',true);
                            }
                            if(data['confirmedYN'] != 1){
                                $('.updateBtn').show();
                            }

                            /* $('.btn-wizard').removeClass('disabled');*/
                            // load_journal_entry_header();
                            myAlert('s', 'saved successfully');

                        }

                        stopLoad();

                        if(data[0] == 's' || data[0] == 'e'){
                            myAlert(data[0], data[1]);

                            setTimeout(function(){
                                $('.updateBtn').attr('disabled', false);
                            }, 300);
                        }

                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        //refreshNotifications(true);
                    }
                });
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
            });

            $('.next').click(function () {
                var nextId = $(this).parents('.tab-pane').next().attr("id");
                $('[href=#' + nextId + ']').tab('show');
            });

            $('.prev').click(function () {
                var prevId = $(this).parents('.tab-pane').prev().attr("id");
                $('[href=#' + prevId + ']').tab('show');
            });
        });

        function confirmAccrual(){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    masterID: masterID

                },
                url: "<?php echo site_url('Employee/confrim_leave_accrual'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if(data[0]=='s'){
                        fetchPage('system/hrm/leave_accrual_annually','','Leave Annual accrual');
                    }else{
                        $("#not_approved_leave").modal('show');
                        open_not_approved_leave_model(data[2])
                    }

                    /* myAlert('s', 'saved');*/
                }, error: function () {

                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    //refreshNotifications(true);
                }
            });
        }
        function updateLeaveAdjustmentDetail(policyMasterID, days, leaveTypeID, empID) {

            if (policyMasterID == 2) {
                row = $(days).closest('td');
                hours = parseInt(row.find("input[name*='noOfHours']").val());
                minutes = parseInt(row.find("input[name*='NoOfMinutes']").val());
                cal = (hours * 60) + minutes;

            } else {
                cal = days.value;
            }

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    masterID: masterID,
                    policyMasterID: policyMasterID,
                    days: cal,
                    leaveTypeID: leaveTypeID,
                    empID: empID
                },
                url: "<?php echo site_url('Employee/update_leave_adjustment'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (data['error'] == 0) {
                        //leaveAdjustment();
                    }

                    //refreshNotifications(true);
                    /* myAlert('s', 'saved');*/
                }, error: function () {

                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    //refreshNotifications(true);
                }
            });
        }


        function loanSettlementTable() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Employee/LeaveAccrualdetails'); ?>",
                data: {masterID: masterID},
                dataType: "html",
                cache: false,

                beforeSend: function () {
                },

                success: function (data) {
                    $("#divSettlementTable").html(data);

                }
            });
            return false;
        }


        $('#jv_detail_form').bootstrapValidator({

            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                leaveTypeID: {validators: {notEmpty: {message: 'Leave Type is required.'}}},
                noOfDays: {validators: {notEmpty: {message: 'No of Days is required.'}}},

            },
        })
        .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'masterID', 'value': masterID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_leaveGroupdetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data['error'] == 0) {
                        loanSettlementTable()
                    }

                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        function getperiodMonth(hrPeriodID) {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Employee/get_hrPeriodMonth'); ?>",
                data: {hrPeriodID: hrPeriodID},

                dataType: "html",
                cache: false,

                beforeSend: function () {
                },

                success: function (data) {
                    $("#div_hrPeriodMonth").html(data);
                    $('.select2').select2();


                }
            });
            return false;
        }

        function getleaveGroupheader() {
            if (masterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': masterID},
                    url: "<?php echo site_url('Employee/getAccrualHeader'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {


                            $("#description").val(data['description']).attr('disabled', true);
                            $("#leaveGroupID").val(data['leaveGroupID']).change();
                            $("#hrPeriod").val(data['hrPeriod']).change();
                            $("#hrPeriodMonth").val(data['period']).change();
                            $('.btnhide').hide();
                            $("#leaveGroupID").attr('disabled',true);


                            if(data['confirmedYN'] != 1){
                                $('.updateBtn').show();
                                $("#description").attr('disabled',false);
                            }

                            $("#hrPeriod").attr('disabled',true);
                            $("#hrPeriodMonth").attr('disabled',true);

                            /* */
                            /*companyID*/
                            /* facilityCode*/
                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }


        function modalleaveDetail() {
            if (masterID) {
                $('#jv_detail_form')[0].reset();
                $('#jv_detail_form').bootstrapValidator('resetForm', true);
                $("#jv_detail_modal").modal({backdrop: "static"});
            }
        }


        function delete_item(id, value) {
            if (JVMasterAutoId) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                        text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55 ",
                        confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'JVDetailAutoID': id},
                            url: "<?php echo site_url('Journal_entry/delete_Journal_entry_detail'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                fetch_journal_entry_detail();
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
            ;
        }

        function open_not_approved_leave_model(data){
            $('#not_approved_leavedet').empty();
            $.each(data, function (key, value) {
                $('#not_approved_leavedet').append('<tr><td>' + value['ECode'] + '</td><td>' + value['documentCode'] + '</td></tr>');
            });

        }

    </script>