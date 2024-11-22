<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_request_note');
echo head_page($title, false);

$segment_arr = fetch_segment();
$segment_arr_detail = fetch_segment(true);
$employee_arr = all_employee_drop(false);
$umo_arr = array('' => 'Select UOM');//all_umo_drop();
$current_date = $running_date = format_date($this->common_data['current_date']);
$uomList = all_umo_new_drop();
$projectcode = fetchProjectCode_ioubooking();
?>

<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #request-add-tb td{ padding: 2px; }

    #assetRequestModal .modal-dialog {
    width: 100%;
    margin: auto;
}
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

 <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('common_step'); ?><!--Step--> 1 -
        <?php echo $this->lang->line('assetmanagement_request_note_header'); ?><!--Asset Request Header--></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_arn_table();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('common_step'); ?><!--Step--> 2 -
        <?php echo $this->lang->line('assetmanagement_request_note_details'); ?><!--Asset Request Detail--></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('common_step'); ?><!--Step--> 3 -
       Asset Request Confirmation</span>
        </a> 
    </div>   
    
</div>
<hr>

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="request_form"'); ?>
        <div class="row">
            <div class="col-sm-4" style="padding-right: 10px">
            <div class="form-group">
                    <label for="documentDate">
                        <?php echo $this->lang->line('common_date'); ?><!--Date--> <?php required_mark(); ?></label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="documentDate" value="<?php echo $running_date ?>" id="documentDate" autocomplete="off" class="form-control dateField">
                    </div>
                </div>
                <div class="form-group">
                    <label for="reference">
                    <?php echo $this->lang->line('common_reference'); ?><!--Reference--></label>
                            <div class="input-group">
                            <input type="text" name="reference" value="" id="reference" class="form-control">
                            </div>
                </div>
            </div>
        
            <div class="col-sm-4" style="padding-right: 10px">
                <div class="form-group">
                    <label for="segment">
                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segmentID', $segment_arr, '', 'class="form-control select2" id="segmentID" required'); ?>
        
                </div>
                <div class="form-group">
                    <label for="location">
                    <?php echo $this->lang->line('common_Location'); ?><!--Location--> <?php required_mark(); ?></label>
                            <div class="input-group">
                            <input type="text" name="location" value="" id="location" class="form-control">
                            </div>
                </div>
            </div>  
            <div class="col-sm-4" style="padding-right: 10px">
                <div class="form-group">
                    <label for="requestedByEmpID">
                    <?php echo $this->lang->line('common_requested_by'); ?><!--Requested_by--> <?php required_mark(); ?></label>
               
                     
                        <select name="requestedByEmpID" id="requestedByEmpID" class="form-control" required>
                            <?php
                            foreach ($employee_arr as $item) {
                                echo '<option value="' . $item['EIdNo'] . '">' . $item['ECode'] . ' - ' . $item['Ename2'] . '</option>';
                            }
                            ?>
                        </select>
    
                
                <div class="form-group">
                    <label for="comments">
                    <?php echo $this->lang->line('common_comments'); ?><!--comments--></label>
                            <div class="input-group">
                            <input type="text" name="comments" value="" id="comments" class="form-control">
                            </div>
                </div>
            </div>  
        </div>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
        </form>
        
    </div>
</div>
<div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>
                    <?php echo $this->lang->line('assetmanagement_asset_request_note'); ?><!--Asset Request Note-->
                </h4><h4></h4></div>
            <div class="col-md-4">
                <button type="button" onclick="assetRequestModal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed" id="request-details_tb">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 25%" class="text-left">
                    <?php echo $this->lang->line('common_item_description'); ?><!--item Description--></th>
                    
                    <th style="min-width: 30%"><?php echo $this->lang->line('common_project'); ?><!--Project--></th>
                    <th style="min-width: 25%" class="text-left">
                 
                    <?php echo $this->lang->line('common_qty'); ?><!--QTY--></th>
                
               <th style="min-width: 8%"><?php echo $this->lang->line('common_comments'); ?><!--Comments--></th>
               <th style="min-width: 8%"><?php echo $this->lang->line('common_actions'); ?>Actions</th>
            </tr>
            </thead>
            <tbody id="table_body">
            
            <tr class="danger">
                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
            </tr>
            </tbody>
        </table>
        <br>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary next" onclick="load_conformation();">
                <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="request_note_lable">Modal title</h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="assetRequest_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="assetRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
           
            <form role="form" class="form-horizontal" id="request_detail_form" >
                <div class="modal-body">
                <input type="hidden" id="masterId" name="masterId">
                    <table class="table table-bordered table-striped table-condesed size-lg" id="request-details_add-tb">
                        <thead>
                            <tr>
                            
                                <th style="width: 400px"><?php echo $this->lang->line('common_description');?><!--Description--><?php required_mark(); ?></th>
                                <th style="width: 200px"><?php echo $this->lang->line('common_project');?><!--Project--></th>
                                <th style="width: 80px"><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                                <th style="width: 1000px"><?php echo $this->lang->line('common_requested_qty');?><!--Requested QTY--><?php required_mark(); ?></th>
                                <th style="width: 500px"><?php echo $this->lang->line('common_comments');?><!--Comments--></th>
                                <th style="width: auto">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i class="fa fa-plus"></i></button>
                                </th>  
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="itemdescription[]" class="form-control itemdescription" required></td>
                                <td><?php echo form_dropdown('project[]', $projectcode , 'select project code', 'class="form-control select2" id="projectCode" onchange="" required'); ?>
                                </td>
                                <td style="width : 100px">
                                <?php echo form_dropdown('UOMID[]', $uomList, 'class="form-control select2" id="UOMID"'); ?>
                                
                                </td>
                                <td><input type="text" name="requestedQty[]" class="form-control requestedQty" required></td> 
                                <td><input type="text" name="comments[]" class="form-control comments"></td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveRequestDetails()"><?php echo $this->lang->line('common_save_change');?><!--Save Changes--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="asset_request_detail_edit_modal" class="modal fade">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('profile_edit_item_detail'); ?><!--Edit Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="asset_request_edit_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="arn_detail_edit_table">
                        <thead>
                        <tr>
                                <th style="width: 200px"><?php echo $this->lang->line('common_description');?><!--Description--><?php required_mark(); ?></th>
                                <th style="width: 150px"><?php echo $this->lang->line('common_project');?><!--Project--></th>
                                
                                <th style="width: 80px"><?php echo $this->lang->line('common_requested_qty');?>Requested QTY<?php required_mark(); ?></th>
                                <th style="width: 200px"><?php echo $this->lang->line('common_comments');?><!--Comments--></th>
                       </tr>
                        </thead>
                        <tbody>
                        <tr>


                        <td><input type="text" name="itemDescriptionEdit" class="form-control itemdescription" required></td>
                                <td><?php echo form_dropdown('contractIDEdit', $projectcode , 'select project code', 'class="form-control select2" id="projectCode" onchange="" required'); ?>
                                </td>
                               
                                <td><input type="text" name="requestedQTYEdit" class="form-control requestedQty" required></td> 
                                <td><input type="text" name="commentsEdit" class="form-control comments"></td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="update_asset_request()"><?php echo $this->lang->line('common_update'); ?><!--Update changes-->
                </button>
            </div>
        </div>
    </div>
</div>
    
<?php echo footer_page('Right foot', 'Left foot', false); ?>   
<script type="text/javascript">
    var masterID ;
    var detailsID;

        function assetRequestModal(){
        $('#assetRequestModal').modal({backdrop: "static"});
        }

        $('.headerclose').click(function () {
            fetchPage('system/asset_management/asset_request', id, 'Asset Request');
        });

    
        $(document).ready(function () {
            $('.dateField').datepicker({
                format: 'yyyy-mm-dd' // Set the date format
            }).on('changeDate', function (ev) {
                $(this).datepicker('hide'); // Hide the datepicker when date is changed
                if (this.id == 'documentDate') {
                    $('#request_form').bootstrapValidator('revalidateField', 'documentDate'); // Revalidate the field if it's 'documentDate'
                }
            });


            $('#request_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_request_date');?>.'}}},/*Request Date is required*/
                    segmentID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                    requestedByEmpID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                    location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_asset_location_is_required');?>.'}}},/*Location is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#segmentID").prop("disabled", false);
                $('#requestedByEmpID').prop("disabled",false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                // data.push({'name': 'id', 'value': id});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('AssetManagement/save_request_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (data['status']) {
                            $('.btn-wizard').removeClass('disabled');
                        
                            var id = data['reqId'];
                            var segmentID = data['segmentID'];
                            var requestedByEmpID = data['requestedByEmpID'];
                            // Assuming this is the name you want to save
                            $('#masterId').val(id);
                        masterID = id;
                            load_request_table();
                            fetch_arn_table();
                            $('[href=#step2]').tab('show');
                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, 
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
        });

       

    // Handle tab navigation when the "Next" button is clicked
    $('.next').click(function () {
        $('[href="#step2"]').tab('show');
    });

    // Handle tab navigation when the "Previous" button is clicked
    $('.prev').click(function () {
        $('[href="#step1"]').tab('show');
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

function saveRequestDetails() {
    var data = $('#request_detail_form').serializeArray();
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: data,
        url: "<?php echo site_url('AssetManagement/save_request_details'); ?>",
        beforeSend: function () {
            startLoad();
            
        },
        success: function (data) {
            stopLoad();
            myAlert(data[0], data[1]);
            if (data[0] == 's') {
                // id = data['reqId'];
                // detailsID = null;
                // updateMasterID('newMasterIDValue');
                fetch_arn_table();
                $('#assetRequestModal').modal('hide');
            } else {

                fetch_arn_table();
                // Handle success case here if needed
            }
        },
        error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            stopLoad();
            refreshNotifications(true);
        }
    });
}

function fetch_arn_table() {
 
    if (masterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': masterID},
            url: "<?php echo site_url('AssetManagement/fetch_arn_table');?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
             
                .length);
                $('#table_body').empty(); 
                var x = 1;
                if ($.isEmptyObject(data['detail'])) {
                    $('#table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                } else {

                    $.each(data['detail'], function (key, value) {
                        var projectCode = $('#projectCode').val();
                        // var projectCode = data['projectCode'][value['contractID']];
                        // var uom = $('#UOMID').val();
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemDescription'] + '</td><td>'  + value['contractCode']  + '</td><td>' + value['requestedQTY'] + '</td><td>' + value['comments'] + '</td><td class="text-right"><a onclick="edit_item(' + value['detailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="asset_request_line_delete_item(' + value['detailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        x++;
                    });
                }
                stopLoad();
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
}

function load_conformation() {
   
        if (masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'masterID': masterID, 'html': true},
                url: "<?php echo site_url('AssetManagement/load_asset_request_confirmation');?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                 
                 $('#conform_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                    //attachment_modal_assetReq(masterID, "ARN");
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }    
    function attachment_modal_assetReq(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#request_note_lable').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#assetRequest_attachment').empty();
                    $('#assetRequest_attachment').append('' +data+ '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function confirmation() {
        if (masterID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'masterID': masterID},
                        url: "<?php echo site_url('AssetManagement/asset_request_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if (data[0] == 's') {
                                swal("Success", ":)", "done");
                                // fetchPage('system/expenseClaim/expense_claim_management', expenseClaimMasterAutoID, 'Expense Claim');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    // function add_more() {

    //         $('select.select2').select2('destroy');
    //         var appendData = $('#ec_detail_add_table tbody tr:first').clone();
    //         // appendData.find('.expenseClaimCategoriesAutoID,.item_text').empty();
    //         appendData.find('.description').val('');
    //         appendData.find('.referenceNo').val('');
    //         //appendData.find('.transactionCurrencyID,.item_text').empty();
    //         appendData.find('.transactionAmount').val('0');
    //         appendData.find('.segmentIDDetail').val(segmentID).change();
    //         appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
    //         $('#ec_detail_add_table').append(appendData);
    //         var lenght = $('#ec_detail_add_table tbody tr').length - 1;

    //         $(".select2").select2();
    //         number_validation();
    //         }
    function save_draft() {
        if (masterID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    fetchPage('system/asset_management/asset_request', masterID, 'Asset Request');
                });
        }
    }


    function asset_request_line_delete_item(id) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {"detailsID": id},
                        url: "<?php echo site_url('AssetManagement/delete_asset_request_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_arn_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $('#request-details_add-tb tbody tr').not(':first').remove();


                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'detailsID': id},
                        url: "<?php echo site_url('AssetManagement/fetch_asset_request_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            
                            // detailsID = data['detailsID'];
                            
                            // $('#masterIDEdit').val(data['masterID']).change();
                            $('#itemDescriptionEdit').val(data['itemDescription']);
                            // $('#uomidEdit').val(data['UOMID']);
                            $('#contractIDEdit').val(data['contractID']);
                            $('#requestedQTYEdit').val(data['requestedQTY']);
                            $('#commentsEdit').val(data['comments']);
                            $("#asset_request_detail_edit_modal").modal({backdrop: "static"});
                          
                            stopLoad();
                        },  error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    } 
    function update_asset_request() {
        var data = $('#asset_request_edit_form').serializeArray();
        if (master) {
            data.push({'name': 'id', 'value': masterID});
            data.push({'name': 'detailsID', 'value': detailsID});
                 $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('AssetManagement/update_asset_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                masterID = null;
                                $('#asset_request_detail_edit_modal').modal('hide');
                                fetch_arn_table();;
                            }
                        }

                    }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
                });
        }
    }
   

    </script>
   

    
   