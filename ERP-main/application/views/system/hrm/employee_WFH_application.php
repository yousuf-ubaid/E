<!--Translation added by Naseek-->


<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = 'Work from Home Application';
echo head_page($title, true);

//$employee_arr = all_employee_drop(false);
//$leaveTypes   = leaveTypes_drop();
$current_date = format_date($this->common_data['current_date']);
//$employeeDrop = leaveApplicationEmployee();
$employeeDrop = all_employeeDrop_for_wfh();
$date_format_policy = date_format_policy();
$current_date_filter = convert_date_format(date('Y-01-01'));
$current_date_filter2 = convert_date_format(date('Y-12-31'));
$designations_arr = employee_designation_for_wfh();

$designation = get_employee_designation_for_wfh();

// echo '<pre>';
// print_r($designation); exit;

$filterStatus = [
    'all' =>$this->lang->line('common_all') /*'All'*/,
    'draft' =>$this->lang->line('common_draft')/* 'Draft'*/,
    'confirmed' =>$this->lang->line('common_confirmed') /*'Confirmed'*/,
    'approved' =>$this->lang->line('common_approved') /*'Approved'*/,
    //'canReq' =>$this->lang->line('common_canceled_req') /*'Cancellation Request'*/,
    //'canApp' =>$this->lang->line('common_canceled') /*'Canceled'*/
];

?>
<style type="text/css">
    .cancel-pop-up:hover{ cursor: pointer; }

    .panel-body {
        margin-bottom: 20px;
        background-color: #ffffff;
        border: 1px solid #dddddd;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    }

    .overlay {
        z-index: 50;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 3px;
    }

    .panel-body > .overlay {
        position: relative;
    / / top: 5 px;
    / / left: 5 px;
        width: 100%;
        height: 100%;
    }

    .myOverlay-spin {
        color: #FFFFFF;
        position: relative;
        left: 50%;
        margin-top: 0px;
        margin-bottom: 0px;
        font-size: 20px;
    }

    /* Testimonials */
    .testimonials blockquote {
        background: #f8f8f8 none repeat scroll 0 0;
        border: medium none;
        color: #666;
        display: block;
        font-size: 14px;
        line-height: 20px;
        padding: 15px;
        position: relative;
    }

    .testimonials blockquote::before {
        width: 0;
        height: 0;
        right: 0;
        bottom: 0;
        content: " ";
        display: block;
        position: absolute;
        border-bottom: 20px solid #fff;
        border-right: 0 solid transparent;
        border-left: 15px solid transparent;
        border-left-style: inset; /*FF fixes*/
        border-bottom-style: inset; /*FF fixes*/
    }

    .testimonials blockquote::after {
        width: 0;
        height: 0;
        right: 0;
        bottom: 0;
        content: " ";
        display: block;
        position: absolute;
        border-style: solid;
        border-width: 20px 20px 0 0;
        border-color: #e63f0c transparent transparent transparent;
    }

    .testimonials .carousel-info img {
        border: 1px solid #f5f5f5;
        border-radius: 150px !important;
        height: 75px;
        padding: 3px;
        width: 75px;
    }

    .testimonials .carousel-info {
        overflow: hidden;
    }

    .testimonials .carousel-info img {
        margin-right: 15px;
    }

    .testimonials .carousel-info span {
        display: block;
    }

    .testimonials span.testimonials-name {
        color: #e6400c;
        font-size: 16px;
        font-weight: 300;
        margin: 23px 0 7px;
    }

    .testimonials span.testimonials-post {
        color: #656565;
        font-size: 11px;
    }

    #menu ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;

    }

    #menu li {
        float: left;
    }

    #menu li div {
        display: block;
        color: black;
        text-align: center;

        text-decoration: none;
        border: 1px solid #efefef;
    }

    #menu li a:hover {
        cursor: pointer;
    }

    .shadow-box {
        border: 2px solid #ccc;
        padding: 10px;
        background-color: #fff; /* Change background color to white */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Set shadow underneath */
    }

</style>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?><!--Date--></label><br>
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?><!--From--></label>
                <input type="text" name="filterDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" value="<?php echo $current_date_filter ?>"
                       data-int="<?php echo $current_date_filter ?>" id="filterDateFrom" class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?><!--To-->&nbsp&nbsp</label>
                <input type="text" name="filterDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" value="<?php echo $current_date_filter2 ?>"
                       data-int="<?php echo $current_date_filter2 ?>" id="filterDateTo" class="input-small">
            </div>

        </div>
        <div class="form-group col-sm-4">
            <label for="empFilter"><?php echo $this->lang->line('hrms_leave_management_employee_name');?> <!--Employee Name--></label><br>
            <?php echo form_dropdown('empFilter[]', $employeeDrop, current_userID(), 'class="form-control select2 empFilter" id="empFilter"'); ?>
            <!-- <select name="empFilter[]" class="form-control" id="empFilter" multiple="multiple" disabled>
                <?php/*

                foreach ($employeeDrop as $empD){
                    $selected = ($empD['EIdNo'] == current_userID())? 'selected' : '';
                    echo '<option value="'.$empD['EIdNo'].'" '.$selected.'>'.$empD['ECode'].' - '.$empD['employee'].'</option>';
                }
                */?>
            </select> -->
        </div>
        <div class="form-group col-sm-4">
            <label for="status"><?php echo $this->lang->line('common_status');?><!--Status--></label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', $filterStatus, '', 'class="form-control" id="status"'); ?></div>
            <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()" style="margin-top: -10%;">
                <i class="fa fa-times-circle-o"></i>
            </button>
            <button type="button" class="btn btn-primary pull-right" onclick="search_WFH_application()" style="margin-top: -9%; margin-left: 250px; position: absolute;">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</div>

<!-- <div class="row">
    <div class="col-md-12" style="margin-bottom: 10px" id="divBalance"></div>
</div> -->

<div class="row">
    <div class="col-md-7">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody>
            <tr>
                <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                  <?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed-->
                  <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                  <?php echo $this->lang->line('common_not_confirmed'); ?><!--Not Confirmed-->
                    <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved-->
                </td>
                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                  <?php echo $this->lang->line('common_refer_back'); ?><!--Refer-back-->
                </td>
                <td><span class="label label-info">&nbsp;</span> <?php echo $this->lang->line('common_canceled');?><!--Canceled--> </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-3 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="newWFH_Apply_modal()">
            <i class="fa fa-plus"></i>
          Apply
        </button>
    </div>
</div>
<hr>


<div class="table-responsive">
    <table id="WFHDetailTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 4%;text-align:left">#</th>
            <th style="min-width: 15%;text-align:left"><?php echo $this->lang->line('hrms_leave_management_document_code'); ?><!--Document Code--></th>
            <th style="min-width: 20%;text-align:left"> <?php echo $this->lang->line('hrms_leave_management_employee_name'); ?><!--Employee Name--></th>
            <th style="min-width: 16%;text-align:left">Created Date</th>
            <th style="min-width: 10%;text-align:left"><?php echo $this->lang->line('common_from'); ?><!--From--></th>
            <th style="min-width: 11%;text-align:left"><?php echo $this->lang->line('common_to'); ?><!--To--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?><!--Approved--></th>
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>



<div class="modal fade" id="newWFH_Apply_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="wfhCode"></span></h4>
            </div>

            <div class="modal-body">
                <div class="panel-body">
                
                <form id="emp_new_WFH_application" class="form-inline" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="wfhID" id="wfhID" value="">

                    <div class="row" style="margin-top: 10px;margin-right:5px;">
                        <div class="col-xs-4 col-sm-2">
                            <label>Application Type</label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-4">
                            <span id="applicationTypeSpan" class="form-control">WFH</span>
                        </div>

                        <div class="col-xs-4 col-sm-2"><label>
                            Application Date</label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-4">
                            <span id="entryDateSpan" class="form-control"><?php echo $current_date ?></span>
                            <input type="hidden" name="entryDate" id="entryDate" data-value="<?php echo $current_date ?>">
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;margin-right:5px;">             
                                <div class="col-xs-4 col-sm-2">
                                    <label>Employee Name<?php required_mark(); ?><!--Employee Name--></label>
                                </div>
                                <div class="shadow-box col-xs-7 col-sm-4">
                                    <span id="empNamespan"><?php echo current_employee(); ?></span>
                                    <input type="hidden" name="empID" class="form-control col-sm-4" id="empID" value="<?php echo current_userID(); ?>">
                                </div>
                                   
                        <div class="col-xs-4 col-sm-2">
                            <label>Employee Code<!--Employee Code--></label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-4">
                            <?php 
                            $empID = current_userID();
                            $empCode = explode(' | ',$employeeDrop[$empID]); ?>
                            <span id="empCodespan"><?php echo $empCode[0]; ?></span>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;margin-right:5px;">
                        <div class="col-xs-4 col-sm-2"><label>
                            <?php echo $this->lang->line('common_designation'); ?><!--Designation--></label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-4">
                            <?php $designation = get_employee_designation_for_wfh(); ?>
                            <span id= "designationspan" class="form-control"><?php echo isset($designations_arr[$designation])? $designations_arr[$designation]: '';?></span>
                        </div>
                        <div class="col-xs-4 col-sm-6"></div>
                    </div>

                    <div class="row" style="margin-top: 10px;margin-right:5px;">
                        <div class="col-xs-4 col-sm-2">
                            <label for="">From Date<?php required_mark(); ?></label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-4">
                            <div class="input-group datepic col-xs-7 col-sm-12">
                                <div class="input-group-addon col-xs-4 col-sm-2" style="display: flex; justify-content: center; align-items: center;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="col-xs-4 col-sm-10">
                                    <input type="text" name="WFHStartDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="WFHStartDate"
                                    class="form-control" required>
                                </div>  
                            </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-2">
                            <label for="">To Date<?php required_mark(); ?></label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-4">
                            <div class="input-group datepic col-xs-7 col-sm-12">
                                <div class="input-group-addon col-xs-4 col-sm-2" style="display: flex; justify-content: center; align-items: center;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="col-xs-4 col-sm-10">
                                    <input type="text" name="WFHEndDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="WFHEndDate"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="row" style="margin-top: 10px;margin-right:5px;">
                        <div class="col-xs-4 col-sm-2">
                            <label for="">Comment</label>
                        </div>
                        <div class="shadow-box col-xs-7 col-sm-10">
                            <textarea class="form-control" rows="1" id="comment" name="comment"></textarea>
                        </div>
                    </div>
                

                    <div class="row" style="margin-top: 15px">
                        <div class="col-xs-4 col-sm-2">
                            <label>Attachments</label>
                        </div>
                        <div class="col-xs-7 col-sm-10">
                            <!-- <a onclick=open_attachment_modal()><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn"></span></a>  -->
                            <input type="file" name="doc_file" class="form-control" id="up_doc_file" placeholder="Brows Here">
                            <a class="btn btn-default" id="remove_document" data-dismiss="doc_file" onclick="remove_document()">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </a>
                            <input type="text" id="data_field" name="data_field" placeholder="Enter description" />
                        </div>
                    <div>
                </form>     
                </div>
            </div>

        </div>
            <div class="modal-footer">
                <div class="text-right m-t-xs">
                    <button onclick="" type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                    <button class="btn btn-primary save" onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
                    <button class="btn btn-success submitWizard" onclick="check_confirmation()"><?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var wfhID;
    var wfhmasterId;
    toastr.clear();
    var masterTable;
    var new_WFH_modal = $('#newWFH_Apply_modal');
    var submitBtn = $('.submitBtn');
    var submitConfirmBtn = $('.submit_confirmBtn');
    var updateBtn = $('.updateBtn');
    var updateConfirmBtn = $('.update_confirmBtn');
    var confirmButton = $('.confirmBtn');
    Inputmask().mask(document.querySelectorAll("input"));

    $(document).ready(function() {
        /*Filter panel expand in page load*/
        $('#filter-panel').addClass('in');

       // var cancelID = $('#cancelID').val();

        $('#empFilter').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            numberDisplayed: 1,
            buttonWidth: '180px'
        });
     
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });
      
        get_WFH_tableView();
    });
    
    $('.headerclose').click(function () {
        fetchPage('system/hrm/employee_WFH_application', wfhID, 'WFH Request');
    });


    function get_WFH_tableView(){
        masterTable = $('#WFHDetailTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_WFH_Details'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseFloat('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseFloat(oSettings.aoData[x]._aData['wfhID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "wfhID"},
                {"mData": "documentCode"},
                {"mData": "empName"},
                {"mData": "documentDate"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "confirm"},
                {"mData": "approved"},
                {"mData": "action"}
            ],
            "columnDefs": [{"targets": [0,6,7,8], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name:'filterDateFrom', value: $('#filterDateFrom').val()});
                aoData.push({name:'filterDateTo', value: $('#filterDateTo').val()});
                aoData.push({"name": "empFilter[]", "value": <?php echo current_userID() ?>});
                aoData.push({name:'status', value: $('#status').val()});
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


    function save_draft(){
        //return new Promise((resolve, reject) => {
            var data = new FormData($("#emp_new_WFH_application")[0]);
            //var data = $("#emp_new_WFH_application").serializeArray();  //wfhID
            //data.push({'name': '', 'value': $('#wfh_employeeCode option:selected').val()});
            //data.push({'name': 'comment', 'value': $('#').prop('checked') ? 1 : 0});
            //data.append({'name': 'comment', 'value': $('#comment').val()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_WFH_employee_applivation'); ?>",
                processData: false,  // Important! Prevent jQuery from processing the data
                contentType: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        wfhmasterId = data[2];
                        $('#data_field').val('');
                        $('#up_doc_file').val('');
                        new_WFH_modal.modal('hide');
                        get_WFH_tableView();

                        //resolve(data);  // Resolve the Promise with the response data
                    }
                    //else {
                        //reject(data);  // Reject the Promise if the response status is not success
                    //}
                    
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
            
        //});
    }

    function delete_empWFH(wfhID, documentCode) {
        swal(
            {
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
                    url: "<?php echo site_url('Employee/delete_empWFH'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'deleteID': wfhID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            //get_WFH_tableView();
                            setTimeout(function () {
                                masterTable.ajax.reload();
                            }, 200);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

 
 function edit_WFH_document(id, code) {
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/fetch_employee_and_WFH_details'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'wfhID': id},
            beforeSend: function () {
                startLoad();
                //wfh_EditForm();
            },
            success: function (data) {
                stopLoad();
                var empDet = data['empDet'];
                var wfhDetails = data['wfhDetails'];

                $('#empID').val(empDet['EIdNo']); 

                var designation = empDet['DesDescription'];
                $('#designationspan').html('');
                $('#designationspan').html(designation);

                $('#entryDateSpan').html('');
                $('#entryDateSpan').html(wfhDetails['documentDate']);
                $('#entryDate').val(wfhDetails['documentDate']);

                setTimeout(function () {
                    $('#comment').val(wfhDetails['comments']);
                    $('#WFHStartDate').val(wfhDetails['startDate']);
                    $('#WFHEndDate').val(wfhDetails['endDate']);
                    $('#wfhCode').html('');
                    $('#wfhCode').html( 'Edit' + ' ' + code + ' ' + 'Application');
                    $('#wfhID').val(id);
                    $('.save').text('update');
                }, 1200);
                new_WFH_modal.modal({backdrop: 'static'});
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }


    function newWFH_Apply_modal() {
        var formInputs = $('#emp_new_WFH_application input, #emp_new_WFH_application select, #emp_new_WFH_application textarea');
        formInputs.prop('value', '');
        formInputs.prop('disabled', false);

        //wfh_apply_employee_details();

        $('applicationType').val('WFH');
        $('#wfhCode').text( 'Create Work From Home Application');
        $('#WFHStartDate').val('');
        $('#WFHStartDate').val('');
       
        var d = new Date();
        var currDate = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
        $('#entryDateSpan').html(currDate);
        $('#entryDate').val(currDate);
       
        //$('#designationSpan').html('');
        $('#empID').val('<?php echo current_userID(); ?>');

        $('.save').text('');
        $('.save').text('Save as Draft');

        new_WFH_modal.modal({backdrop: 'static'});
    }

    function remove_document(){
        $('#up_doc_file').val('');
    }


    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function search_WFH_application(){
        var filterDateFrom = $('#filterDateFrom').val();
        var filterDateTo = $('#filterDateTo').val();

        var txt = '';
        if( isDateInputMaskNotComplete(filterDateFrom) ){
            txt = 'Date from is incomplete<br/>';
        }

        if( isDateInputMaskNotComplete(filterDateTo) ){
            txt += 'Date to is incomplete';
        }

        if(txt != ''){
            myAlert('e', txt);
            return false;
        }

        masterTable.draw()
    }


    function clear_all_filters(){
        var filterDateFrom = $('#filterDateFrom').attr('data-int');
        var filterDateTo = $('#filterDateTo').attr('data-int');

        $('#filterDateFrom').val(filterDateFrom);
        $('#filterDateTo').val(filterDateTo);

        $('#status').val('all');

        setTimeout(function(){
            search_WFH_application();
        }, 150);
    }

    function check_confirmation() {
        swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
                },
                function () {
                    var wfh_id = $('#wfhID').val();
                    if(wfh_id){
                        confirmation(wfh_id);
                    }else{
                        save_before_confirm();
                    }
                }
        );
    }

    function confirmation(wfh_id) {
        $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'wfhmasterId': wfh_id},
                        url: "<?php echo site_url('Employee/confirmation_WFH_document'); ?>",
                        beforeSend: function () {
                            //save_draft();
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if(data[0] == 's'){
                                new_WFH_modal.modal('hide');
                                setTimeout(function () {
                                    masterTable.ajax.reload();
                                }, 200);
                               // fetchPage('system/hrm/employee_WFH_application', wfhmasterId, 'Work From Home');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
          
    }

    function save_before_confirm(){
        var data = new FormData($("#emp_new_WFH_application")[0]);
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_WFH_employee_applivation'); ?>",
                processData: false,
                contentType: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        wfhmasterId = data[2];
                        new_WFH_modal.modal('hide');
                        confirmation(wfhmasterId);
                    } 
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
    }

    function refer_back_confirmation(refID, des) {
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?> [ " + des + " ]!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_refer_back');?> ",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/refer_back_WFH_confirmation'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'refID': refID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            setTimeout(function () {
                                fetchPage('system/hrm/employee_WFH_application', refID, 'HRMS')
                            }, 300);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function document_uplode() {
        var formData = new FormData($("#attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    attachment_modal($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }


    function open_attachment_modal() {
        $('#attachmentDescription').val('');
        $('#documentSystemCode').val('');
        $('#document_name').val('');
        $('#documentID').val('WFH');
        $('#confirmYNadd').val('');
        $('#remove_id').click();
        $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + 'Work From Home Attachments' + "");
        $('#attachment_modal_body').empty();
       // $('#attachment_modal_body').append('' + data + '');
        $("#attachment_modal").modal({backdrop: "static", keyboard: true});
               
    }


</script>

