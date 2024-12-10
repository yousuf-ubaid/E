 
<!--Translation added by Naseek-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_new_leave_group');
echo head_page($title, false);


$current_date = format_date($this->common_data['current_date']);
$currency_arr = all_currency_new_drop();
$financeyear_arr = all_financeyear_drop();
$gl_code_arr = company_PL_account_drop();
$segment_arr = fetch_segment();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 1 - <?php echo $this->lang->line('hrms_leave_management_leave_group_header');?><!--Leave Group Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="leaveGroupdetails()" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 2
        - <?php echo $this->lang->line('hrms_leave_management_leave_group_detail');?><!--Leave Group Detail--></a>
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


             <!--   <div class="form-group col-sm-4">
                    <label for="JVType"> <?php /*echo $this->lang->line('hrms_leave_management_policy');*/?> <?php /*required_mark(); */?></label>
                    <?php /*echo form_dropdown('isMonthly', dropdown_leavepolicy(), '', 'class="form-control select2" id="isMonthly" required "'); */?>
                </div>-->

            </div>

        </div>

        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs ">

                <li class="active"><a data-toggle="tab" href="#tab_4" aria-expanded="false"><?php echo $this->lang->line('common_details');?><!--Detail--></a></li>

            </ul>
            <div class="tab-content">
                <div id="tab_4" class="tab-pane active ">
                    <div id="divSettlementTable">

                    </div>
                    <hr>
                    <div class="text-right m-t-xs">
                        <a onclick="fetchPage('system/hrm/leave_group','','HRMS')"
                           class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></a>
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
                    <h5 class="modal-title"><?php echo $this->lang->line('hrms_leave_management_group_detail');?><!--Group Detail--></h5>
                </div>
                <form role="form" id="jv_detail_form" class="form-horizontal">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_policy');?><!--Leave policy--> <?php required_mark(); ?></label>
                            <div class="col-sm-6" id="leavepolicyx">
                              <?php echo form_dropdown('leavepolicyID', dropdown_leavepolicy(), '', ' onchange="policyValidation(this.value)" class="form-control select2" id="leavepolicyID" required "'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_leave_type');?><!--Leave Type--> <?php required_mark(); ?></label>
                            <div class="col-sm-6" id="leavepolicyx">
                                <?php echo form_dropdown('leaveTypeID', leavemaster_dropdown(), '', 'class="form-control select2" id="leaveTypeID" required'); ?>
                            </div>
                        </div>

                        <div class="form-group" id="rotation-days-section">
                            <div class="col-sm-12">
                                <label class="col-sm-4 control-label" for="rotationWorkingDays"> <?php echo $this->lang->line('hrms_leave_management_rotation_working_days');?><!--Is Carry forward--></label>
                                <div class="col-sm-6">
                                <div class="input-group" style="">
                                    <span class=""><input type="number" id="rotationWorkingDays" name="rotationWorkingDays" step="any" class="col-sm-8 form-control" value="" onchange="validate_noofdays(this)"></span>
                                </div>
                            </div>
                            </div>
                        </div>
                        
                        <div class="form-group dayForm hide ">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_leave_management_no_of_day');?><!--No of Days--></label>
                            <div class="col-sm-6">
                                <input type="number" name="noOfDays" step="any" class="form-control" id="noOfDays">
                            </div>
                        </div>
                       

                        <div class="form-group dayForm hide ">
                            <label class="col-sm-4 control-label" for="JVType"> <?php echo $this->lang->line('hrms_leave_management_is_calender_days');?><!--Is calender Days--></label>
                            <div class="col-sm-6 " style="margin-top: 7px">
                                <input type="checkbox" id="isCalenderDays" name="isCalenderDays" value="1">
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-4 control-label" for="JVType"> <?php echo $this->lang->line('hrms_leave_management_is_allow_minus');?><!--Is Allow minus--></label>
                            <div class="col-sm-6 " style="margin-top: 7px">
                                <input type="checkbox" id="isAllowminus" name="isAllowminus" value="1">
                            </div>
                        </div>

                        <div class="form-group carry-forward">
                            <label class="col-sm-4 control-label" for="isCarryForward"> <?php echo $this->lang->line('hrms_leave_management_is_carry_forward');?><!--Is Carry forward--></label>
                            <div class="col-sm-1 " style="margin-top: 7px">
                                <input type="checkbox" id="isCarryForward" name="isCarryForward" value="1" onclick="changeCarryForward()">
                            </div>
                            <!--<div class="col-sm-5 carry-forward carry-forward-limit">
                                <input type="number" id="carryForwardLimit" name="carryForwardLimit" step="any" class="form-control" value="" onchange="validate_noofdays(this)">
                            </div>-->
                        </div>

                        <div class="form-group carry-forward carry-forward-limit">
                            <label class="col-sm-4 control-label" for="isCarryForward"> Maximum Carry forward</label>
                            <div class="col-sm-1 " style="margin-top: 7px">
                                <input type="checkbox" id="maxCarryForward" name="maxCarryForward" value="1" onclick="changeCarryForwardMax()">
                            </div>
                            <div class="col-sm-5 carry-forward ">
                                <input type="number" id="carryForwardLimit" name="carryForwardLimit" step="any" class="form-control" readonly value="" onchange="validate_noofdays(this)">
                            </div>
                        </div>

                        <div class="form-group rotation-leave">
                            <label class="col-sm-4 control-label" for="isRotationLeave"> <?php echo $this->lang->line('hrms_leave_management_is_rotation_leave');?><!--Is rotation leave--></label>
                            <div class="col-sm-1 " style="margin-top: 7px">
                                <input type="checkbox" id="isRotationLeave" name="isRotationLeave" value="1" onclick="changeRotationLeave()">
                            </div>
                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Max Consecutive Days</label>
                            <div class="col-sm-6">
                                <input type="number" name="maxConsecetiveDays" step="any" class="form-control" id="maxConsecetiveDays">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Accrual After Months</label>
                            <div class="col-sm-6">
                                <input type="number" name="accrualAfterMonth" step="any" class="form-control" id="accrualAfterMonth">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Provision Months</label>
                            <div class="col-sm-6">
                                <input type="number" name="provisionAfterMonth" step="any" class="form-control" id="provisionAfterMonth">
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


    <script type="text/javascript">
        $(document).ready(function () {
            //called when key is pressed in textbox
            $(".number").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    //display error message

                    return false;
                }
            });
        });
        $(function () {
            $( ".limit" ).change(function() {
                var max = parseInt($(this).attr('max'));
                var min = parseInt($(this).attr('min'));
                if ($(this).val() > max)
                {
                    $(this).val(max);
                }
                else if ($(this).val() < min)
                {
                    $(this).val(min);
                }
            });
        });
        var masterID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        $(document).ready(function () {

            if (masterID) {
                $('[href=#step2]').tab('show');
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $('[href=#step2]').removeClass('btn-default');
                $('[href=#step2]').addClass('btn-primary');
                $('.btn-wizard').removeClass('disabled');
                // load_journal_entry_header();
                getleaveGroupheader();
                leaveGroupdetails();


            }
            else {
                $('.btn-wizard').addClass('disabled');
            }

            $('.headerclose').click(function () {

                fetchPage('system/hrm/leave_group', '', 'HRMS');
            });
            $('.select2').select2();

            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function (ev) {
                setinitlainterstpaymentDate();
                $('#Journal_entry_form').bootstrapValidator('revalidateField', 'documentDate');
                $(this).datepicker('hide');
            });
            $('#facilityDateFrom').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function (ev) {
                getfacilityfatefrom();
                $('#Journal_entry_form').bootstrapValidator('revalidateField', 'facilityDateFrom');
                $(this).datepicker('hide');
            });
            $('#facilityDateTo').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function (ev) {
                $('#Journal_entry_form').bootstrapValidator('revalidateField', 'facilityDateTo');
                $(this).datepicker('hide');
            });
            $('#DateofInterestPayment').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function (ev) {
                $('#Journal_entry_form').bootstrapValidator('revalidateField', 'DateofInterestPayment');
                $(this).datepicker('hide');
            });



            function loanUtlizationTable() {
                var Otable3 = $('#loanUtlizationTable').DataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "bDestroy": true,
                    "bStateSave": true,
                    "sAjaxSource": "<?php echo site_url('Bank_rec/bank'); ?>",
                    "aaSorting": [[1, 'desc']],
                    "fnInitComplete": function () {

                    },
                    "fnDrawCallback": function (oSettings) {


                    },
                    "aoColumns": [
                        {"mData": "date"},
                        {"mData": "principleAmount"},
                        {"mData": "principalRepayment"},
                        {"mData": "closingBalance"},
                        {"mData": "installmentDueDays"},
                        {"mData": "interestAmount"},
                        {"mData": "variableLibor"},
                        {"mData": "variableAmount"},
                        {"mData": "variableTotal"}

                    ],
                    //"columnDefs": [{"targets": [2], "orderable": false}],
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                        aoData.push({"name": "masterID", "value": masterID});
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

            $('#jv_detail_form').bootstrapValidator({

                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    leaveTypeID: {validators: {notEmpty: {message: 'Leave Type is required.'}}}
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
                        stopLoad();
                        refreshNotifications(true);
                        if (data['error'] == 0) {
                            leaveGroupdetails();
                            $('#leaveTypeID').val('').change();
                            $('#jv_detail_form').bootstrapValidator('resetForm', true);
                            $('#leavepolicyID').val('1').change();

                        }else{
                            stopLoad();
                            myAlert('e',data[1]);
                        }


                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });

            $('#Journal_entry_form').bootstrapValidator({

                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                    isHourly: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_type_is_required');?>.'}}},/*Type is required*/

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
                    url: "<?php echo site_url('Employee/save_leaveGroup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {

                            $('.btn-wizard').removeClass('disabled');
                            masterID = data['leaveGroupID'];

                            if (masterID) {
                                $('[href=#step2]').tab('show');
                                $('a[data-toggle="tab"]').removeClass('btn-primary');
                                $('a[data-toggle="tab"]').addClass('btn-default');
                                $('[href=#step2]').removeClass('btn-default');
                                $('[href=#step2]').addClass('btn-primary');
                                leaveGroupdetails()
                            }
                        }

                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
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

        function leaveGroupdetails() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Employee/LeavegroupDetails'); ?>",
                data: {masterID: masterID},
                dataType: "html",
                cache: false,

                beforeSend: function () {
                    startLoad();
                },

                success: function (data) {
                    stopLoad();
                    $("#divSettlementTable").html(data);

                }
            });
            return false;
        }

        function deleteLeavedeltails(id) {
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
                        data: {'leaveGroupDetailID': id},
                        url: "<?php echo site_url('Employee/deleteLeavedeltails'); ?>",
                        beforeSend: function () {
                            //startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            myAlert('s', 'Successfully deleted');
                            $('#row_' + id).remove();

                            //  stopLoad();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function leavepolicy() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Employee/refresh_policy'); ?>",
                data: {masterID: masterID},
                dataType: "html",
                cache: false,
                beforeSend: function () {
                },
                success: function (data) {
                    $("#leavepolicy").html(data);

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
                    url: "<?php echo site_url('Employee/getleaveGroupheader'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {


                            $("#description").val(data['description']);
                            $("#isMonthly").val(data['isMonthly']).change();


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

        function policyValidation(policyID){
            $('#isCarryForward').prop('checked', false);
            $('#carryForwardLimit').val('');
            //$('#jv_detail_form').bootstrapValidator('resetField', 'carryForwardLimit');
            $('.carry-forward').hide();
            if(policyID==2){
                $('.dayForm').addClass('hide');
                //$('.hourform').removeClass('hide');
            }else{
                $('.dayForm').removeClass('hide');
                //$('.hourform').addClass('hide');

                if(policyID==1 || policyID==3){
                    $('.carry-forward').show();
                    $('.carry-forward-limit').hide();
                }
            }
        }
        function modalleaveDetail(policyID) {
            if (masterID) {
                $('#leaveTypeID').val('').change();
                $('#jv_detail_form')[0].reset();
                $('#jv_detail_form').bootstrapValidator('resetForm', true);
            /*    leavepolicy();*/
                $("#jv_detail_modal").modal({backdrop: "static"});
                $('#leavepolicyID').val(1);

                $('.dayForm').removeClass('hide');
                //$('.hourform').addClass('hide');
                $('#isCarryForward').prop('checked', false);
                $('#carryForwardLimit').val('');
                $('#carryForwardLimit').prop('readonly',true);
                //$('#jv_detail_form').bootstrapValidator('resetField', 'carryForwardLimit');
                $('.carry-forward').show();
                $('.carry-forward-limit').hide();
                $('#rotation-days-section').addClass('hide');
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

        function changeCarryForward(){
            $('#carryForwardLimit').val('');
            //$('#jv_detail_form').bootstrapValidator('resetField', 'carryForwardLimit');
            $('.carry-forward-limit').hide();
            if( $('#isCarryForward').prop('checked') ){
                $('.carry-forward-limit').show();
            }
        }

        function changeRotationLeave(){
            if( $('#isRotationLeave').prop('checked') ){
                $('#rotation-days-section').removeClass('hide');
            }else{
                $('#rotation-days-section').addClass('hide');
            }
        }

        function validate_noofdays(det) {
            var noOfDays = $('#noOfDays').val();
                if(det.value >noOfDays)
                {
                    /*myAlert('w', 'You can not enter Carry forward days greater than No of Days');
                    $('#carryForwardLimit').val('');*/
                }
        }

        function changeCarryForwardMax(){
            if( $('#maxCarryForward').prop('checked') ){
                $('#carryForwardLimit').attr('readonly',false);
            }else{
                $('#carryForwardLimit').attr('readonly',true);
            }
        }

    </script>