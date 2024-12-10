<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_revising_approved_document');
echo head_page($title, true);

$policySplitDoc = getPolicyValues('DOCS','All');

$doc_arr = [
    'PO'=>'PO | Purchase Order','GRV'=>'GRV | Goods Received Voucher','SR'=>'SR | Stock Return','ST'=>'ST | Stock Transfer','BSI'=>'BSI | Supplier Invoice',
    'PV'=>'PV | Payment Voucher','MI'=>'MI | Material Issue','DN'=>'DN | Debit Note','CINV'=>'CINV | Customer Invoice','RV'=>'RV | Receipt Voucher',
    'MRV'=>'MRV | Material Receipt Voucher','MRN'=>'MRN | Material Receipt Note','CN'=>'CN | Credit Note','QUT'=>'QUT | Quotation','CNT'=>'CNT | Contract',
    'SO'=>'SO | Sales Order','SP'=>'SP | Salary Process','SPN'=>'SPN | Non Salary Process','SD'=>'SD | Salary Declaration','JV'=>'JV | Journal Voucher',
    'BT'=>'BT | Bank Transfer','SLR'=>'SLR | Sales Return','FA'=>'FA | Fixed Asset','ATS'=>'ATS | Attendance Summary','PRQ'=>'PRQ | Purchase Request',
    'MR'=>'MR | Material Request','DO'=>'DO | Delivery Order','HCINV'=>'HCINV | Invoice (Buyback)','BRC'=>'BRC | Bank Reconciliation','INV'=>'INV | Item Master',
    'SUP'=>'SUP | Supplier Master', 'EC'=> 'EC | Expense Claim', 'P'=> 'P | Project', 'FAD' => 'FAD | Fixed Asset Depreciation', 'SC' => 'SC | Sales Commission',
    'RJV' => 'RJV | Recurring Journal Voucher', 'BD' => 'BD | Budget', 'ADSP' => 'ADSP | Asset Disposal'
]

?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?><!--Date--></label><br>
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?><!--From--></label>
                <input class="input-small" id="IncidateDateFrom" data-date="" data-date-format="dd-mm-yyyy" size="11"
                       type="text" name="IncidateDateFrom" placeholder="DD-MM-YYYY" value="" onchange="fetch_reversing_approval_table()">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_to');?><!--To-->&nbsp;&nbsp;</label>
                <input class="input-small" id="IncidateDateTo" data-date="" data-date-format="dd-mm-yyyy" size="11"
                       type="text" name="IncidateDateTo" placeholder="DD-MM-YYYY" value="" onchange="fetch_reversing_approval_table()">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="documentID"> <?php echo $this->lang->line('config_document_type');?><!--Document type--></label><br>
            <?php echo form_dropdown('documentID[]', $doc_arr, '', 'class="form-control" id="documentID" onchange="fetch_reversing_approval_table()" multiple="multiple"'); ?>
        </div>

    
        
        <div class="form-group col-sm-2">
            <label for="">&nbsp;</label><br> &nbsp;&nbsp;&nbsp;
            <button type="button" class="btn btn-primary"
                    onclick="clear_all_filters()" style="/*margin-top: -10%;*/"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>
           
        </div>

        <div class="form-group col-sm-2">
            <?php if($policySplitDoc == 1) { ?>
                <button type="button" class="btn btn-primary"
                        onclick="load_split_modal()" style="margin-top: 10%"><i class="fa fa-paint-brush"></i> <?php echo 'Document Split'?><!--Clear-->
                </button>
            
            <?php } ?>
           
        </div>


        
    </div>

    
</div>



<div class="table-responsive">
    <table id="reversing_approval_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="4"><?php echo $this->lang->line('common_document');?><!--Document--></th>
            <th colspan="4"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
        </tr>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 20%"> <?php echo $this->lang->line('common_comments');?><!--Comments--></th>
            <th style="min-width: 5%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="reversing_approval_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="document_title">Modal title</h4>
            </div>
            <form class="form-horizontal" id="reversing_approval_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-1">
                                <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                    <li id="TabViewActivation_view" class="active"><a href="#home-v" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                    <li id="TabViewActivation_attachment"><a href="#profile-v" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                </ul>
                            </div>
                            <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                <div class="zx-tab-content">
                                    <div class="zx-tab-pane active" id="home-v">
                                        <div id="load_approved_document" class="col-md-12"></div>
                                    </div>
                                    <div class="zx-tab-pane" id="profile-v">
                                        <div id="loadPageViewAttachment" class="col-md-8">
                                            <div class="table-responsive">
                                                <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>&nbsp; <strong><?php echo $this->lang->line('common_attachments');?><!--Attachments--></strong>
                                                <br><br>
                                                <table class="table table-striped table-condensed table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                                                        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                                        <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                                        <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="View_attachment_modal_body" class="no-padding">
                                                    <tr class="danger">
                                                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                            <input type="hidden" name="auto_id" id="auto_id">
                            <input type="hidden" name="document_id" id="document_id">
                            <input type="hidden" name="document_code" id="document_code">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('config_reverse_this_document');?><!--Reverse this document--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="access_denied" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
       
            </div>
            <div class="modal-body">
                <h6 class="modal-title" id="myModalLabel_title" style="color: red;font-size: 13px;">You cannot process this document reversal. Because this document has been pulled for documents listed below.</h6>
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('config_system_code');?><!--System Code--></th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>





<script type="text/javascript">
    fetch_reversing_approval_table();
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/approval_document/erp_approval_document','','Reversing Approved Document');
        });

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#documentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('#IncidateDateTo').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('#IncidateDateFrom').datepicker({
            format: 'yyyy-mm-dd'
        });

        $('#reversing_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                auto_id       : {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_auto_id_is_required');?>.'}}},/*Auto ID is required*/
                comments      : {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_comments_are_required');?>.'}}},/*Comments are required*/
                document_id   : {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_document_id_is_required');?>.'}}},/*Document ID is required*/
                document_code : {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_document_code_is_required');?>.'}}}/*Document Code is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            var document_code = $('#document_code').val();

            var massage = (document_code == 'SP')? "All the bank transfers related with this payroll will be deleted" : "<?php echo $this->lang->line('config_you_want_to_reverse_this_record');?>";/*You want to reverse this document!*/

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: massage,
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
                        data: data,
                        url: "<?php echo site_url('Reversing_approval/reversing_approval_document'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if(document_code =='BRC')
                            {
                               $('#myModalLabel_title').html('You cannot process this document reversal. Because Bank Reconciliation documents for this selected bank are created with future dates');
                            }else
                            {
                                $('#myModalLabel_title').html('You cannot process this document reversal. Because this document has been pulled for documents listed below.');
                            }
                            refreshNotifications(true);
                            if (data['status']=='A') {
                                    $('#access_denied_body').empty();x = 1;
                                    if (jQuery.isEmptyObject(data['data'])) {
                                        $('#access_denied_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                                    }
                                    else {
                                        $.each(data['data'], function (key, value) {
                                            $('#access_denied_body').append('<tr><td>' + x + '</td><td>' + value['system_code'] + '</td></tr>');
                                            x++;
                                        });
                                    }
                                    $('#access_denied').modal('show');

                            }else{
                                $('#reversing_approval_document').modal('hide');
                                fetch_reversing_approval_table();
                                $('#reversing_approval_form')[0].reset();
                                $('#reversing_approval_form').bootstrapValidator('resetForm', true);
                            }
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        });
    });

    function fetch_reversing_approval_table(selectedID=null) {
        var Otable = $('#reversing_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Reversing_approval/fetch_reversing_approval'); ?>",
            "aaSorting": [[1, 'desc']],
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
                    if( parseInt(oSettings.aoData[x]._aData['documentApprovedID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "documentApprovedID"},
                {"mData": "documentCode"},
                {"mData": "documentID"},
                {"mData": "documentDate"},
                {"mData": "empName"},
                {"mData": "approvedDate"},
                {"mData": "approvedComments"},
                {"mData": "action"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"visible":true,"searchable": true,"targets": [1,2,3,4,5,6] }, {"visible":true,"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
                aoData.push({"name": "documentID", "value": $("#documentID").val()});
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

    function reversing_approval_modal(documentID,auto_id, para1,para2) {
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        attachment_View_modal(documentID, para1);
        $('#load_approved_document').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;
        switch (documentID) {
            case "BT":  // Bank Transfer 
                siteUrl = "<?php echo site_url('Bank_rec/bank_transfer_view'); ?>";
                paramData.push({'name': 'bankTransferAutoID', 'value': para1});
                title = "Bank Transfer";
                break;
            case "PO": // Purchase Order -
                siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>";
                paramData.push({'name': 'purchaseOrderID', 'value': para1});
                title = "Purchase Order";
                break;
            case "GRV": // Good Receipt Voucher -
                siteUrl = "<?php echo site_url('Grv/load_grv_conformation'); ?>";
                paramData.push({'name': 'grvAutoID', 'value': para1});
                title = "Goods Received Voucher";
                a_link = "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                break;
            case "SR": // Purchase Return -
                siteUrl = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>";
                paramData.push({'name': 'stockReturnAutoID', 'value': para1});
                title = "Purchase Return";
                a_link = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_stock_return'); ?>/" + para1 + '/SR';
                break;
            case "MI": // Material Issue -
                siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>";
                paramData.push({'name': 'itemIssueAutoID', 'value': para1});
                title = "Material Issue";
                a_link = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                break;
            case "ST": // Stock Transfer -
                siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>";
                paramData.push({'name': 'stockTransferAutoID', 'value': para1});
                title = "Stock Transfer";
                a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                break;
            case "SA": // Stock Adjustment -
                siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>";
                paramData.push({'name': 'stockAdjustmentAutoID', 'value': para1});
                title = "Stock Adjustment";
                a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                break;
            case "BSI": // Supplier Invoices -
                siteUrl = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>";
                paramData.push({'name': 'InvoiceAutoID', 'value': para1});
                title = "Supplier Invoices";
                a_link = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + para1 + '/BSI';
                break;
            case "DN": // Debit Note -
                siteUrl = "<?php echo site_url('Payable/load_dn_conformation'); ?>";
                paramData.push({'name': 'debitNoteMasterAutoID', 'value': para1});
                title = "Debit Note";
                a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + para1 + '/DN';
                break;
            case "PV": // Payment Voucher -
                siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                paramData.push({'name': 'payVoucherAutoId', 'value': para1});
                title = "Payment Voucher";
                a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                break;
            case "PVM": // Payment Match -
                siteUrl = "<?php echo site_url('Payment_voucher/load_pvm_conformation'); ?>";
                paramData.push({'name': 'payVoucherAutoId', 'value': para1});
                title = "Payment Voucher";
                a_link = "<?php echo site_url('Payment_voucher/load_pvm_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                break;
            case "CINV": // Invoice -
                if(para2 == 'Commission' )
                {
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    title = "Invoice";
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                }else{
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation'); ?>";
                    paramData.push({'name': 'invoiceAutoID', 'value': para1});
                    title = "Invoice";
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                }
            case "CN": // Credit Note -
                siteUrl = "<?php echo site_url('Receivable/load_cn_conformation'); ?>";
                paramData.push({'name': 'creditNoteMasterAutoID', 'value': para1});
                title = "Credit Note";
                a_link = "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/CN';
                break;
            case "RV": // Receipt Voucher -
                siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>";
                paramData.push({'name': 'receiptVoucherAutoId', 'value': para1});
                title = "Receipt Voucher";
                a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                break;
            case "RVM": // Receipt Matching
                siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>";
                paramData.push({'name': 'matchID', 'value': para1});
                title = "Receipt Matching";
                break;
            case "JV": // Journal entry -
                siteUrl = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>";
                paramData.push({'name': 'JVMasterAutoId', 'value': para1});
                title = "Journal Entry";
                a_link = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_journal_entry'); ?>/" + para1 + '/JE';
                break;
            case "FAD": // Fixed Asset Depriciation
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_view'); ?>";
                paramData.push({'name': 'depMasterAutoID', 'value': para1});
                title = "Asset Depreciation";
                break;
            case "ADSP": // Fixed Asset Disposal
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_disposal_view'); ?>";
                paramData.push({'name': 'assetdisposalMasterAutoID', 'value': para1});
                title = "Asset Disposal";
                break;
            case "SD": // Salary Declaration
                siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                paramData.push({'name': 'declarationMasterID', 'value': para1});
                title = "Salary Declaration";
                break;
            case "CNT": // Contract
                siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                paramData.push({'name': 'contractAutoID', 'value': para1});
                title = "Contract";
                break;
            case "QUT": // Quotation
                siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                paramData.push({'name': 'contractAutoID', 'value': para1});
                title = "Quotation";
                break;
            case "SO": // Quotation
                siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                paramData.push({'name': 'contractAutoID', 'value': para1});
                title = "Sales Order";
                break;
            case "SP": // Salary Processing 
                siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                paramData.push({'name': 'hidden_payrollID', 'value': para1});
                paramData.push({'name': 'isNonPayroll', 'value': 'N'});
                paramData.push({'name': 'from_approval', 'value': 'Y'});
                paramData.push({'name': 'isForReverse', 'value': 'Y'});
                title = "Monthly Allowance";
                break;
            case "SPN": // Salary Processing (Non-payroll)
                siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                paramData.push({'name': 'hidden_payrollID', 'value': para1});
                paramData.push({'name': 'isNonPayroll', 'value': 'Y'});
                paramData.push({'name': 'from_approval', 'value': 'Y'});
                paramData.push({'name': 'isForReverse', 'value': 'Y'});
                title = "Salary Processing";
                break;
            case "DC": 
                siteUrl = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>";
                paramData.push({name: 'collectionAutoId', value: para1});
                a_link = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_donor_collection'); ?>/" + para1 + '/DC';
                title = "<?php echo $this->lang->line('footer_donor_collection');?>";
                /*Donor Collection*/
                break;
            case "BBBC": 
                siteUrl = "<?php echo site_url('Buyback/load_production_report_confirmation'); ?>";
                paramData.push({name: 'batchMasterID', value: para1});
                a_link = "<?php echo site_url('Buyback/buyback_production_report'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_batchClosing'); ?>/" + para1 + '/BBBC';
                title = "Batch";
                /*Buyback Batch Note*/
                break;
            case "BBDPN": 
                siteUrl = "<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>";
                paramData.push({name: 'dispatchAutoID', value: para1});
                a_link = "<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatchNote'); ?>/" + para1 + '/BBDPN';
                title = "<?php echo $this->lang->line('footer_dispatch_note');?>";
                /*Buyback Dispatch Note*/
                break;
            case "BBGRN": 
                siteUrl = "<?php echo site_url('Buyback/load_goodReceiptNote_confirmation'); ?>";
                paramData.push({name: 'grnAutoID', value: para1});
                a_link = "<?php echo site_url('Buyback/load_goodReceiptNote_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_goodReceiptNote'); ?>/" + para1 + '/BBGRN';
                title = "<?php echo $this->lang->line('footer_good_receipt_note');?>";
                /*Buyback Goods Received Note*/
                break;
            case "BBDR": 
                siteUrl = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>";
                paramData.push({name: 'returnAutoID', value: para1});
                a_link = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatch_return'); ?>/" + para1 + '/BBDRN';
                title = "<?php echo $this->lang->line('footer_dispatch_return');?>";
                /*Buyback Dispatch Return*/
                break;
            case "BBPV": 
                siteUrl = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>";
                paramData.push({name: 'pvMasterAutoID', value: para1});
                a_link = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_paymentVoucher'); ?>/" + para1 + '/BBPV';
                title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                /*Buyback Payment Voucher*/
                break;
            case "BBRV": 
                siteUrl = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>";
                paramData.push({name: 'pvMasterAutoID', value: para1});
                a_link = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_paymentVoucher'); ?>/" + para1 + '/BBPV';
                title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                /*Buyback Receipt Voucher*/
                break;
            case "BBSV": 
                siteUrl = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>";
                paramData.push({name: 'pvMasterAutoID', value: para1});
                a_link = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_paymentVoucher'); ?>/" + para1 + '/BBPV';
                title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                /*Buyback Settlement Voucher*/
                break;
            case "SLR": // Sales Retrun
                siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>";
                paramData.push({'name': 'salesReturnAutoID', 'value': para1});
                title = "Stock Retrun";
                a_link = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + para1 + '/SLR';
                break;
            case "FA": // Fixed Asset
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>";
                paramData.push({'name': 'faID', 'value': para1});
                title = "Fixed Asset";
                a_link = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>/" + para1;
                //de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/FA';
                break;
            case "ATS": // Attendance Summary
                siteUrl = "<?php echo site_url('OverTime/fetch_over_time_template_view'); ?>";
                paramData.push({'name': 'generalOTMasterID', 'value': para1});
                title = "Attendance Summary";
                a_link = "<?php echo site_url('OverTime/load_general_ot_print'); ?>/" + para1;
                break;
            case "PRQ": // Attendance Summary
                siteUrl = "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>";
                paramData.push({'name': 'purchaseRequestID', 'value': para1});
                title = "Purchase Request";
                a_link = "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>/" + para1;
                break;
            case "MR": // Material Request
                siteUrl = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>";
                paramData.push({'name': 'mrAutoID', 'value': para1});
                title = "Material Request";
                a_link = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + para1;
                break;
            case "DO": // Delivery Order
                siteUrl = "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>";
                paramData.push({'name': 'orderAutoID', 'value': para1});
                title = "Delivery Order";
                a_link = "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>/" + para1;
                break;
            case "MRN": // Material Receipt Note
                siteUrl = "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>";
                paramData.push({'name': 'mrnAutoID', 'value': para1});
                title = "Material Receipt Note";
                a_link = "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>/" + para1;
                break;
            case "HCINV": // Invoice (Buyback)
                siteUrl = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>";
                paramData.push({'name': 'invoiceAutoID', 'value': para1});
                title = "Invoice";
                a_link = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + para1;
                break;
            case "BRC": // Bank Rec
                siteUrl = "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>";
                paramData.push({name: 'bankRecAutoID', value: para1});
                paramData.push({name: 'GLAutoID', value: para2});
                title = "<?php echo $this->lang->line('footer_bank_reconciliation');?>";
                /*Bank Reconciliation*/
                break;
            case "SUP": // Supplier Master
                siteUrl = "<?php echo site_url('Supplier/load_supplier_master_view'); ?>";
                paramData.push({name: 'supplierAutoID', value: para1});
                //paramData.push({name: 'GLAutoID', value: para2});
                title = "Supplier Master";
                a_link = "<?php echo site_url('Supplier/load_supplier_master_view'); ?>/" + para1;
                break;
            case "INV": // Supplier Master
                siteUrl = "<?php echo site_url('ItemMaster/load_Item_master_view'); ?>";
                paramData.push({name: 'itemAutoID', value: para1});
                //paramData.push({name: 'GLAutoID', value: para2});
                title = "Supplier Master";
                a_link = "<?php echo site_url('ItemMaster/load_Item_master_view'); ?>/" + para1;
                break;
            case "P":  // Project
                siteUrl = "<?php echo site_url('Boq/project_summary'); ?>";
                paramData.push({name: 'headerID', value: para1});
                title = "Project";
                break;
            case "SC":  // Sales Commission
                siteUrl = "<?php echo site_url('Sales/load_sc_conformation'); ?>";
                paramData.push({name: 'salesCommisionID', value: para1});
                title = "Sales Commission";
                break;
            case "RJV":  // Recurring Journal Voucher
                siteUrl = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>";
                paramData.push({name: 'RJVMasterAutoId', value: para1});
                title = "Recurring Journal Voucher";
                break;
            case "BD":  // Budget
                siteUrl = "";
                paramData.push();
                title = "Budget";
                break;
        
            default:
                notification('Document ID is not set .', 'w');
                return false;
        }
        paramData.push({'name': 'html', value: true});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: paramData,
            url: siteUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#reversing_approval_form')[0].reset();
                $('#reversing_approval_form').bootstrapValidator('resetForm', true);
                $('#auto_id').val(auto_id);
                $('#document_id').val(para1);
                $('#document_code').val(documentID);
                //refreshNotifications(true);
                $('#document_title').html(title);
                $('#load_approved_document').html(data);
                $('#reversing_approval_document').modal('show');
                $("#a_link").attr("href", a_link);
                $("#de_link").attr("href", de_link);
                $('.review').removeClass('hide');

                if( documentID = 'SP' ){
                    $('#paysheet-tb').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });

                }

                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function clear_all_filters(){
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
        $('#documentID').multiselect2('deselectAll', false);
        $('#documentID').multiselect2('updateButtonText');
        fetch_reversing_approval_table();
    }

    function load_split_modal(){
        fetchPage('system/approval_document/erp_split_document','Split document','Split document','','');
    }
</script>
