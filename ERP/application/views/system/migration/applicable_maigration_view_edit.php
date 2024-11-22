<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_others_master', $primaryLanguage);
$date_format_policy = date_format_policy();
echo head_page($this->input->post('page_name'), false); ?>

<div id="filter-panel" class="collapse filter-panel"></div>

    <div class="row">
        <?php echo form_open('', 'role="form" class="" id="migrationReview_form" autocomplete="off"'); ?>

        <div class="row">
            <div class="table-responsive">
                <table id="partNumber_tablee" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">Secondary Code</th>
                            <th style="min-width: 10%">Title</th>
                            <th style="min-width: 10%">Calling Name</th>
                            <th style="min-width: 10%">Name Initials</th>
                            <th style="min-width: 10%">Full Name</th>
                            <th style="min-width: 10%">Surname</th>
                            <th style="min-width: 10%">Gender</th>
                            <th style="min-width: 10%">Nationality</th>
                            <th style="min-width: 10%">Religion</th>
                            <th style="min-width: 10%">Marital Status</th>
                            <th style="min-width: 10%">Date of Birth</th>
                            <th style="min-width: 10%">BloodGroup</th>

                            <th style="min-width: 10%">Primary E-Mail</th>
                            <th style="min-width: 10%">NIC No</th>
                            <th style="min-width: 10%">Confirmed date</th>

                            <th style="min-width: 10%">Address Line1</th>
                            <th style="min-width: 10%">Address Line2</th>
                            <th style="min-width: 10%">Address Line3</th>
                            <th style="min-width: 10%">Country</th>
                            <th style="min-width: 10%">Personal Email</th>
                            <th style="min-width: 10%">Mobile Number</th>

                            <th style="min-width: 10%">Date Joined</th>
                            <th style="min-width: 10%">Date Assumed</th>
                            <th style="min-width: 10%">Type</th>
                            <th style="min-width: 10%">Currency</th>
                            <th style="min-width: 10%">Segment</th>

                            <th style="min-width: 10%">Probation End date</th>
                            <th style="min-width: 10%">Reporting Manager</th>
                            <th style="min-width: 10%">is Payroll Employee</th>
                            <th style="min-width: 10%">Passport No</th>
                            <th style="min-width: 10%">Passport Expiry Date</th>
                            <th style="min-width: 10%">Visa Expiry Date</th>
                            <th style="min-width: 10%">Airport Destination</th>
                            <th style="min-width: 10%">Designation</th>
                            <th style="min-width: 10%">Designation Start Date</th>
                            <th style="min-width: 10%">Department</th>
                            <th style="min-width: 10%">Account Holder Name</th>
                            <th style="min-width: 10%">Bank</th>
                            <th style="min-width: 10%">Branch</th>
                            <th style="min-width: 10%">Account No</th>
                            <th style="min-width: 10%">Salary Transfer %</th>
                            <th style="min-width: 10%">Payroll Type</th>
                            <th style="min-width: 10%">Leave Group</th>
                            <td >&nbsp;</td>
                            
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
<div class="row">
   
    <div class="col-sm-12 form-inline">
        <div class="form-group">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-success-new size-sm"
                    onclick="post_validated_excel_data()"> Post Data<!--Search-->
            </button>
        </div>

        <div class="form-group hide" id="validate_data">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-primary-new size-sm"
                    onclick="validated_excel_data()"> Validate Data
            </button>
        </div>

        <div class="form-group hide" id="error_view">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-primary-new size-sm"
                    onclick="view_validated_errors()"> View Errors
            </button>
        </div>
     
    </div>
   
    
</div>



<?php

echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="item_part_number_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="itemPartNumberModelHeader">Errors</h3>
            </div>
            
                <div class="modal-body p-5">
                  
                    <div class="row">
                        <div class="table-responsive">
                            <table id="partNumber_table" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 10%">Excel LineID</th>
                                        <th style="min-width: 20%">Temp Coloumn Name</th>
                                        <th style="min-width: 11%">Error Message</th>
                                        
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                  
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_close')?><!--Close--></button>
                        <!-- <button onclick="save_partNumber_details()" class="btn btn-primary">Save</button> -->
                    </div>
            
        </div>
    </div>
</div>

<script type="text/javascript">
     var empBankTbl = $('#empBankTB');
    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/migration/load_applicable_document','','Load Document');
        });
        fetch_header_migration_details();
      //  fetch_migration_submission();
        fetch_excel_upload_migration_details();
        
    });

    function loadSelectOptionDrop(){
       // $('#select_hod_emp').select2();
       var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.select2').select2();

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });
    }


    function fetch_excel_upload_migration_details() {
        setTimeout(loadSelectOptionDrop, 500);
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        var Otable = $('#partNumber_tablee').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('MigrationDocument/fetch_excel_upload_migration_details_edit'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "EmpSecondaryCode"},
                {"mData": "emp_title"},
                {"mData": "shortName"},
                {"mData": "initial"},
                {"mData": "fullName"},
                {"mData": "Ename3"},
                {"mData": "emp_gender"},

                {"mData": "Nationality"},
                {"mData": "religion"},
                {"mData": "MaritialStatus"},
                {"mData": "empDob"},
                {"mData": "BloodGroup"},
                {"mData": "emp_email"},
                {"mData": "NIC"},
                {"mData": "confirmDate"},

                {"mData": "ep_address1"},
                {"mData": "ep_address2"},
                {"mData": "ep_address3"},
                {"mData": "ep_address4"},
                {"mData": "personalEmail"},
                {"mData": "emp_mobile"},

                {"mData": "empDoj"},
                {"mData": "dateAssumed"},
                {"mData": "employeeConType"},
                {"mData": "empCurrency"},
                {"mData": "empSegment"},

                {"mData": "probationPeriod"},
                {"mData": "managerID"},
                
                {"mData": "isPayrollEmployee"},
                {"mData": "pass_portNo"},
                {"mData": "passPort_expiryDate"},
                {"mData": "visa_expiryDate"},
                {"mData": "airport_destination"},
                {"mData": "designationID"},
                {"mData": "startDate"},
                {"mData": "items"},
                {"mData": "accHolder"},
                {"mData": "bank"},
                {"mData": "branch"},

                {"mData": "accountNo"},
                {"mData": "salPerc"},
                {"mData": "payrollType"},
                {"mData": "leaveGroupID"},
                {"mData": "id"},
                
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "migrationHeaderMasterID", "value": p_id});
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

    // function fetch_excel_upload_migration_details(){

    //    // var formData = new FormData($("#excelUpload_form")[0]);
    //    p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

    //    if(p_id){

    //         $.ajax({
    //             async: true,
    //             type: 'post',
    //             dataType: 'json',
    //             data: {migrationHeaderMasterID: p_id},
    //             url: "<?php echo site_url('MigrationDocument/fetch_excel_upload_migration_details_edit'); ?>",
    //             beforeSend: function () {

    //             },
    //             success: function (data) {
    //                // $('#loaduserGroupdropdown').html(data);
    //              //  $('#loaduserGroupdropdown').html(data);

    //             }, error: function () {

    //             }
    //         });

    //    }
        
    // }

    function fetch_header_migration_details(){
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {migrationHeaderMasterID: p_id},
                url: "<?php echo site_url('MigrationDocument/fetch_header_migration_details'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if(data.isValidated==0){
                        $('#post_data').addClass('hide');
                        $('#validate_data').removeClass('hide');
                    }else{
                        $('#post_data').removeClass('hide');
                       $('#validate_data').addClass('hide');
                    }

                }, error: function () {

                }
            });

        }
        
    }

    function validated_excel_data(){
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {migrationHeaderMasterID: p_id},
                url: "<?php echo site_url('MigrationDocument/validated_excel_data'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if(data.length==0){
                        myAlert('s', "validate successfully");
                        fetchPage('system/migration/load_applicable_document','','Load Document');
                    }else{
                        // $.each(data, function (i, v) {
                        //     myAlert('e', v.Errormessage + ' At Line Number '+ v.excelLineID+ ' And Column Name -: '+ v.tempColoumnName);

                        // });
                        myAlert('e', "validation fail Please view errors");
                        $('#error_view').removeClass('hide');
                    }

                }, error: function () {

                }
            });

        }
        
    }

    function view_validated_errors(){
     
        $('#item_part_number_model').modal('show');
        fetch_view_validated_errors();
    }

    function fetch_view_validated_errors() {
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        var Otable = $('#partNumber_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('MigrationDocument/fetch_view_validated_errors'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "autoID"},
                {"mData": "excelLineID"},
                {"mData": "tempColoumnName"},
                {"mData": "Errormessage"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "migrationHeaderMasterID", "value": p_id});
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

    function fetch_banck_brach_em_migration(id){

        var bank_id =$('#bank_'+id).val();

        if(bank_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'bankID': bank_id},
                url: "<?php echo site_url('MigrationDocument/fetchbankBranches'); ?>",
                success: function (data) {
                    $('#branch_'+id).empty();
                    var mySelect = $('#branch_'+id);
                    mySelect.append($('<option></option>').val('').html('<?php echo "Select Branch"?>'));
                    /*Select batch*/
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['branchID']).html(text['branchCode']+' | '+text['branchName']));
                        });
                        
                    }
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });

        }
    }

    function post_validated_excel_data(){

        var data_submit = $('#migrationReview_form').serializeArray();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data_submit,
                url: "<?php echo site_url('MigrationDocument/post_validated_edit_em_excel_data'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                   $.each(data, function (i, v) {

                        var header_post_url = v.header_post_url;
                        var details_arr = v.emp_details;
                        
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: v,
                            url: header_post_url,
                            beforeSend: function () {

                            },
                            success: function (data1) {
                                refreshNotifications(true);
                                myAlert(data1[0], data1[1]);
                                if (data1[0]=='s') {
                                    var empAutoID = data1[2];
                                    
                                    $.each(details_arr, function (i, val) {

                                        var url_d=val.details_url;
                                        val['empID']=empAutoID;
                                        val['updateID']=empAutoID;

                                        if(val['details_url']=='Employee/save_employmentData_envoy'){
                                            url_d=val.details_url+'/?empID='+empAutoID;
                                        }

                                        if(val['details_url']=='Employee/save_attendanceData'){
                                            url_d=val.details_url+'/?empID='+empAutoID;
                                        }

                                        $.ajax({
                                            async: true,
                                            type: 'post',
                                            dataType: 'json',
                                            data: val,
                                            url: url_d,
                                            beforeSend: function () {

                                            },
                                            success: function (data2) {
                                                refreshNotifications(true);

                                            }, error: function () {

                                            }
                                        });
                                    });

                                }

                            }, error: function () {

                            }
                        });

                        
                        
                       
                    });

                }, error: function () {

                }
            });

       }
    }

    function send_details(v,url1){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: v,
            url: url1,
            beforeSend: function () {

            },
            success: function (data2) {
                refreshNotifications(true);

            }, error: function () {

            }
        });
    }

    /////////////////////////////////////////////////

</script>