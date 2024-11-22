<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$isGCC= getPolicyValues('MANFL', 'All');
if($isGCC=='GCC'){
    $title = $this->lang->line('manufacturing_recipt_warehouse');
}
else{
    $title = $this->lang->line('manufacturing_delivery_note');
}

echo head_page($title, true);

$segment = fetch_mfq_segment(true,false);
$customer = all_mfq_customer_drop(false);
$date_format_policy = date_format_policy();
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>" />

<div id="filter-panel" class="collapse filter-panel">
    <form role="form" id="deliveryOrder_filter" class="" autocomplete="off">
        <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
        <div class="row">
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date_from');?></label><br>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="DeliveryDateFrom" onchange="oTable.draw()"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="DeliveryDateFrom"
                           class="form-control" value="">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">&nbsp;&nbsp;<?php echo $this->lang->line('common_date_to');?>&nbsp;&nbsp;</label> </br>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="DeliveryDateTo" onchange="oTable.draw()"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="DeliveryDateTo"
                           class="form-control" value="">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label>Segment :</label><br>
                <div>
                    <?php echo form_dropdown('DepartmentID', $segment,'', 'class="form-control filter " id="DepartmentID" onchange="oTable.draw()" multiple="multiple" '); ?>
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label> Customer :</label><br>
                <?php echo form_dropdown('mfqCustomerAutoID', $customer, '', 'class="form-control  filter" id="mfqCustomerAutoID" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label> Job Code :</label><br>
                <?php echo form_dropdown('mfq_job[]', get_mfq_job(), '', 'multiple id="mfq_job" onchange="oTable.draw()" class="form-control input-sm"'); ?>
            </div>
            <div class="col-sm-1" id="search_cancel" style="margin-top: 2%;">
                                <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clear_all_filters_DN()"><img
                                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
            <!--<div class="form-group col-sm-1">
                </br>
                <button type="button" class="btn-sm btn-primary pull-right" onclick="clear_all_filters_DN()" style="margin-top: -10%;"><?php /*echo $this->lang->line('common_clear');*/?></button>
            </div>-->
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class=" pull-right">
            <button type="button" data-text="Add" id="btnAdd"
                onclick="fetchPage('system/mfq/mfq_delivery_note_create',null,'<?php 
                if($isGCC=='GCC'){
                    echo $this->lang->line('manufacturing_add_recipt_warehouse');
                } else {
                    echo $this->lang->line('manufacturing_add_delivery_note');
                }
                ?>','MFQ');"
                    class="btn btn-sm btn-primary">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
            </button>
            <button type="button" data-text="Add" id="btnAdd"
                    onclick="delivery_order_excel()"
                    class="btn btn-sm btn-success">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> <?php echo $this->lang->line('common_excel') ?><!--Excel-->
            </button>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="tbl_delivery_note" class="table table-striped table-condensed">
                <thead>
                <tr>
                    <th style="min-width: 5%">&nbsp;</th>
                    <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_delivery_note_code') ?><!--DELIVERY NOTE CODE--></th>
                    <th class="text-uppercase" style="min-width: 12%;width: 99px;">SEGMENT</th>
                    <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('common_customer') ?><!--CUSTOMER--></th>
                    <!--<th class="text-uppercase" style="min-width: 12%"><?php /*echo $this->lang->line('manufacturing_job_no') */?></th>-->
                    <th class="text-uppercase" style="min-width: 12%;width: 116px;"><?php echo $this->lang->line('manufacturing_document_date') ?><!--DOCUMENT DATE--></th>
                    <th class="text-uppercase" style="min-width: 3%;width: 362px">Job Number</th>
                    <th class="text-uppercase" style="min-width: 3%"><?php echo $this->lang->line('common_confirmation') ?><!--CONFIRMATION--></th>
                    <th style="min-width: 5%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="delivery_note_view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="70%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('manufacturing_delivery_note') ?><!--Delivery Note--></h4>
            </div>
            <div class="modal-body" style="overflow: overlay;">
                <div class="col-sm-1">
                    <!-- Nav tabs -->
                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                        <li id="view-tab-container" class="active">
                            <a href="#view-tab" data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view');?><!--View--></a>
                        </li>
                        <li id="attachment-tab-container">
                            <a href="#attachment-tab" data-toggle="tab" onclick="tabAttachment()"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-11">
                    <div class="zx-tab-content">
                        <div class="zx-tab-pane active" id="view-tab">
                            <div id="conform_body"></div>
                            <!-- <hr/> -->
                            <div class="form-group hide">
                                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                <div class="col-sm-4">
                                    <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' =>  $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                    <input type="hidden" name="level" id="level">
                                    <input type="hidden" name="orderAutoID" id="orderAutoID">
                                    <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                </div>
                            </div>
                            <div class="form-group hide">
                                <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                <div class="col-sm-8">
                                    <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane hide" id="attachment-tab">
                            <div class="table-responsive">
                                <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                &nbsp <strong>Delivery Note Attachments</strong>
                                <br><br>
                                <table class="table table-striped table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                                        <th><?php echo $this->lang->line('common_description');?></th></th><!--Description-->
                                        <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                        <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                                    </tr>
                                    </thead>
                                    <tbody id="attachment_body" class="no-padding">
                                    <tr class="danger">
                                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          <!--  <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="customizeTemplateBody">

                        </div>
                    </div>
                </div>
            </div>-->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachment_modal_DN" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">DELIVERY NOTE</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="DN_attachment_uplode_form" class="form-inline"'); ?>
                            <div class="form-group">
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription"
                                       placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                                <!--Description-->
                                <input type="hidden" class="form-control" id="documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="documentID" name="documentID">
                                <input type="hidden" class="form-control" id="document_name" name="document_name">
                                <input type="hidden" class="form-control" id="confirmYNadd" name="confirmYNadd">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                              class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                              class="fileinput-filename"></span></div>
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
                          <button type="button" class="btn btn-default" onclick="document_uplode_DN()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
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
                    <tbody id="DN_attachment_modal_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_JOB" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Job Attachment</h4>
            </div>
            <div class="modal-body">
                
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="text-align:start;">#</th>
                        <th style="text-align:start;">Job No</th>
                        <th style="text-align:start;"><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th style="text-align:start;"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th style="text-align:start;"><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                    </tr>
                    </thead>
                    <tbody id="job_modal_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing <button class="btn btn-default pull-right" onclick="print_tracing_view()"><i class="fa fa-print"></i> </button>
            </div>
            </h4>
            <div class="modal-body">
                <input type="hidden" id="tracingId" name="tracingId">
                <input type="hidden" id="tracingCode" name="tracingCode">
                <div id="mcontainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var oTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_delivery_note', '', 'Delivery Note');
        });

        $(".select2").select2();
       

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (e) {
            oTable.draw();
        });

        $(".filter").change(function () {
            oTable.draw();
            $("#search_cancel").show();
        });

        template();

        $('#DepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        
      

        $('#mfqCustomerAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#mfq_job").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
     
    });

    function template() {
        oTable = $('#tbl_delivery_note').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_DeliveryNote/fetch_delivery_note'); ?>",
            "aaSorting": [[0, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
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
            "aoColumns": [
                {"mData": "deliverNoteID"},
                {"mData": "deliveryNoteCode"},
                {"mData": "segment"},
                {"mData": "CustomerName"},
                /*{"mData": "jobCode"},*/
                {"mData": "deliveryDate"},
                {"mData": "job_codes"},
                {"mData": "status"},
                {"mData": "edit"}
            ],
            "columnDefs": [
                {"targets": [5,6], "orderable": false}, {"targets": [0], "searchable": false}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name: 'mfqCustomerAutoID', value: $('#mfqCustomerAutoID').val()});
                aoData.push({name: 'DepartmentID', value: $('#DepartmentID').val()});
                aoData.push({name: 'DeliveryDateFrom', value: $('#DeliveryDateFrom').val()});
                aoData.push({name: 'DeliveryDateTo', value: $('#DeliveryDateTo').val()});
                aoData.push({name: 'jobID', value: $('#mfq_job').val()});
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

//this function logic is different and not used in this file.
    function referBack_delivery_note(deliverNoteID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'deliverNoteID': deliverNoteID},
                    url: "<?php echo site_url('MFQ_DeliveryNote/referback_delivery_note'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            template();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function view_delivery_note(deliverNoteID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'deliverNoteID': deliverNoteID, 'html': true},
            url: "<?php echo site_url('MFQ_DeliveryNote/load_deliveryNote_confirmation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#conform_body').html(data);
                load_delivery_note_attachment('DN', deliverNoteID);
                $("#delivery_note_view_modal").modal();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function delete_delivery_note(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'deliverNoteID': id},
                    url: "<?php echo site_url('MFQ_DeliveryNote/delete_delivery_note'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's') {
                            template();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters_DN()
    {
     $('#DepartmentID').multiselect2('deselectAll', false);
     $('#mfqCustomerAutoID').multiselect2('deselectAll', false);
     $('#mfqCustomerAutoID').multiselect2('updateButtonText');
     $('#DepartmentID').multiselect2('updateButtonText');
        $('#DeliveryDateFrom').val('');
        $('#DeliveryDateTo').val('');
    }

    function tabAttachment(){
        $("#attachment-tab").removeClass("hide");
    }
    function tabView(){
        $("#attachment-tab").addClass("hide");
    }

    function load_delivery_note_attachment(documentID, documentSystemCode) {
        $("#attachment-tab").removeClass("active");
        $("#view-tab").addClass("active");
        $("#attachment-tab-container").removeClass("active");
        $("#view-tab-container").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#attachment_body').empty().append('' +data+ '');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delivery_order_excel()
    {
        var form = document.getElementById('deliveryOrder_filter');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('MFQ_DeliveryNote/fetch_delivery_note_excel'); ?>';
        form.submit();
    }

    function attachment_modal_DN(documentSystemCode, document_name, documentID, confirmedYN) {
        $('#attachmentDescription').val('');
        $('#documentSystemCode').val(documentSystemCode);
        $('#document_name').val(document_name);
        $('#documentID').val(documentID);
        $('#confirmYNadd').val(confirmedYN);
        $('#remove_id').click();
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': confirmedYN},
                success: function (data) {
                    $('#DN_attachment_modal_body').empty();
                    $('#DN_attachment_modal_body').append('' + data + '');
                    $("#attachment_modal_DN").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    /**job attachments */
    function modal_JOB(documentSystemCode) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_DeliveryNote/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': 'JOB'},
                success: function (data) {
                    $('#job_modal_body').empty();
                    $('#job_modal_body').append('' + data + '');
                    $('#modal_JOB').modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }


    function document_uplode_DN() {
        var formData = new FormData($("#DN_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MFQ_DeliveryNote/upload_attachment_for_DeliveryNote'); ?>",
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
                    attachment_modal_DN($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
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

    function trace_mfq_document(deliverNoteID, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'deliverNoteID': deliverNoteID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/trace_MDN_document'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(deliverNoteID, DocumentID);
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_document_tracing(id, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'purchaseOrderID': id,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#mcontainer").empty();
                $("#mcontainer").html(data);
                $("#tracingId").val(id);
                $("#tracingCode").val(DocumentID);

                $("#tracing_modal").modal('show');
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function deleteDocumentTracing() {
        var purchaseOrderID = $("#tracingId").val();
        var DocumentID = $("#tracingCode").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'purchaseOrderID': purchaseOrderID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#tracing_modal").modal('hide');
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function referBackDeliveryNote(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'deliverNoteID': id},
                    url: "<?php echo site_url('MFQ_DeliveryNote/referback_delivery_note_with_validation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            template();//refreshing delivery notes table.
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>