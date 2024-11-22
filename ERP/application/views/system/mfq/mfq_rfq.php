<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('srm', $primaryLanguage);
$title = $this->lang->line('manufacturing_customer_inquiry');
echo head_page($title, false);
$segment = fetch_mfq_segment(true,false);
$employeedrop_prp_eng = load_employee_drop_mfq(3);
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
?>
<style>
/*    #estimate_print_modal {
        overflow-y:scroll;
    }*/
</style>

<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<form role="form" id="deliveryOrder_filter" class="" autocomplete="off">
    <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
    <div class="row" style="margin-top: 2%;">

        <div class="col-sm-2">
        <label>Client </label>
        <div>
        <?php echo form_dropdown('mfqCustomerAutoID[]', all_mfq_customer_drop(false), '', 'class="form-control filter" id="mfqCustomerAutoID" multiple="multiple"');
            ?>
        </div>
          
        </div>
        <div class="col-sm-2">
        <label for="filter_dateTo">Inquiry Status </label>
        <div>
        <?php echo form_dropdown('statusID', array('' => 'Select Status', '1' => 'Open', '2' => 'Awarded', '3' => 'Lost'), '', 'class="form-control filter select2 dropdownfilter" id="statusID"'); ?>
        </div>
    
        </div>
        <div class="col-sm-2">
        <label>Rfq Type</label>
        <div>
        <?php echo form_dropdown('rfqtype', array('' => 'Select Rfq Type', '1' => 'Tender', '2' => 'RFQ', '3' => 'SPC'), ' ', 'class="form-control filter select2 dropdownfilter" id="rfqtype"'); ?>
        </div>
           
        </div>
        
        <div class="col-sm-2">
        <label>Department</label>
        <div>
        <?php echo form_dropdown('DepartmentID[]', $segment,'', 'class="form-control filter" id="DepartmentID" multiple="multiple" '); ?>
        </div>    
     

        </div>
        <div class="col-sm-2">
        <label>Proposal Engineer</label>
        <div>
        <?php echo form_dropdown('proposalengID[]', $employeedrop_prp_eng,'', 'class="form-control filter" id="proposalengID"  multiple="multiple"'); ?>
        </div>
           
        </div>
        <div class="col-sm-2">
            <label>Job Status</label>
            <div>
            <?php echo form_dropdown('jobstatus',array(''=>'Select Job Status',1=>'Pending',2=>'Delivered',3=>'Invoiced'),'', 'class="form-control filter select2 dropdownfilter" id="jobstatus"  required'); ?>
            </div>
           
        </div>
        
        <div class="col-sm-1" id="search_cancel">
            <span class="tipped-top"><a id="cancelSearch" href="#"><img src='<?php echo  base_url('images/crm/cancel-search.gif')?>'></a></span>
        </div>


    </div>
</form>
<br>
<div class="row">
<div class="col-md-12 text-right">
    <button type="button" class="btn btn-primary-new size-sm"
            onclick="fetchPage('system/mfq/mfq_add_new_mfq',null,'<?php echo $this->lang->line('manufacturing_add_customer_inquiry')?>','CI');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_new_customer_inquiry') ?><!--New Customer Inquiry-->
    </button>
    <button type="button" data-text="Add" id="btnAdd"
            onclick="customer_inquiry_excel()"
            class="btn btn-sm btn-success-new size-sm">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> <?php echo $this->lang->line('common_excel') ?><!--Excel-->
    </button>
</div>
</div>
<br>
<div id="" style="margin-top: 10px">
    <div class="table-responsive">
        <table id="customer_inquiry_table" class="table table-striped table-condensed" width="100%">
            <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('srm_inquiry_code') ?><!--INQUIRY CODE--></th>
                <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('manufacturing_inquiry_date') ?><!--INQUIRY DATE--></th>
                <th class="text-uppercase" style="min-width: 20%">DETAILS<!--CLIENT--></th>
                <th class="text-uppercase" style="min-width: 12%">SUBMISSION DATES<!--CLIENT--></th>
                <th class="text-uppercase" style="min-width: 8%">AWARDED DATE<!--CLIENT--></th>
                <th class="text-uppercase" style="min-width: 8%">CLIENT<!--CLIENT--></th>
                <th class="text-uppercase" style="min-width: 8%">ESTIMATE VALUE<!--CLIENT--></th>
                <!--<th class="text-uppercase" style="min-width: 12%">--><?php /*echo $this->lang->line('manufacturing_client') */?><!--CLIENT--><!--</th>-->
                <!--<th class="text-uppercase" style="min-width: 12%">--><?php /*echo $this->lang->line('manufacturing_proposal_engineer') */?><!--PROPOSAL ENGINEER--> <!--</th>-->
                <!--<th class="text-uppercase" style="min-width: 12%">--><?php /*echo $this->lang->line('common_segment') */?><!--SEGMENT--><!--</th>-->
                <!--<th class="text-uppercase" style="min-width: 12%">--><?php /*echo $this->lang->line('manufacturing_client_ref_no') */?><!--CLIENT REF NO--><!--</th>-->
                <!--<th class="text-uppercase" style="min-width: 12%">--><?php /*echo $this->lang->line('manufacturing_actual_submission_date') */?><!--ACTUAL SUBMISSION DATE--><!--</th>-->
                <!--<th class="text-uppercase" style="min-width: 12%">--><?php /*echo $this->lang->line('manufacturing_planned_submission_date') */?><!--PLANNED SUBMISSION DATE--><!--</th>-->
                <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('manufacturing_inquiry_status') ?><!--INQUIRY STATUS--></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_quote_status') ?><!--QUOTE STATUS--></th>
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="customer_inquiry_print_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel"><?php echo $this->lang->line('manufacturing_customer_inquiry') ?><!--Customer Inquiry--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="print">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="customer_inquiry_quotation_decline_modal" role="dialog" aria-labelledby="myModalLabel" data-width="40%" data-keyboard="false" data-backdrop="static">
     <div class="modal-dialog" style="width: 30%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="usergroup-title">Decline Quote</h4>
                </div>
                <?php echo form_open('', 'role="form" id="delivery_order_Status"'); ?>
                <input type="hidden" class="form-control" id="ciMasterID" name="ciMasterID">
                <div class="modal-body">
                    <div class="row" id="comment">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('common_comment')?><!--Comment--> :</label>
                        </div>
                        <div class="form-group col-sm-7">
                            <span class="input-req" title="Required Field">
                                <textarea class="form-control" rows="2" id="comment_quote" name="comment_quote"></textarea>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer" id="update_status">
                        <button type="button" class="btn btn-sm btn-primary" onclick="updateQuoteStatus()"> Decline </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
</div>
<div class="modal fade" id="estimate_print_modal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel"><?php echo $this->lang->line('manufacturing_estimate') ?><!--Estimate--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group col-sm-3 md-offset-2">
                            <label class="title"><?php echo $this->lang->line('manufacturing_revisions') ?><!--Revisions--> : </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <select onchange="changeVersion(this.value)" class="form-control"
                                    id="est-versionLevel"></select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group col-sm-3 md-offset-2">
                            <label class="title">Discount : </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('discountView', array('1'=> 'View Discount', '0'=>'Hide Discount'), '0', ' onchange="viewDiscount(this.value)" class="form-control" id="est-discountView"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="print1">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="BOM_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel"><?php echo $this->lang->line('manufacturing_bom') ?><!--BOM--></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div id="bom_print">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachment_modal_CI" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">CUSTOMER INVOICE</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="CI_attachment_uplode_form" class="form-inline"'); ?>
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
                          <button type="button" class="btn btn-default" onclick="document_uplode_CI()"><span
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
                    <tbody id="CI_attachment_modal_body" class="no-padding">
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
         $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_rfq', 'Test', 'Bill Of Material');
        });

        $("#search_cancel").hide();
        customer_inquiry_table();
    
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });
        $('#mfqCustomerAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        
        // $("#mfqCustomerAutoID").multiselect2('selectAll', false);
        // $("#mfqCustomerAutoID").multiselect2('updateButtonText');

        $('#DepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        
        // $("#DepartmentID").multiselect2('selectAll', false);
        // $("#DepartmentID").multiselect2('updateButtonText');
        
        $('#proposalengID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        
        // $("#proposalengID").multiselect2('selectAll', false);
        // $("#proposalengID").multiselect2('updateButtonText');

        
    });

    function customer_inquiry_table() {
        oTable = $('#customer_inquiry_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            /*"bStateSave": true,*/
            "sAjaxSource": "<?php echo site_url('MFQ_CustomerInquiry/fetch_customerInquiry'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

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


                $('.xEditable').editable({
                    success: function () {
                        colorLabel($(this).data('related'));
                    }
                });

                $('.xEditableDate').editable({
                    format: 'DD-MM-YYYY',
                    viewformat: 'DD.MM.YYYY',
                    template: 'D/MMMM/YYYY ',
                    combodate: {
                        minYear: 1930,
                        maxYear: <?php echo format_date_getYear() + 10 ?>,
                        minuteStep: 1
                    },
                    success: function (response) {
                        colorLabel($(this).data('related'));
                        var thisID = $(this).attr('id');
                        if (thisID == 'dob' || thisID == 'visaExpiryDate') {
                            var dataArr = JSON.parse(response);
                            setTimeout(function () {
                                $('#' + thisID).text(dataArr[2]);
                            }, 300);
                        }
                    }
                });


            },
            "aoColumns": [
                {"mData": "ciMasterID"},
                {"mData": "ciCode"},
                {"mData": "documentDate"},
                {"mData": "details"},
                {"mData": "dates"},
                {"mData": "awardedDate"},
                {"mData": "poNumber"},
                {"mData": "estAmount"},
                {"mData": "statusID"},
                {"mData": "status"},
                {"mData": "edit"},
                {"mData": "CustomerName"},
                {"mData": "proposalengineer"},
                {"mData": "segment"},
                {"mData": "referenceNo"},
                {"mData": "dueDate"},
                {"mData": "expectedDeliveryDate"},
                {"mData": "actualDeliveryDate"},
                {"mData": "poNumber"},
                {"mData": "documentCode"},
                {"mData": "transactionCurrency"},
                {"mData": "estimateValue"},
                {"mData": "estimateCode"}
            ],
            "columnDefs":   [   {"targets": [11,12,13,14,15,16,17,18,19,20,21,22], "visible": false, "orderable": false, "searchable": true}, 
                                {"targets": [8,9,10], "orderable": false},
                                {"targets": [0,3,4,6,7,8,9], "searchable": false}
                            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name: 'customerID', value: $('#mfqCustomerAutoID').val()});
                aoData.push({name: 'statusID', value: $('#statusID').val()});
                aoData.push({name: 'rfqtype', value: $('#rfqtype').val()});
                aoData.push({name: 'DepartmentID', value: $('#DepartmentID').val()});
                aoData.push({name: 'proposalengID', value: $('#proposalengID').val()});
                aoData.push({name: 'jobstatus', value: $('#jobstatus').val()});
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

    function viewDocument(ciMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                ciMasterID: ciMasterID
            },
            url: "<?php echo site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#print").html(data);
                $("#customer_inquiry_print_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function createEstimate(ciMasterID) {
        swal({
                title: "Are you sure?",
                text: "You want to generate estimate for this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Generate",
                closeOnConfirm: true
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        ciMasterID: ciMasterID
                    },
                    url: "<?php echo site_url('MFQ_CustomerInquiry/generateEstimate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/mfq/mfq_add_new_estimate', data[2], 'Edit Estimate', 'EST');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            });
    }

    function referbackCustomerInquiry(ciMasterID) {
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
                    data: {'ciMasterID': ciMasterID},
                    url: "<?php echo site_url('MFQ_CustomerInquiry/referback_customer_inquiry'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function referbackCustomerInquiry_cus(ciMasterID) {
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
                    data: {'ciMasterID': ciMasterID},
                    url: "<?php echo site_url('MFQ_CustomerInquiry/referback_customer_inquiry_cus'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function colorLabel(labelID) {
        $('#' + labelID).addClass('pendingApproval');
        $('#msg-div').show();

    }

    function decline_quotation(ciMasterID) {
        $("#ciMasterID").val(ciMasterID);
        $('#customer_inquiry_quotation_decline_modal').modal({backdrop: "static"});
    }

    function updateQuoteStatus()
    {
        var masterID = $('#ciMasterID').val();
        var comment = $('#comment_quote').val();
        swal({
                title: "Are you sure?",
                text: "You want to Decline Quote!",
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
                    data: {'ciMasterID': masterID, 'comment': comment},
                    url: "<?php echo site_url('MFQ_CustomerInquiry/decline_customer_inquiry_quote'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#customer_inquiry_quotation_decline_modal").modal('hide');
                             oTable.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function viewDocument_customerInquiry(estimateMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateMasterID: estimateMasterID
            },
            url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_version'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                /*$('#est-versionLevel').append($("<option></option>").attr("value", " ").text('Select Version'));*/
                $('#est-versionLevel').empty();
                $.each(data, function (key, value) {
                    $('#est-versionLevel').append($("<option></option>").attr("value", value.estimateMasterID).text('[Revision ' + value.versionLevel + '] ' + value.estimateCode));
                });
                $('#est-versionLevel').val(estimateMasterID).change();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function changeVersion(estimateMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                estimateMasterID: estimateMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#print1").html(data);
                $("#estimate_print_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function viewDiscount(discountView){
        var estimateMasterID = $('#est-versionLevel').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                discountView: discountView,
                estimateMasterID: estimateMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/change_discount_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data) {
                    $("#print1").html(data);
                    $("#estimate_print_modal").modal();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function viewItemBOM(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                bomMasterID: bomMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_item_bom'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#bom_print").html(data);
                $("#BOM_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function modalFix() {
        setTimeout(function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        }, 500);
    }

    function customer_inquiry_excel()
    {
        var form = document.getElementById('deliveryOrder_filter');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('MFQ_CustomerInquiry/fetch_customerInquiry_excel'); ?>';
        form.submit();
    }
     $(".filter").change(function () {
            oTable.draw();
            $("#search_cancel").show();
    });

    

    $("#search_cancel").click(function () {
        $(".dropdownfilter").val(null).trigger('change.select2');
        $('#DepartmentID').multiselect2('deselectAll', false);
        $('#mfqCustomerAutoID').multiselect2('deselectAll', false);
        $('#proposalengID').multiselect2('deselectAll', false);
        
        $('#mfqCustomerAutoID').multiselect2('updateButtonText');
        $('#DepartmentID').multiselect2('updateButtonText');
        $('#proposalengID').multiselect2('updateButtonText');
        oTable.draw();
        $(this).hide();
    });

    function attachment_modal_CI(documentSystemCode, document_name, documentID, confirmedYN) {
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
                    $('#CI_attachment_modal_body').empty();
                    $('#CI_attachment_modal_body').append('' + data + '');
                    $("#attachment_modal_CI").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function document_uplode_CI() {
        var formData = new FormData($("#CI_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MFQ_CustomerInquiry/upload_attachment_for_inquiry'); ?>",
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
                    attachment_modal_CI($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
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