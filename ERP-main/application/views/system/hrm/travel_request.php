<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_travel_request');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$emp_id = current_userID();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(true, 1);
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div class="row">
    <div class="col-md-7">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved--></td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_confirmed'); ?><!--Not Confirmed -->/
                    <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span>
                    <?php echo $this->lang->line('common_refer_back'); ?><!--Refer-back--></td>
                <!--<td><span class="label label-info">&nbsp;</span> Closed </td>-->
            </tr>
        </table>
    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">

        <!--Add travel request-->
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/hrm/hrms_travel_request_form',null,'<?php echo $this->lang->line('common_create_travel-request'); ?>','TRQ');">
            <i
                class="fa fa-plus"></i>
            <?php echo $this->lang->line('common_create_travel-request'); ?>
        </button>
    </div>
</div>
<hr>
<!-- Table -->
<div class="table-responsive">
    <table id="travel_request_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_employee_name'); ?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_designation'); ?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('commom_trip'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?><!--Approved--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
        <tbody id="table_body">

        </tbody>
    </table>
</div>

<!-- View -->
<div class="modal fade" id="travel_request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Travel Request</h4>
            </div>
            <form class="form-horizontal" id="pr_close_form">
                <div class="modal-body">
                    <div id="conform_TRQ_body"></div>
                    <hr>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Generate -->
<div class="modal fade" id="generate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Generate</h4>
            </div>
            <div class="modal-body">
                <div id="conform_TRQ_body">
                    <div class="form-group">
                        <label > <?php echo $this->lang->line('common_select_type');?><!--Select--></label>
                        <div>
                            <input type="hidden" id="requestid" >
                            <select name="generate_type" class="form-control" id="generate_type" >
                                <option value="" >Select</option>
                                <option value="Expense Claim">Expense Claim</option>
                                <option value="Purchase Order">Purchase Order</option>
                                <option value="Purchase Request">Purchase Request</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="generate()"><?php echo $this->lang->line('common_generate');?><!--Save--></button>
            </div>

        </div>
    </div>
</div>

<!-- PO -->
<div class="modal fade" id="PO_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Purchase Order</h4>
            </div>
            <form class="form-horizontal" id="PO_form">
                <div class="modal-body">
                    <div id="conform_TRQ_body">
                        <input type="hidden" id="requestidPO">
                        <div class="form-group col-sm-6" style="margin-right:10px;">
                            <label for="supplierPrimaryCode">
                                <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('supplierPrimaryCode', $supplier_arr, '', 'class="form-control select2" id="supplierPrimaryCode" '); ?>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="POdate">
                                <?php echo $this->lang->line('procurement_approval_po_date'); ?><!--PO Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <input type="date" name="POdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="POdate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="generatePO()">
                        <?php echo $this->lang->line('common_generate'); ?><!--PO Generate-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attachment -->
<div class="modal fade" id="attachementModel" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="width: 100%">
                    <div class="col-md-12">
                        <span class="pull-right">
                        <form id="attachment_form" class="form-inline" enctype="multipart/form-data" method="post">
                            <input type="hidden" class="form-control" id="documentSystemCode" name="documentSystemCode">
                            <input type="hidden" class="form-control" id="documentID" value="TRQ" name="documentID">
                            <input type="hidden" class="form-control" id="document_name" value="Travel Request" name="document_name">
                            <div class="form-group">
                                <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                            </div>
                            <div class="form-group ">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename set-w-file-name"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                                aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                        data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="uplode_attachment()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                        </span>
                    </div>
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                        </thead>
                        <tbody id="attachment_pop" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">
                                    <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('#travel_request_table').DataTable
        ({///Loading the table
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo site_url('Employee/fetch_travel_request_confirm_table_hr'); ?>",
                "type": "POST",
                "data": function (d) {
                    d.datefrom = $("#IncidateDateFrom").val();
                    d.dateto = $("#IncidateDateTo").val();
                    d.status = $("#status").val();
                },
                "dataSrc": function (json) {
                    return json.data;
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "travelRequestCode" },
                { "data": "requestedByEmpName" },
                { "data": "designation" },
                { "data": "requestType" },
                { "data": "confirmedYN" },
                { "data": "approvedYN" },
                { "data": "action" }
            ],
            "order": [[1, "desc"]], 
            "columnDefs": [
                { "orderable": false, "targets": [6, 7] }, 
                { "searchable": false, "targets": [0] } 
            ]
        });
    });
        
    function delete_item(id, value)  
    {//delete a record from the table
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
            text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
            cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: { 'id': id },
                    url: "<?php echo site_url('Employee/delete_travel_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            // Refresh DataTable after successful deletion
                            if ($.fn.DataTable.isDataTable('#travel_request_table')) {
                                $('#travel_request_table').DataTable().ajax.reload();
                            }
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + " Error: " + errorThrown);
                    }
                });
            }
        });
    }

    function approvalview(id) 
    {
        $('#trq_user_modal').modal('show');
    }

    function reviseClaim(id)
    {///Return the confiramtion
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_refer_back'); ?>", /*You want to refer back!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>", /*Yes!*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" /*Cancel*/
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id': id},
                url: "<?php echo site_url('Employee/reverse_travel_request'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        if ($.fn.DataTable.isDataTable('#travel_request_table'))
                        {
                            $('#travel_request_table').DataTable().ajax.reload();
                        }
                    }
                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                }
            });
        });
    }

    function view_close(requestid)
    {///View
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'requestid': requestid, 'html': true},
            url: "<?php echo site_url('Employee/load_travel_request_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#travel_request_modal").modal({backdrop: "static"});
                $('#conform_TRQ_body').html(data);
                stopLoad();
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                refreshNotifications(true);
            }
        });
    }

    function generatePOExpense(id){
        $('#requestid').val(id);
        $('#generate_modal').modal('show');
    }

    function generate(){
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", 
            text: "<?php echo $this->lang->line('common_you_want_to_generate'); ?>", 
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_generate'); ?>", 
            cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" 
        },
            function (isConfirm) {
                var id=$('#requestid').val();
                var type=$('#generate_type').val();
                if(type!=''){
                    $('#generate_modal').modal('hide');
                    if(type=='Purchase Order'){
                        $('#PO_modal').modal('show');
                        $('#requestidPO').val(id);
                    } 
                    else if(type=='Expense Claim')
                    {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'requestid': id},
                            url: "<?php echo site_url('Employee/generateExpenseClaim'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if (data.status === 's') {
                                    myAlert('s', data.message);
                                    $('#generate_modal').modal('hide');
                                    if ($.fn.DataTable.isDataTable('#travel_request_table')) {
                                    $('#travel_request_table').DataTable().ajax.reload();
                                }
                                } else {
                                    myAlert('e', data.message);
                                }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', data.message);
                                refreshNotifications(true);
                            }
                        }); 
                    }
                    else if(type=='Purchase Request'){
                        generatePRQ(id);
                      
                    }
                   
                }
                else
                {
                    myAlert('e','select a type to generate');
                }
                
            }
        )
    }

    function generatePRQ(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'requestid': id},
            url: "<?php echo site_url('Employee/generatePR'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.status === 's') {
                    myAlert('s', data.message);
                    $('#generate_modal').modal('hide');
                    if ($.fn.DataTable.isDataTable('#travel_request_table')) {
                    $('#travel_request_table').DataTable().ajax.reload();
                }
                } else {
                    myAlert('e', data.message);
                }
            }, error: function () {
                stopLoad();
                myAlert('e', data.message);
                refreshNotifications(true);
            }
        }); 
    }

    function generatePO(){
        var id=$('#requestidPO').val();
        var suppiler=$('#supplierPrimaryCode').val();
        var POdate=$('#POdate').val();

        if(id==''||suppiler==''||POdate==''){
            myAlert('e','Fill all the fileds')
        }
        else{
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'requestid': id, 'supplier':suppiler,'PODate':POdate },
                url: "<?php echo site_url('Employee/generatePO'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data.status === 's') {
                        myAlert('s', data.message);
                        $('#PO_modal').modal('hide');
                        if ($.fn.DataTable.isDataTable('#travel_request_table')) {
                                $('#travel_request_table').DataTable().ajax.reload();
                            }
                    } else {
                        myAlert('e', data.message);
                    }
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'Error in generating Purchase Order');
                    refreshNotifications(true);
                }
            }); 
        }
    }

    function fetchAttachments(ID)
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
            dataType: 'json',
            data:  {'documentSystemCode': ID, 'documentID': 'TRQ', 'confirmedYN': 0},
            success: function (data) {
                $('#attachment_pop').empty();
                $('#attachment_pop').append('' +data+ '');
                $("#attachementModel").modal({ backdrop: "static", keyboard: true });
                $('#documentSystemCode').val(ID)       
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ajax_nav_container').html(xhr.responseText);
            }
        });
    }

    function uplode_attachment(){
        var salAccID=$('#documentSystemCode').val();
        var formData = new FormData($('#attachment_form')[0]);
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
                    fetchAttachments(salAccID);
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
</script>
