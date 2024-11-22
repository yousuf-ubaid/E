<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_quotation_Contract_sales_order');
echo head_page($title, true);

/*echo head_page('Quotation / Contract / Sales Order',true);*/
$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customerCategory    = party_category(1, false);
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row" style="">
        <div class="form-group col-sm-4" style="margin-left: 4px;">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?></label><br><!--Date-->
                <label for="supplierPrimaryCode" style="margin-left: 5px;"><?php echo $this->lang->line('common_from');?></label><!--From-->
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw();" value="" id="IncidateDateFrom"
                       class="input-small datepic">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to');?><!--To-->&nbsp&nbsp</label>
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw();" value="" id="IncidateDateTo"
                       class="input-small datepic">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="customerCode"><?php echo $this->lang->line('common_customer_name');?> </label><br><!--Customer Name-->
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw();" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-2" style="margin-left: 10px;">
            <label for="contractType"><?php echo $this->lang->line('sales_markating_transaction_document_type');?> </label><br><!--Document type-->
            <?php echo form_dropdown('contractType[]', array('Quotation' => $this->lang->line('sales_markating_transaction_quotation')/*'Quotation'*/,'Contract' => $this->lang->line('sales_markating_transaction_contract')/*'Contract'*/,'Sales Order' => $this->lang->line('sales_markating_transaction_sales_order')/*'Sales Order'*/), '', 'class="form-control" id="contractType" onchange="Otable.draw();" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3" style="margin-left: 10px">
            <label for="category"><?php echo $this->lang->line('common_category');?> </label><br><!--Category-->
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-4" style="margin-left: 2%">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?></label><br><!--Status-->
            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' =>$this->lang->line('common_draft') /*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw();"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
            </button>
        </div>
    </div>



</div>
<div class="row" style="margin-top: 2px">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?> </td><!--Confirmed / Approved-->
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?> / <?php echo $this->lang->line('common_not_approved');?></td><!--Not Confirmed / Not Approved-->
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?></td><!--Refer-back-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/quotation_contract/erp_quotation_contract_buyback',null,'Add New Quotation or Contract','CNT');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_create');?> </button><!--Create-->
    </div>
</div><hr>
<div class="table-responsive">
    <table id="quotation_contract_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width: 13%"> <?php echo $this->lang->line('common_code');?></th><!--Code-->
                <th style="min-width: 40%"> <?php echo $this->lang->line('common_details');?></th><!--Details-->
                <th style="min-width: 7%"><?php echo $this->lang->line('common_type');?></th><!--Type-->
                <th style="min-width: 13%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
                <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="document_version_View" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="document_version_ViewTitle"><?php echo $this->lang->line('sales_markating_transaction_model_title');?></h4><!--Modal title-->
                <input type="hidden" name="contractAutoID" id="contractAutoID">
            </div>
            <div class="modal-body" id="loaddocument_version_View">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" onclick="quotation_version()" role="button"><?php echo $this->lang->line('sales_markating_transaction_quotation_version');?> </a><!--Quotation Version-->
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_closed');?> </button><!--Close-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="drill_down_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="document_version_ViewTitle"><?php echo $this->lang->line('sales_markating_transaction_drill_down');?></h4><!--Drill Down-->
            </div>
            <div class="modal-body" >
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></td><!--Invoice code-->
                            <td><?php echo $this->lang->line('sales_markating_transaction_invoice_date');?></td><!--Invoice Date-->
                            <td><?php echo $this->lang->line('common_customer_name');?></td><!--Customer Name-->
                            <td><?php echo $this->lang->line('common_total');?></td><!--Total-->
                            <td><?php echo $this->lang->line('common_action');?></td> <!--Action-->
                        </tr>
                    </thead>
                    <tbody id="drill_down_table">
                        <tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr><!--No Records Founds-->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('sales_markating_transaction_close');?></button><!--Close-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 35%">
        <form method="post" id="Send_Email_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="contractid" id="email_contractid" value="">
                    <h4 class="modal-title" id="EmailHeader">Email</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="emailContent">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>
                    <div class="append_data_nw">
                        <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                            <div class="col-sm-1">
                            </div>
                            <div class="col-sm-8">
                                <input type="email" name="email" id="email" class="form-control"
                                       placeholder="example@example.com" style="margin-left: -10px">
                            </div>
                            <div class="col-sm-1 remove-tdnw">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="SendQuotationMail()">Send Email</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>-->
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing</h4>
            </div>
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

<div class="modal fade" id="contract_close_modal_buyback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Close Quotation / Contract</h4>
            </div>
            <form class="form-horizontal" id="contract_close_buy_form">
                <div class="modal-body">
                    <div id="contract_close"></div>
                    <hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_date'); ?><!--Date--></label>
                        <div class="col-sm-4">
                            <div class="input-group datepickerClose">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="closedDate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="closedDate" class="form-control" required>
                            </div>
                            <input type="hidden" name="contractAutoID" id="contractAutoID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="submit" class="btn btn-primary">Close Document</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="closed_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="closed_user_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt>Document code</dt>
                    <dd id="c_document_code">...</dd>
                    <dt>Document Date</dt>
                    <dd id="c_document_date">...</dd>
                    <dt>Confirmed Date</dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="6">Approved Details</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th>Approved Date</th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comment'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="approved_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="6" class="text-center">
                            <?php echo $this->lang->line('footer_document_not_approved_yet'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
                <br><br>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="4">Closed Details</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th>Closed Date</th>
                        <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody id="closed_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="4" class="text-center">Document not closed yet.</td>
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
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Quotation Contract Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="contractID">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Print Option</label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page'), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_quotation_contract_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var contractAutoID;
var Otable;
var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
$('.datepic').datetimepicker({
    useCurrent: false,
    format: date_format_policy,
}).on('dp.change', function (e) {
    Otable.draw();
});


$('.datepickerClose').datetimepicker({
    useCurrent: false,
    format: date_format_policy,
}).on('dp.change', function (ev) {
    //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
});

$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/quotation_contract/quotation_contract_management_buyback','','Quotation or Contracts');
    });
    contractAutoID = null;
    number_validation();
    quotation_contract_table();

    $('#customerCode').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#contractType').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
    $('#category').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    Inputmask().mask(document.querySelectorAll("input"));


    $('#contract_close_buy_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            // contractAutoID: {validators: {notEmpty: {message: 'contractAutoID is required.'}}},
            comments: {validators: {notEmpty: {message: 'comments is required.'}}},
            closedDate: {validators: {notEmpty: {message: 'Date is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'contractAutoID', 'value': $('#contractAutoID').val()});
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to close this document!",/*You want to close this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Quotation_contract/close_contract'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        $("#contract_close_modal_buyback").modal('hide');
                        stopLoad();
                        Otable.draw();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    });

});

function quotation_contract_table(selectedID=null){
    Otable = $('#quotation_contract_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Quotation_contract/fetch_Quotation_contract_buyback'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
            var tmp_i   = oSettings._iDisplayStart;
            var iLen    = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {

                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                if( parseInt(oSettings.aoData[x]._aData['contractAutoID']) == selectedRowID ){
                    var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                    $(thisRow).addClass('dataTable_selectedTr');

                }
                x++;
            }
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');

        },
        "aoColumns": [
            {"mData": "contractAutoID"},
            {"mData": "contractCode"},
            {"mData": "detail"},
            {"mData": "contractType"},
            {"mData": "total_value"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "edit"},
            {"mData": "contractNarration"},
            {"mData": "customerMasterName"},
            {"mData": "contractDate"},
            {"mData": "contractExpDate"},
            {"mData": "contractType"},
            {"mData": "isDeleted"},
            {"mData": "detTransactionAmount"}
        ],
        "columnDefs": [{"targets": [7], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10,11,12,13,14] },{"visible":true,"searchable": false,"targets": [0,2,4,5,6,7] }],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
            aoData.push({"name": "status", "value": $("#status").val()});
            aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
            aoData.push({"name": "contractType", "value": $("#contractType").val()});
            aoData.push({"name": "category", "value": $("#category").val()});
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

$('.table-row-select tbody').on('click', 'tr', function () {
    $('.table-row-select tr').removeClass('dataTable_selectedTr');
    $(this).toggleClass('dataTable_selectedTr');
});

function referback_customer_contract(id,code){
    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
        text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
        type: "warning",/*warning*/
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
            data: {'contractAutoID': id,'code':code},
            url: "<?php echo site_url('Quotation_contract/referback_Quotation_contract'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    Otable.draw();
                }
            }, 
            error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });
}

function delete_item(id,value){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'contractAutoID':id},
                url :"<?php echo site_url('Quotation_contract/delete_con_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    refreshNotifications(true);
                    stopLoad();
                    Otable.draw();
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });        
}

    function referback_customer_invoice(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'contractAutoID':id},
                    url :"<?php echo site_url('Quotation_contract/referback_customer_invoice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function quotation_version(){
        $('#document_version_View').modal('hide');
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_reversing_this_quotation');?> ",/*You want to reverse this version*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'contractAutoID':$('#contractAutoID').val()},
                    url :"<?php echo site_url('Quotation_contract/quotation_version'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data['type'], data['message'], 1000);
                        if (data['status']) {
                            Otable.draw();
                        }   
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function document_version_View_modal(documentID, para1){
        title = "Quotation";
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'contractAutoID': para1,'html': true},
            url: "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#document_version_ViewTitle').html(title);
                $('#loaddocument_version_View').html(data);
                $('#document_version_View').modal('show');
                $("#contractAutoID").val(para1);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function document_drill_down_View_modal(documentID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': documentID},
            url: "<?php echo site_url('Quotation_contract/document_drill_down_View_modal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#drill_down_table').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                 $('#drill_down_table').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                }
                else {
                    $.each(data, function (key, value) {
                        $('#drill_down_table').append('<tr><td>' + x + '</td><td><a target="_blank" onclick="documentPageView_modal(\'CINV\',' + value['invoiceAutoID'] + ')" >' + value['invoiceCode'] + '</a></td><td>' + value['invoiceDueDate'] + '</td><td>' + value['customerName'] + '</td><td class="pull-right">' + parseFloat(value['contractAmount']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td ><a class="pull-right" target="_blank" onclick="documentPageView_modal(\'CINV\',' + value['invoiceAutoID'] + ')" ><span class="glyphicon glyphicon-eye-open"></span></a></td></tr>');
                            x++;
                    });
                    //<td class="text-right"><a onclick="edit_addon_cost_model(' + value['id'] + ')"><span class="glyphicon glyphicon-pencil" style="color:blue;"></span></a></td>
                }
                $('#drill_down_modal').modal('show');
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

function reOpen_contract(id){
    swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open');?>",/*You want to re open*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'contractAutoID':id},
                url :"<?php echo site_url('Quotation_contract/re_open_contract'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    Otable.draw();
                    stopLoad();
                    refreshNotifications(true);
                },error : function(){
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

function clear_all_filters(){
    $('#IncidateDateFrom').val("");
    $('#IncidateDateTo').val("");
    $('#status').val("all");
    $('#customerCode').multiselect2('deselectAll', false);
    $('#customerCode').multiselect2('updateButtonText');
    $('#contractType').multiselect2('deselectAll', false);
    $('#contractType').multiselect2('updateButtonText');
    Otable.draw();
}

function sendemail(id) {
    $('#email_contractid').val(id);
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'contractAutoID': id},
        url: "<?php echo site_url('Quotation_contract/loademail'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $("#Email_modal").modal();
            if (!jQuery.isEmptyObject(data)) {
                $('#email').val(data['customerEmail']);
            }
            stopLoad();
            refreshNotifications(true);
        }, error: function () {
            stopLoad();
            alert('An Error Occurred! Please Try Again.');
            refreshNotifications(true);
        }
    });
}

function SendQuotationMail() {
    var form_data = $("#Send_Email_form").serialize();
    swal({
            title: "Are You Sure?",
            text: "You Want To Send This Mail",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55 ",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: form_data,
                url: "<?php echo site_url('Quotation_contract/send_quatation_email'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#Email_modal").modal('hide');
                    }
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}


function traceDocument(cntID,DocumentID){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'contractAutoID': cntID,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/trace_cnt_document'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            //myAlert(data[0], data[1]);
            $(window).scrollTop(0);
            load_document_tracing(cntID,DocumentID);
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function load_document_tracing(id,DocumentID){
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {'purchaseOrderID': id,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $("#mcontainer").empty();
            $("#mcontainer").html(data);
            $("#tracingId").val(id);
            $("#tracingCode").val(DocumentID);
            $("#tracing_modal").modal('show');

        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function deleteDocumentTracing(){
    var purchaseOrderID=$("#tracingId").val();
    var DocumentID=$("#tracingCode").val();
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'purchaseOrderID': purchaseOrderID,'DocumentID': DocumentID},
        url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $("#tracing_modal").modal('hide');
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });

}

function contract_close_buyback(contractAutoID) {
    if(contractAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'contractAutoID': contractAutoID, 'html': true},
            url: "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contractAutoID').val(contractAutoID);
                $("#contract_close_modal_buyback").modal({backdrop: "static"});
                $('#contract_close').html(data);
                $('#comments').val('');
                stopLoad();
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
}

function fetch_approval_closed_user_modal(documentID, documentSystemCode) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
        url: '<?php echo site_url('Approvel_user/fetch_document_closed_users_modal'); ?>',
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            $('#closed_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Closed User');
            <!--Approval user-->
            $('#closed_user_body').empty();
            $('#approved_user_body').empty();
            /** Approval Details*/
            x = 1;
            if (jQuery.isEmptyObject(data['approved'])) {
                $('#approved_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                <!--No Records Found-->
            } else {
                $.each(data['approved'], function (key, value) {
                    comment = ' - ';
                    if (value['approvedComments'] !== null) {
                        comment = value['approvedComments'];
                    }
                    approvalDate = ' - ';
                    if (value['approveDate'] !== null) {
                        approvalDate = value['approveDate'];
                    }
                    bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                    $('#approved_user_body').append('<tr><td>' + x + '</td><td>' +  value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">' + approvalDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                    x++;
                });
            }

            /** Closed Details*/
            if (data['closedYN'] == 1) {
                $('#closed_user_body').append('<tr><td> 1 </td><td>' + data['closedBy'] + '</td><td class="text-center">' + data['closedDate'] + '</td><td class="text-center">' + data['closedReason'] + '</td></tr>');
            } else {
                $('#closed_user_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                <!--No Records Found-->
            }

            /** confirmed Details*/
            $("#closed_user_modal").modal({backdrop: "static", keyboard: true});
            $("#c_document_code").html(data['document_code']);
            $("#c_document_date").html(data['document_date']);
            $("#c_confirmed_date").html(data['confirmed_date']);
            $("#c_conformed_by").html(data['conformed_by']);

            if (documentID == 'LA' && (data.requestForCancelYN !== undefined)) {
                $('#closed_user_label').append(' - Cancellation');
            }
            stopLoad();
        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            /*An Error Occurred! Please Try Again*/
            stopLoad();
            refreshNotifications(true);
        }
    });
}
function load_printtemp(contractID)
{
    $('#printSize').val(1);
    $('#contractID').val(contractID);
    $('#print_temp_modal').modal('show');
}
function print_quotation_contract_temp(){
    var printSize =  $('#printSize').val();
    var contractID =   $('#contractID').val();

    if(contractID==''){
        myAlert('e', 'Select Print Type');
    }else{
        window.open("<?php echo site_url('Quotation_contract/load_contract_conformation_buyback') ?>" +'/'+ contractID +'/'+ printSize +'/'+1);
    }
}
</script>