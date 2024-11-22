<!--Translation added by Naseek-->

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<style>
    .radio input[type="radio"] {
        opacity: 1;

    }

</style>
<?php


  $primaryLanguage = getPrimaryLanguage();
  $this->lang->load('hrms_leave_management', $primaryLanguage);
  $this->lang->load('common', $primaryLanguage);
  $this->lang->load('calendar', $primaryLanguage);
  $title = $this->lang->line('hrms_leave_management_new_leave_adjustment');
  echo head_page($title, FALSE);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
      <?php echo $this->lang->line('common_step'); ?><!--Step--> 1 -
      <?php echo $this->lang->line('hrms_leave_management_leave_adjustment_header'); ?><!--Leave Adjustment Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="leaveAdjustment()" data-toggle="tab">
      <?php echo $this->lang->line('common_step'); ?><!--Step--> 2
        -
      <?php echo $this->lang->line('hrms_leave_management_leave_adjustment_header_detail'); ?><!--Leave Adjustment Detail--></a>
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
                    <label for=""><?php echo $this->lang->line('common_description'); ?><!--Description--></label>
                    <input type="text" class="form-control " id="description" name="description">
                </div>

                <!--  <div class="form-group col-sm-4">
                    <label for="period">Month <?php /*required_mark(); */ ?></label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="period" id="period" class="form-control" required>
                    </div>
                </div>-->


                <div class="form-group col-sm-4">
                    <label for="JVType">
                      <?php echo $this->lang->line('hrms_leave_management_leave_group'); ?><!--Leave Group--> <?php required_mark(); ?></label>
                  <?php echo form_dropdown('leaveGroupID', leaveGroup_drop(), '',
                    'class="form-control select2" id="leaveGroupID" required  "'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="JVType"> <?php echo $this->lang->line('hrms_leave_management_policy'); ?><!--Policy--> <?php required_mark(); ?></label>
                  <?php echo form_dropdown('policyMasterID', dropdown_leavepolicy(), '',
                    'class="form-control select2" onchange="getperiodMonth(this.value)" required id="hrPeriod"  "'); ?>
                </div>
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
            <button class="btn btn-primary" type="submit">
              <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>

        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs ">

                <li class="active"><a data-toggle="tab" href="#tab_4" aria-expanded="false">
                    <?php echo $this->lang->line('common_details'); ?><!--Detail--></a></li>

            </ul>
            <div class="tab-content">
                <div id="tab_4" class="tab-pane active ">
                    <div class="row" style="margin: 0px;">
                        <div class="span12">
                            <button type="button" onclick="modalemployee()"
                                    class="btn btnhide btn-primary btn-xs pull-right"><?php echo $this->lang->line('hrms_leave_management_add_employee');?><!--Add Employee-->
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin: 0px;">
                        <div class="span12" id="divSettlementTable">

                        </div>
                    </div>

                    <hr>
                    <div class="text-right m-t-xs">
                        <a onclick="fetchPage('system/hrm/leave_adjustment','','HRMS')"
                           class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save'); ?><!--Save--></a>
                        <a onclick="confirmAdjustment()"
                           class="btn btn-sm btn-success">
                          <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></a>
                    </div>


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
                    <h5 class="modal-title">
                      <?php echo $this->lang->line('hrms_leave_management_group_detail'); ?><!--Group Detail--></h5>
                </div>
                <form role="form" id="jv_detail_form" class="form-horizontal">
                    <input type="hidden" id="xJVDetailAutoID" name="JVDetailAutoID">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                              <?php echo $this->lang->line('hrms_leave_management_leave_type'); ?><!--Leave Type--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                              <?php echo form_dropdown('leaveTypeID', leavemaster_dropdown(), '',
                                'class="form-control select2" id="leaveTypeID" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                              <?php echo $this->lang->line('hrms_leave_management_no_of_day'); ?><!--No of Days--> (
                              <?php echo $this->lang->line('common_month'); ?><!--Monthly-->)</label>
                            <div class="col-sm-6">
                                <input type="text" name="noOfDays" class="form-control" id="noOfDays">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                          <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="submit" id="save_btn">
                          <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="add_employee_modal" class="modal fade">
        <div class="modal-dialog" style="width: 50%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                      <?php echo $this->lang->line('hrms_leave_management_add_employee'); ?><!--Group Detail--></h5>
                </div>
                <form role="form" id="employee_from" class="form-horizontal">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                            <div class="col-sm-6" id="">
                              <?php echo form_dropdown('segmentID[]', fetch_segment(TRUE, FALSE), '',
                                'onchange="modaladjustmentEmployee()" class="form-control" multiple id="segmentID" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                              <?php echo $this->lang->line('hrms_leave_management_employee'); ?><!--Leave Type--> <?php required_mark(); ?></label>

                            <div class="col-sm-6" id="employee_drop">

                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                          <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="submit" id="save_btn">
                          <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        /*    $('#segmentID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                maxHeight: '200px'

            });*/
        $('#segmentID')
            .multiselect2({
                allSelectedText: 'All',
                maxHeight: 200,
                includeSelectAllOption: true
            })
            .multiselect2('selectAll', false)
            .multiselect2('updateButtonText');

        var leaveGroupID;
        var policyMasterID;
        var masterID;
        $("#leaveGroupID").prop("disabled", false);
        $("#description").prop("disabled", false);
        $("#hrPeriod").prop("disabled", false);
        $(document).ready(function () {
            $('#period').datepicker({

                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months"
            }).on('changeDate', function (ev) {

                $(this).datepicker('hide');
            });


            masterID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

            if (masterID) {
                $('[href=#step2]').tab('show');
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $('[href=#step2]').removeClass('btn-default');
                $('[href=#step2]').addClass('btn-primary');
                $('.btn-wizard').removeClass('disabled');
                // load_journal_entry_header();
                getleaveGroupheader();
                leaveAdjustment();


            }
            else {
                $('.btn-wizard').addClass('disabled');
            }

            $('.headerclose').click(function () {


                fetchPage('system/hrm/leave_adjustment', '', 'HRMS');
            });
            $('.select2').select2();

            $('#Journal_entry_form').bootstrapValidator({

                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required.*/


                },
            }).on('success.form.bv', function (e) {
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
                    url: "<?php echo site_url('Employee/save_leave_adjustment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {

                        if (data['error'] == 0) {
                            $("#hrPeriod").prop("disabled", true);
                            $("#leaveGroupID").prop("disabled", true);
                            $("#description").prop("disabled", true);

                            $('.btn-wizard').removeClass('disabled');
                            masterID = data['leaveGroupID'];
                            leaveGroupID = $("#leaveGroupID").val();
                            policyMasterID = $("#hrPeriod").val();
                            setTimeout(function () {
                                $('[href=#step2]').tab('show');
                                $('a[data-toggle="tab"]').removeClass('btn-primary');
                                $('a[data-toggle="tab"]').addClass('btn-default');
                                $('[href=#step2]').removeClass('btn-default');
                                $('[href=#step2]').addClass('btn-primary');
                            }, 500);
                            leaveAdjustment();

                        }

                        stopLoad();
                        //refreshNotifications(true);
                        myAlert('s', 'saved');
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
        $('#employee_from').bootstrapValidator({

            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}}


            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'masterID', 'value': masterID});
            data.push({'name': 'leaveGroupID', 'value': leaveGroupID});
            data.push({'name': 'policyMasterID', 'value': policyMasterID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_leave_adjustmentDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   /* $('#employee_from')[0].reset();
                    $('#employee_from').bootstrapValidator('resetForm', true);*/
                    $form.bootstrapValidator('disableSubmitButtons', false);
                    if (data['error'] == 's') {
                        leaveAdjustment();
                        modaladjustmentEmployee();

                       refreshNotifications(true);
                    }else{
                        myAlert(data['error'], data['message']);
                    }
                    $('#empID').multiselect2('deselectAll', false);
                    $('#empID').multiselect2('updateButtonText');


                    stopLoad();
                    //refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    //refreshNotifications(true);
                }
            });

        });

        function modalemployee() {

            $('#add_employee_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#segmentID').multiselect2('selectAll', false);
            $('#segmentID').multiselect2('updateButtonText');

            modaladjustmentEmployee();

        }

            function modaladjustmentEmployee() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {
                        leaveGroupID: leaveGroupID,
                        masterID: masterID,
                        policyMasterID: policyMasterID,
                        segmentID: $('#segmentID').val()


                    },
                    url: "<?php echo site_url('Employee/leave_adjustment_employees_drop'); ?>",
                    beforeSend: function () {

                    },
                    success: function (data) {

                        $('#employee_drop').html(data);
                        $('.empID').multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            maxHeight: '200px'
                        });


                    }, error: function () {

                        myAlert('e', 'An Error Occurred! Please Try Again.');

                    }
                });
            }

            function updatecomment(value, empID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        masterID: masterID,
                        value: value,
                        empID: empID,

                    },
                    url: "<?php echo site_url('Employee/update_leave_adjustmentcomment'); ?>",
                    beforeSend: function () {

                    },
                    success: function (data) {

                    }, error: function () {

                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again.*/

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


            function leaveAdjustment() {

                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Employee/leaveAdjustmentDetail'); ?>",
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
            }).on('success.form.bv', function (e) {
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
                            leaveAdjustment()
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

            function getfacilityfatefrom() {
                setinitlainterstpaymentDate();
                $.ajax({
                    type: "POST",
                    /*    url: "ajax/ajax-get-treasury-load-facility-date-from.php",*/
                    url: "<?php echo site_url('Bank_rec/getfacilityfatefrom'); ?>",
                    data: {
                        datefrom: $("#facilityDateFrom").val(),
                        installmentType: $('#installmentID').val(),
                        noInstallment: $('#noInstallment').val()
                    },
                    dataType: "json",
                    cache: false,

                    beforeSend: function () {
                    },

                    success: function (data) {
                        $("#facilityDateTo").val(data['value']);

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

                                $("#hrPeriod").prop("disabled", true);
                                $("#leaveGroupID").prop("disabled", true);
                                $("#description").prop("disabled", true);
                                $("#description").val(data['description']);
                                $("#leaveGroupID").val(data['leaveGroupID']).change();
                                $("#hrPeriod").attr('onchange', '').val(data['policyMasterID']).change();
                                $("#hrPeriod").attr('onchange', 'getperiodMonth(this.value)');
                                if (data['confirmedYN'] == 1) {
                                    $('.btnhide').hide();
                                }
                                leaveGroupID = data['leaveGroupID'];
                                policyMasterID = data['policyMasterID'];
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

            function confirmAdjustment() {
                if (masterID) {
                    swal({
                            title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                            text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                        },
                        function () {
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: {'masterID': masterID},
                                url: "<?php echo site_url('Employee/confirm_leaveadjustment'); ?>",
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    refreshNotifications(true);
                                    if (data['error'] == 0) {
                                        myAlert('s', data['message']);
                                        fetchPage('system/hrm/leave_adjustment', '', 'HRMS');
                                    }
                                    else {
                                        myAlert('e', data['message']);
                                    }


                                }, error: function () {
                                    swal("Cancelled", "Your file is safe :)", "error");
                                }
                            });
                        });
                }

            }


            function delete_adjustment(empID) {
                if (masterID) {
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
                                data: {'masterID': masterID, 'empID': empID},
                                url: "<?php echo site_url('Employee/delete_adjustmentDetail'); ?>",
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    refreshNotifications(true);
                                    leaveAdjustment();
                                }, error: function () {
                                    swal("Cancelled", "Your file is safe :)", "error");
                                }
                            });
                        });
                }

            }

    </script>